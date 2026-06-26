<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Creates sample Cambodia Lotto bets for both VND and USD so results/win-lose
 * can be verified after entering lottery results.
 *
 * Usage:  php artisan db:seed --class=KHSampleBetSeeder
 *
 * Bet numbers included (pick any when entering results to trigger a win):
 *   2D : 25, 47, 88
 *   3D : 412, 567
 *   Roll: 33 (matches all prize levels)
 */
class KHSampleBetSeeder extends Seeder
{
    public function run(): void
    {
        $today   = Carbon::today()->format('Y-m-d');
        $dayName = Carbon::today()->dayName;

        // --- resolve today's schedules (one per company) ---
        $schedules = DB::table('bet_lottery_schedules')
            ->where('draw_day', $dayName)
            ->where('record_status_id', 1)
            ->orderBy('company_id')
            ->orderBy('id')
            ->get()
            ->unique('company_id')   // one schedule per company
            ->values();

        if ($schedules->isEmpty()) {
            $this->command->warn("No lottery schedules found for {$dayName}. No bets inserted.");
            return;
        }

        // Package config IDs (package_id=1, same for both users)
        // 2D rate=76 price=75 | 3D rate=76 price=650
        $pkg2D = DB::table('bet_package_configurations')
            ->where('package_id', 1)->where('bet_type', '2D')->first();
        $pkg3D = DB::table('bet_package_configurations')
            ->where('package_id', 1)->where('bet_type', '3D')->first();

        if (!$pkg2D || !$pkg3D) {
            $this->command->error('Package configs not found. Aborting.');
            return;
        }

        foreach (['vnd' => 3, 'usd' => 5] as $currency => $userId) {
            $tBet     = "bet_kh_{$currency}";
            $tNum     = "bet_number_kh_{$currency}";
            $tReceipt = "bet_receipt_kh_{$currency}";

            $this->command->info("Seeding {$currency} bets for user_id={$userId}…");
            $this->seedCurrency($tBet, $tNum, $tReceipt, $today, $userId, $currency, $schedules, $pkg2D, $pkg3D);
        }

        $this->command->info('Done. You can now enter lottery results to test win/lose calculation.');
    }

    private function seedCurrency(
        string $tBet, string $tNum, string $tReceipt,
        string $today, int $userId, string $currency,
        $schedules, $pkg2D, $pkg3D
    ): void {
        $currencyLabel = strtoupper($currency);

        // ── Bets to insert per schedule ──────────────────────────────────────
        // Each entry: [digit_format, number, bet_type_field => amount]
        $betPlans = [
            // 2D bets
            ['digit_format' => '2D', 'pkg' => $pkg2D, 'number' => '25', 'game' => 'a_amount', 'amount' => 1.00],
            ['digit_format' => '2D', 'pkg' => $pkg2D, 'number' => '47', 'game' => 'b_amount', 'amount' => 1.00],
            ['digit_format' => '2D', 'pkg' => $pkg2D, 'number' => '88', 'game' => 'a_amount', 'amount' => 2.00],
            // 3D bets
            ['digit_format' => '3D', 'pkg' => $pkg3D, 'number' => '412', 'game' => 'abcd_amount', 'amount' => 1.00],
            ['digit_format' => '3D', 'pkg' => $pkg3D, 'number' => '567', 'game' => 'a_amount',    'amount' => 1.00],
            // Roll (2D)
            ['digit_format' => '2D', 'pkg' => $pkg2D, 'number' => '33',  'game' => 'roll_amount',  'amount' => 1.00],
        ];

        // One receipt per currency covers all schedules
        $totalTurnover = 0;
        $totalNet      = 0;
        $totalComm     = 0;

        foreach ($betPlans as $plan) {
            $totalTurnover += $plan['amount'] * count($schedules);
            $totalNet      += $plan['amount'] * count($schedules) * $plan['pkg']->rate / 100;
            $totalComm     += $plan['amount'] * count($schedules) - ($plan['amount'] * count($schedules) * $plan['pkg']->rate / 100);
        }

        $receiptNo = 'KH-' . strtoupper($currency) . '-SAMPLE-' . date('Ymd-His');

        $receiptId = DB::table($tReceipt)->insertGetId([
            'receipt_no'       => $receiptNo,
            'user_id'          => $userId,
            'date'             => $today,
            'currency'         => $currencyLabel,
            'total_amount'     => $totalTurnover,
            'commission'       => $totalComm,
            'net_amount'       => $totalNet,
            'compensate'       => 0,
            'created_by'       => $userId,
            'record_status_id' => 1,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        foreach ($schedules as $schedule) {
            foreach ($betPlans as $plan) {
                $amount  = $plan['amount'];
                $rate    = $plan['pkg']->rate;
                $net     = $amount * $rate / 100;
                $comm    = $amount - $net;

                // ── Insert bet row ────────────────────────────────────────────
                $betId = DB::table($tBet)->insertGetId([
                    'company_id'           => $schedule->company_id,
                    'bet_receipt_id'       => $receiptId,
                    'user_id'              => $userId,
                    'bet_schedule_id'      => $schedule->id,
                    'bet_package_config_id'=> $plan['pkg']->id,
                    'number_format'        => $plan['number'],
                    'digit_format'         => $plan['digit_format'],
                    'total_amount'         => $amount,
                    'bet_date'             => $today . ' 00:00:00',
                    'status'               => 1,
                    'draw_time'            => $schedule->draw_time,
                    'created_by'           => $userId,
                    'record_status_id'     => 1,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);

                // ── Determine digit_length ────────────────────────────────────
                $digitLength = match($plan['digit_format']) {
                    '2D'    => '2',
                    '3D'    => '3',
                    '4D'    => '4',
                    default => '2',
                };

                // ── Build amount columns ──────────────────────────────────────
                $amountCols = array_fill_keys(
                    ['a_amount','b_amount','c_amount','d_amount','abcd_amount','roll_amount','roll2_amount','roll_parlay_amount'],
                    0.00
                );
                $amountCols[$plan['game']] = $amount;

                // ── Insert bet_number row ─────────────────────────────────────
                DB::table($tNum)->insert([
                    'bet_id'           => $betId,
                    'original_number'  => $plan['number'],
                    'generated_number' => $plan['number'],
                    'digit_length'     => $digitLength,
                    'rate'             => $rate,
                    'total_amount'     => $amount,
                    ...$amountCols,
                    'a_check'              => 0,
                    'b_check'              => 0,
                    'c_check'              => 0,
                    'd_check'              => 0,
                    'abcd_check'           => 0,
                    'roll_check'           => 0,
                    'roll2_check'          => 0,
                    'roll_parlay_check'    => 0,
                    'created_by'           => $userId,
                    'record_status_id'     => 1,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);

                $this->command->line("  [{$currencyLabel}] #{$plan['number']} {$plan['digit_format']} {$plan['game']}={$amount} → schedule {$schedule->province_en}");
            }
        }

        $this->command->info("  Receipt: {$receiptNo} | Turnover: {$totalTurnover} {$currencyLabel}");
    }
}
