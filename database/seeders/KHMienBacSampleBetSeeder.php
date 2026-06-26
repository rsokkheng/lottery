<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Creates sample Cambodia Lotto (Mien Bac / Hanoi) bets for both VND and USD
 * so results and win/lose calculation can be verified after entering results.
 *
 * Usage:  php artisan db:seed --class=KHMienBacSampleBetSeeder
 *
 * Bet numbers per game type (enter any of these in the result to trigger a win):
 *   2D channel A   : 25   → matches KH_GiaiBay (4 × 2D results)
 *   2D channel B   : 47   → matches KH_B_2D
 *   2D ABCD        : 88   → matches KH_GiaiBay + KH_B_2D + KH_C_2D + KH_D_2D
 *   3D channel A   : 123  → matches KH_GiaiSau (3 × 3D results)
 *   3D ABCD        : 456  → matches KH_GiaiSau + KH_B_3D + KH_C_3D + KH_D_3D
 *   Roll (2D)      : 33   → last 2 digits of ANY result across all prize levels
 *   Roll2 (2D)     : 12   → row 1/A (all) + row 2 (all) + row 3 (all) + row 4 first
 */
class KHMienBacSampleBetSeeder extends Seeder
{
    public function run(): void
    {
        $today   = Carbon::today()->format('Y-m-d');
        $dayName = Carbon::today()->dayName;

        // Mien Bac (Hanoi) schedule for today — one HN schedule per day
        $schedule = DB::table('bet_lottery_schedules')
            ->where('draw_day', $dayName)
            ->where('region_slug', 'mien-bac')
            ->where('record_status_id', 1)
            ->first();

        if (!$schedule) {
            $this->command->warn("No Mien Bac (Hanoi) schedule found for {$dayName}. No bets inserted.");
            return;
        }

        $this->command->info("Using schedule: {$schedule->province_en} (id={$schedule->id}, {$dayName})");

        // Package configs (package_id=1)
        $pkg2D = DB::table('bet_package_configurations')
            ->where('package_id', 1)->where('bet_type', '2D')->first();
        $pkg3D = DB::table('bet_package_configurations')
            ->where('package_id', 1)->where('bet_type', '3D')->first();

        if (!$pkg2D || !$pkg3D) {
            $this->command->error('Package configs not found. Aborting.');
            return;
        }

        // Bet plans: each covers a different game type for Mien Bac
        $betPlans = [
            // 2D channel A — checks KH_GiaiBay (4 × 2D results)
            ['digit_format' => '2D', 'pkg' => $pkg2D, 'number' => '25', 'game' => 'a_amount', 'amount' => 1.00],
            // 2D channel B — checks KH_B_2D
            ['digit_format' => '2D', 'pkg' => $pkg2D, 'number' => '47', 'game' => 'b_amount', 'amount' => 1.00],
            // 2D ABCD — checks all 2D channels (KH_GiaiBay + B/C/D 2D)
            ['digit_format' => '2D', 'pkg' => $pkg2D, 'number' => '88', 'game' => 'abcd_amount', 'amount' => 1.00],
            // 3D channel A — checks KH_GiaiSau (3 × 3D results)
            ['digit_format' => '3D', 'pkg' => $pkg3D, 'number' => '123', 'game' => 'a_amount', 'amount' => 1.00],
            // 3D ABCD — checks all 3D channels (KH_GiaiSau + B/C/D 3D)
            ['digit_format' => '3D', 'pkg' => $pkg3D, 'number' => '456', 'game' => 'abcd_amount', 'amount' => 1.00],
            // Roll (2D) — last 2 digits of any result across ALL prize levels
            ['digit_format' => '2D', 'pkg' => $pkg2D, 'number' => '33',  'game' => 'roll_amount',  'amount' => 1.00],
            // Roll2 (2D) — row 1/A all + row 2 all + row 3 all + row 4 first result
            ['digit_format' => '2D', 'pkg' => $pkg2D, 'number' => '12',  'game' => 'roll2_amount', 'amount' => 1.00],
        ];

        foreach (['vnd' => 3, 'usd' => 5] as $currency => $userId) {
            $tBet     = "bet_kh_{$currency}";
            $tNum     = "bet_number_kh_{$currency}";
            $tReceipt = "bet_receipt_kh_{$currency}";
            $currencyLabel = strtoupper($currency);

            $this->command->info("Seeding {$currencyLabel} bets for user_id={$userId}…");

            // Compute receipt totals
            $totalTurnover = 0;
            $totalNet      = 0;
            $totalComm     = 0;
            foreach ($betPlans as $plan) {
                $totalTurnover += $plan['amount'];
                $net = $plan['amount'] * $plan['pkg']->rate / 100;
                $totalNet  += $net;
                $totalComm += $plan['amount'] - $net;
            }

            $receiptNo = 'KH-BAC-' . strtoupper($currency) . '-' . date('Ymd-His');
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

            foreach ($betPlans as $plan) {
                $amount = $plan['amount'];
                $rate   = $plan['pkg']->rate;
                $net    = $amount * $rate / 100;
                $comm   = $amount - $net;

                $betId = DB::table($tBet)->insertGetId([
                    'company_id'            => $schedule->company_id,
                    'bet_receipt_id'        => $receiptId,
                    'user_id'               => $userId,
                    'bet_schedule_id'       => $schedule->id,
                    'bet_package_config_id' => $plan['pkg']->id,
                    'number_format'         => $plan['number'],
                    'digit_format'          => $plan['digit_format'],
                    'total_amount'          => $amount,
                    'bet_date'              => $today . ' 00:00:00',
                    'status'                => 1,
                    'draw_time'             => $schedule->draw_time,
                    'created_by'            => $userId,
                    'record_status_id'      => 1,
                    'created_at'            => now(),
                    'updated_at'            => now(),
                ]);

                $digitLength = match($plan['digit_format']) {
                    '3D'    => '3',
                    '4D'    => '4',
                    default => '2',
                };

                $amountCols = array_fill_keys(
                    ['a_amount','b_amount','c_amount','d_amount','abcd_amount','roll_amount','roll2_amount','roll_parlay_amount'],
                    0.00
                );
                $amountCols[$plan['game']] = $amount;

                DB::table($tNum)->insert([
                    'bet_id'            => $betId,
                    'original_number'   => $plan['number'],
                    'generated_number'  => $plan['number'],
                    'digit_length'      => $digitLength,
                    'rate'              => $rate,
                    'total_amount'      => $amount,
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

                $this->command->line("  [{$currencyLabel}] #{$plan['number']} {$plan['digit_format']} {$plan['game']}={$amount}");
            }

            $this->command->info("  Receipt: {$receiptNo} | Turnover: {$totalTurnover} {$currencyLabel}");
        }

        $this->command->info('Done. Enter Hanoi lottery results to test win/lose calculation.');
        $this->command->line('');
        $this->command->line('Quick reference — enter these numbers in results to trigger wins:');
        $this->command->line('  KH_GiaiBay (1/A 2D, any of 4 results) → end with 25 or 88');
        $this->command->line('  KH_B_2D                                → end with 47 or 88');
        $this->command->line('  KH_GiaiSau (1/A 3D, any of 3 results) → end with 123 or 456');
        $this->command->line('  Any result (all levels)                → end with 33 (Roll)');
        $this->command->line('  Rows 1/A+2+3+4first                    → end with 12 (Roll2)');
    }
}
