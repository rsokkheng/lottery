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
        $this->betModel = $betModel;
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    public function getBetNumber(Request $request)
    {
        try {
            $date = $this->currentDate;
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            $user = Auth::user()??0;
            $digits = BetLotteryPackageConfiguration::query()
                ->where('package_id', $user->package_id)
                ->orderBy('id')->get(['id', 'bet_type','has_special']);
            $members = User::where('record_status_id', 1)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'staff');
                })
                ->get();
            $company_id = null;
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $digit_type = "2D";
            if ($request->has('digit_type')) {
                $digit_type = $request->get('digit_type');
            }
            $member_id = $request->get('member_id');
            $member_id = ($member_id === 'undefined' || empty($member_id)) ? null : $member_id;
            $roles = [];
            if ($user) {
                $user = User::with('roles')->find($user->id); // reload with roles
                $roles = $user->roles->pluck('name')->toArray();
            }


              // Get member list based on role
            $members = collect(); // default empty collection
            if (in_array('admin', $roles)) {
                // Admin sees users who are not admin or manager
                $members = User::with('manager') // Eager load manager relationship
                    ->whereDoesntHave('roles', function ($q) {
                        $q->whereIn('name', ['admin', 'manager']);
                    })->get();
            } elseif (in_array('manager', $roles)) {
                // Manager sees their own members (exclude admins)
                $members = User::with('manager')
                    ->where('manager_id', $user->id)
                    ->whereDoesntHave('roles', function ($q) {
                        $q->where('name', 'admin');
                    })->get();
            }

            $number = $request->number ?? null;

            $company = [
                ["label" => "All Company", "id" => null],
                ["label" => "4PM Company", "id" => 1],
                ["label" => "5PM Company", "id" => 2],
                ["label" => "6PM Company", "id" => 3],
            ];

            $data = [];
            $totalNetAmount = [
                'turnover' => 0,
                'commission'=>0,
                'net_amount'=>0,
                'win_lose' => 0
            ];
                DB::table('bet_number_kh')
                ->select(
                    'bet_number_kh.id as bet_number_id',
                    'bet_number_kh.original_number',
                    'bet_number_kh.generated_number',
                    'bet_number_kh.total_amount as number_turnover',
                    'bet_number_kh.a_amount',
                    'bet_number_kh.b_amount',
                    'bet_number_kh.c_amount',
                    'bet_number_kh.d_amount',
                    'bet_number_kh.abcd_amount',
                    'bet_number_kh.roll_amount',
                    'bet_number_kh.roll2_amount',
                    'bet_number_kh.roll_parlay_amount',
                    'bet_kh.bet_date',
                    'bet_kh.digit_format',
                    DB::raw("CASE
                                WHEN config.bet_type LIKE 'RP%' THEN COUNT(winning_records.bet_number_id)*config.price/2
                                ELSE COUNT(winning_records.bet_number_id)*config.price
                             END AS total_amount_number_win"),
                    DB::raw("TRIM(TRAILING ',' FROM CONCAT(
                                IF(bet_number_kh.a_amount > 0, 'A,', ''),
                                IF(bet_number_kh.b_amount > 0, 'B,', ''),
                                IF(bet_number_kh.c_amount > 0, 'C,', ''),
                                IF(bet_number_kh.d_amount > 0, 'D,', ''),
                                IF(bet_number_kh.abcd_amount > 0, 'ABCD,', ''),
                                IF(bet_number_kh.roll_amount > 0, 'Roll,', ''),
                                IF(bet_number_kh.roll2_amount > 0, 'Roll2,', ''),
                                IF(bet_number_kh.roll_parlay_amount > 0, 'RP,', '')
                             )) AS bet_game"),
                    DB::raw("(COALESCE(bet_number_kh.a_amount,0)
                             + COALESCE(bet_number_kh.b_amount,0)
                             + COALESCE(bet_number_kh.c_amount,0)
                             + COALESCE(bet_number_kh.d_amount,0)
                             + COALESCE(bet_number_kh.abcd_amount,0)
                             + COALESCE(bet_number_kh.roll_amount,0)
                             + COALESCE(bet_number_kh.roll2_amount,0)
                             + COALESCE(bet_number_kh.roll_parlay_amount,0)) AS get_roll_amount"),
                    DB::raw("bet_number_kh.total_amount - (bet_number_kh.total_amount * config.rate / 100) as commission"),
                    DB::raw("(bet_number_kh.total_amount * config.rate / 100) as net_amount"),
                    'config.rate',
                    'config.price',
                    'config.bet_type',
                    'schedules.province_en',
                    'schedules.code',
                    'bet_kh.company_id',
                    'bet_kh.bet_schedule_id'
                )
                ->join('bet_kh', 'bet_kh.id', '=', 'bet_number_kh.bet_id')
                ->leftJoin('bet_winning_record_kh as winning_records', 'winning_records.bet_number_id', '=', 'bet_number_kh.id')
                ->join('bet_package_configurations as config', 'config.id', '=', 'bet_kh.bet_package_config_id')
                ->join('bet_lottery_schedules as schedules', 'schedules.id', '=', 'bet_kh.bet_schedule_id')
                ->join('users', 'users.id', '=', 'bet_kh.user_id')
                ->when(in_array('manager', $roles), function ($q) use ($user) {
                    $memberIds = User::where('manager_id', $user->id)
                        ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                        ->pluck('id')->toArray();
                    $q->whereIn('user_id', $memberIds);
                })
                ->when(!in_array('admin', $roles) && !in_array('manager', $roles), fn($q) => $q->where('user_id', $user->id))
                ->when(!is_null($member_id), fn($q) => $q->where('user_id', $member_id))
                ->when($date, fn($q) => $q->whereDate('bet_date', $date))
                ->when(!is_null($digit_type), fn($q) => $q->where('bet_kh.digit_format', $digit_type))
                ->when(!is_null($company_id), fn($q) => $q->where('bet_kh.company_id', $company_id))
                ->when(!is_null($number), fn($q) => $q->where('bet_number_kh.generated_number', $number))
                ->groupBy(
                    'bet_number_kh.id',
                    'bet_number_kh.original_number',
                    'bet_number_kh.generated_number',
                    'bet_number_kh.total_amount',
                    'bet_number_kh.a_amount',
                    'bet_number_kh.b_amount',
                    'bet_number_kh.c_amount',
                    'bet_number_kh.d_amount',
                    'bet_number_kh.abcd_amount',
                    'bet_number_kh.roll_amount',
                    'bet_number_kh.roll2_amount',
                    'bet_number_kh.roll_parlay_amount',
                    'bet_kh.bet_date',
                    'bet_kh.digit_format',
                    'bet_kh.company_id',
                    'bet_kh.bet_schedule_id',
                    'config.rate',
                    'config.price',
                    'config.bet_type',
                    'schedules.province_en',
                    'schedules.code'
                )
                ->orderBy('bet_number_kh.id', 'DESC')
                ->lazy()
                ->each(function ($betNumber) use (&$data, &$totalNetAmount) {
                    $betNumber->win_lose = $betNumber->total_amount_number_win - $betNumber->net_amount;
                    $totalNetAmount['commission'] += $betNumber->commission;
                    $totalNetAmount['net_amount'] += $betNumber->net_amount;
                    $totalNetAmount['turnover'] += $betNumber->number_turnover;
                    $totalNetAmount['win_lose'] += $betNumber->win_lose;

                    if (empty($data)) {
                        $data[] = $betNumber;
                    } else {
                        $betExist = false;
                        $data = array_map(function ($item) use ($betNumber, &$betExist) {
                            $sameSlot = $item->company_id === $betNumber->company_id
                                && $item->generated_number === $betNumber->generated_number
                                && (($item->code ?? '') === 'HN') === (($betNumber->code ?? '') === 'HN');
                            if ($sameSlot && !$betExist) {
                                foreach (['a_amount','b_amount','c_amount','d_amount','abcd_amount','roll_amount','roll2_amount','roll_parlay_amount'] as $field) {
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

    function sumExistingBet(&$item, &$betNumber){
        $item->commission += $betNumber->commission;
        $item->total_amount_number_win += $betNumber->total_amount_number_win;
        $item->net_amount += $betNumber->net_amount;
        $item->win_lose += $betNumber->win_lose;
        $item->number_turnover += $betNumber->number_turnover;
        $item->get_roll_amount += $betNumber->get_roll_amount;
    }


    public function getBetAmount($a, $b, $ab, $roll7, $roll, $rollParlay)
    {
        $getAmount = 0;
        if ((float)$a){
            $getAmount = (float)$a;
        }
        if ((float)$b){
            $getAmount = (float)$b;
        }
        if ((float)$ab){
            $getAmount = (float)$ab;
        }
        if ((float)$roll7){
            $getAmount = (float)$roll7;
        }
        if ((float)$roll){
            $getAmount = (float)$roll;
        }
        if ((float)$rollParlay){
            $getAmount = (float)$rollParlay;
        }
        return $getAmount;
    }


}
