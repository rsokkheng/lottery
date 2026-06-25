<?php

namespace App\Http\Controllers;

use App\Models\BetLotteryPackageConfiguration;
use App\Models\BetKH;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\throwException;

class BetKHController extends Controller
{
    public $betModel;
    public $currentDate;

    public function __construct(BetKH $betModel)
    {
        $this->betModel    = $betModel;
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    public function getBetNumber(Request $request)
    {
        try {
            $cur    = strtolower(session('currency', 'VND'));
            $tBet   = "bet_kh_{$cur}";
            $tNum   = "bet_number_kh_{$cur}";
            $tWinRec = "bet_winning_record_kh_{$cur}";

            $date = $this->currentDate;
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            $user = Auth::user() ?? 0;
            $digitsQuery = BetLotteryPackageConfiguration::query()->orderBy('bet_type');
            if ($user->package_id) {
                $digitsQuery->where('package_id', $user->package_id);
            }
            $digits = $digitsQuery->select(['bet_type', 'has_special'])->distinct()->get();

            $company_id = null;
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $digit_type = ($request->get('digit_type', '') !== '') ? $request->get('digit_type') : null;
            $member_id  = $request->get('member_id');
            $member_id  = ($member_id === 'undefined' || empty($member_id)) ? null : $member_id;
            $number     = $request->number ?? null;

            $roles = [];
            if ($user) {
                $user  = User::with('roles')->find($user->id);
                $roles = $user->roles->pluck('name')->toArray();
            }

            $members = collect();
            if (in_array('admin', $roles)) {
                $members = User::with('manager')
                    ->whereDoesntHave('roles', function ($q) {
                        $q->whereIn('name', ['admin', 'master', 'agent']);
                    })->get();
            } elseif (in_array('agent', $roles)) {
                $members = User::with('manager')
                    ->where('manager_id', $user->id)
                    ->whereDoesntHave('roles', function ($q) {
                        $q->where('name', 'admin');
                    })->get();
            }

            $company = [
                ["label" => "All Company", "id" => null],
                ["label" => "4PM Company", "id" => 1],
                ["label" => "5PM Company", "id" => 2],
                ["label" => "6PM Company", "id" => 3],
            ];

            $data         = [];
            $totalNetAmount = [
                'turnover'   => 0,
                'commission' => 0,
                'net_amount' => 0,
                'win_lose'   => 0,
            ];

            DB::table($tNum)
                ->select(
                    "{$tNum}.id as bet_number_id",
                    "{$tNum}.original_number",
                    "{$tNum}.generated_number",
                    "{$tNum}.total_amount as number_turnover",
                    "{$tNum}.a_amount",
                    "{$tNum}.b_amount",
                    "{$tNum}.c_amount",
                    "{$tNum}.d_amount",
                    "{$tNum}.abcd_amount",
                    "{$tNum}.roll_amount",
                    "{$tNum}.roll2_amount",
                    "{$tNum}.roll_parlay_amount",
                    "{$tBet}.bet_date",
                    "{$tBet}.digit_format",
                    DB::raw("CASE
                                WHEN config.bet_type LIKE 'RP%' THEN COUNT(winning_records.bet_number_id)*config.price/2
                                ELSE COUNT(winning_records.bet_number_id)*config.price
                             END AS total_amount_number_win"),
                    DB::raw("TRIM(TRAILING ',' FROM CONCAT(
                                IF({$tNum}.a_amount > 0, 'A,', ''),
                                IF({$tNum}.b_amount > 0, 'B,', ''),
                                IF({$tNum}.c_amount > 0, 'C,', ''),
                                IF({$tNum}.d_amount > 0, 'D,', ''),
                                IF({$tNum}.abcd_amount > 0, 'ABCD,', ''),
                                IF({$tNum}.roll_amount > 0, 'Roll,', ''),
                                IF({$tNum}.roll2_amount > 0, 'Roll2,', ''),
                                IF({$tNum}.roll_parlay_amount > 0, 'RP,', '')
                             )) AS bet_game"),
                    DB::raw("(COALESCE({$tNum}.a_amount,0)
                             + COALESCE({$tNum}.b_amount,0)
                             + COALESCE({$tNum}.c_amount,0)
                             + COALESCE({$tNum}.d_amount,0)
                             + COALESCE({$tNum}.abcd_amount,0)
                             + COALESCE({$tNum}.roll_amount,0)
                             + COALESCE({$tNum}.roll2_amount,0)
                             + COALESCE({$tNum}.roll_parlay_amount,0)) AS get_roll_amount"),
                    DB::raw("{$tNum}.total_amount - ({$tNum}.total_amount * config.rate / 100) as commission"),
                    DB::raw("({$tNum}.total_amount * config.rate / 100) as net_amount"),
                    'config.rate',
                    'config.price',
                    'config.bet_type',
                    'schedules.province_en',
                    'schedules.code',
                    "{$tBet}.company_id",
                    "{$tBet}.bet_schedule_id"
                )
                ->join($tBet, "{$tBet}.id", '=', "{$tNum}.bet_id")
                ->leftJoin("{$tWinRec} as winning_records", 'winning_records.bet_number_id', '=', "{$tNum}.id")
                ->join('bet_package_configurations as config', 'config.id', '=', "{$tBet}.bet_package_config_id")
                ->join('bet_lottery_schedules as schedules', 'schedules.id', '=', "{$tBet}.bet_schedule_id")
                ->join('users', 'users.id', '=', "{$tBet}.user_id")
                ->when(in_array('agent', $roles), function ($q) use ($user) {
                    $memberIds = User::where('manager_id', $user->id)
                        ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                        ->pluck('id')->toArray();
                    $q->whereIn('user_id', $memberIds);
                })
                ->when(!in_array('admin', $roles) && !in_array('agent', $roles), fn($q) => $q->where('user_id', $user->id))
                ->when(!is_null($member_id), fn($q) => $q->where('user_id', $member_id))
                ->when($date, fn($q) => $q->whereDate("{$tBet}.bet_date", $date))
                ->when(!is_null($digit_type), fn($q) => $q->where("{$tBet}.digit_format", $digit_type))
                ->when(!is_null($company_id), fn($q) => $q->where("{$tBet}.company_id", $company_id))
                ->when(!is_null($number), fn($q) => $q->where("{$tNum}.generated_number", $number))
                ->groupBy(
                    "{$tNum}.id",
                    "{$tNum}.original_number",
                    "{$tNum}.generated_number",
                    "{$tNum}.total_amount",
                    "{$tNum}.a_amount",
                    "{$tNum}.b_amount",
                    "{$tNum}.c_amount",
                    "{$tNum}.d_amount",
                    "{$tNum}.abcd_amount",
                    "{$tNum}.roll_amount",
                    "{$tNum}.roll2_amount",
                    "{$tNum}.roll_parlay_amount",
                    "{$tBet}.bet_date",
                    "{$tBet}.digit_format",
                    "{$tBet}.company_id",
                    "{$tBet}.bet_schedule_id",
                    'config.rate',
                    'config.price',
                    'config.bet_type',
                    'schedules.province_en',
                    'schedules.code'
                )
                ->orderBy("{$tNum}.id", 'DESC')
                ->lazy()
                ->each(function ($betNumber) use (&$data, &$totalNetAmount) {
                    $betNumber->win_lose = $betNumber->total_amount_number_win - $betNumber->net_amount;
                    $totalNetAmount['commission'] += $betNumber->commission;
                    $totalNetAmount['net_amount'] += $betNumber->net_amount;
                    $totalNetAmount['turnover']   += $betNumber->number_turnover;
                    $totalNetAmount['win_lose']   += $betNumber->win_lose;

                    if (empty($data)) {
                        $data[] = $betNumber;
                    } else {
                        $betExist = false;
                        $data = array_map(function ($item) use ($betNumber, &$betExist) {
                            $sameSlot = $item->company_id === $betNumber->company_id
                                && $item->generated_number === $betNumber->generated_number
                                && (($item->code ?? '') === 'HN') === (($betNumber->code ?? '') === 'HN');
                            if ($sameSlot && !$betExist) {
                                foreach (['a_amount', 'b_amount', 'c_amount', 'd_amount', 'abcd_amount', 'roll_amount', 'roll2_amount', 'roll_parlay_amount'] as $field) {
                                    $item->$field = (float)$item->$field + (float)$betNumber->$field;
                                }
                                $this->sumExistingBet($item, $betNumber);
                                $betExist = true;
                            }
                            return $item;
                        }, $data);
                        if (!$betExist) {
                            $data[] = $betNumber;
                        }
                    }
                });

            usort($data, function ($a, $b) {
                $digitCmp = strcmp($a->digit_format ?? '', $b->digit_format ?? '');
                if ($digitCmp !== 0) return $digitCmp;
                return (float)($b->get_roll_amount ?? 0) <=> (float)($a->get_roll_amount ?? 0);
            });

            return view('bet_kh.bet-number', compact('data', 'totalNetAmount', 'date', 'company', 'company_id', 'digits', 'number', 'members', 'member_id', 'digit_type', 'roles'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    function sumExistingBet(&$item, &$betNumber)
    {
        $item->commission              += $betNumber->commission;
        $item->total_amount_number_win += $betNumber->total_amount_number_win;
        $item->net_amount              += $betNumber->net_amount;
        $item->win_lose                += $betNumber->win_lose;
        $item->number_turnover         += $betNumber->number_turnover;
        $item->get_roll_amount         += $betNumber->get_roll_amount;
    }

    public function getBetAmount($a, $b, $ab, $roll7, $roll, $rollParlay)
    {
        $getAmount = 0;
        if ((float)$a)         { $getAmount = (float)$a; }
        if ((float)$b)         { $getAmount = (float)$b; }
        if ((float)$ab)        { $getAmount = (float)$ab; }
        if ((float)$roll7)     { $getAmount = (float)$roll7; }
        if ((float)$roll)      { $getAmount = (float)$roll; }
        if ((float)$rollParlay){ $getAmount = (float)$rollParlay; }
        return $getAmount;
    }
}
