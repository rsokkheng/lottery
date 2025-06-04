<?php

namespace App\Http\Controllers;

use App\Models\BetLotteryPackageConfiguration;
use App\Models\BetUSD;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\throwException;

class BetUSDController extends Controller
{
    public $betModel;
    public $currentDate;
    public function __construct(BetUSD $betModel)
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
                DB::table('bet_number_usd')
                ->select(
                    'bet_number_usd.id as bet_number_id',
                    'bet_number_usd.original_number',
                    'bet_number_usd.generated_number',
                    'bet_number_usd.total_amount as number_turnover',
                    'bet_number_usd.a_amount',
                    'bet_number_usd.b_amount',
                    'bet_number_usd.ab_amount',
                    'bet_number_usd.roll_amount',
                    'bet_number_usd.roll7_amount',
                    'bet_number_usd.roll_parlay_amount',
                    'bet_usd.bet_date',
                    'bet_usd.digit_format',
                    DB::raw("CASE
                                WHEN config.bet_type LIKE 'RP%' THEN COUNT(winning_records.bet_number_id)*config.price/2
                                ELSE COUNT(winning_records.bet_number_id)*config.price
                             END AS total_amount_number_win"
                    ),
                    DB::raw("CASE 
                                WHEN bet_number_usd.a_amount > 0 THEN 'A'
                                WHEN bet_number_usd.b_amount > 0 THEN 'B'
                                WHEN bet_number_usd.ab_amount > 0 THEN 'AB'
                                WHEN bet_number_usd.roll_amount > 0 THEN 'Roll'
                                WHEN bet_number_usd.roll7_amount > 0 THEN 'Roll7'
                                ELSE 'Roll Parlay'
                             END AS bet_game"
                    ),
                    DB::raw("CASE 
                                WHEN bet_number_usd.a_amount > 0 THEN bet_number_usd.a_amount
                                WHEN bet_number_usd.b_amount > 0 THEN bet_number_usd.b_amount
                                WHEN bet_number_usd.ab_amount > 0 THEN bet_number_usd.ab_amount
                                WHEN bet_number_usd.roll_amount > 0 THEN bet_number_usd.roll_amount
                                WHEN bet_number_usd.roll7_amount > 0 THEN bet_number_usd.roll7_amount
                                ELSE bet_number_usd.roll_parlay_amount
                             END AS get_roll_amount"
                    ),
                    DB::raw("bet_number_usd.total_amount - (bet_number_usd.total_amount * config.rate /100) as commission"),
                    DB::raw("(bet_number_usd.total_amount * config.rate /100) as net_amount"),
                    'config.rate',
                    'config.price',
                    'config.bet_type',
                    'schedules.province_en',
                    'bet_usd.company_id',
                    'bet_usd.bet_schedule_id'
                )
                ->join('bet_usd','bet_usd.id','=', 'bet_number_usd.bet_id')
                ->leftJoin('bet_winning_records as winning_records','winning_records.bet_number_id','=', 'bet_number_usd.id')
                ->join('bet_package_configurations as config','config.id','=', 'bet_usd.bet_package_config_id')
                ->join('bet_lottery_schedules as schedules','schedules.id','=', 'bet_usd.bet_schedule_id')
                ->join('users','users.id','=', 'bet_usd.user_id')
                ->when(in_array('manager', $roles), function ($q) use ($user) {
                    // Get all users under this manager
                    $memberIds = User::where('manager_id', $user->id)
                                    ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                                    ->pluck('id')
                                    ->toArray();
                    $q->whereIn('user_id', $memberIds);
                })->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->when(!is_null($member_id), function ($q) use ($member_id) {
                    $q->where('user_id', $member_id);
                })->when($date, function ($q) use ($date) {
                    $q->whereDate('bet_date', $date);
                })->when(!is_null($digit_type), function ($q) use ($digit_type) {
                    $q->where('bet_usd.digit_format', $digit_type);
                })->when(!is_null($company_id), function ($q) use ($company_id) {
                    $q->where('bet_usd.company_id', $company_id);
                })->when(!is_null($number), function ($q) use ($number) {
                    $q->where('bet_number_usd.generated_number', $number);
                })
                ->groupBy('winning_records.bet_number_id')
                ->groupBy('bet_number_usd.id')
                ->groupBy('bet_number_usd.a_amount')
                ->groupBy('bet_number_usd.b_amount')
                ->groupBy('bet_number_usd.ab_amount')
                ->groupBy('bet_number_usd.roll_amount')
                ->groupBy('bet_number_usd.roll7_amount')
                ->groupBy('bet_number_usd.roll_parlay_amount')
                ->groupBy('bet_number_usd.original_number')
                ->groupBy('bet_number_usd.generated_number')
                    ->groupBy('bet_usd.bet_date')
                    ->groupBy('bet_usd.total_amount')
                ->groupBy('bet_usd.digit_format')
                ->groupBy('bet_usd.company_id')
                ->groupBy('bet_usd.bet_schedule_id')
                ->groupBy('config.rate')
                ->groupBy('config.price')
                ->groupBy('config.bet_type')
                ->groupBy('schedules.province_en')
                ->groupBy('bet_number_usd.total_amount')
                    ->orderBy('get_roll_amount','DESC')
                ->lazy()
                ->each(function ($betNumber) use (&$data, &$totalNetAmount){
                    $betNumber->win_lose = $betNumber->total_amount_number_win - $betNumber->net_amount;
                    $totalNetAmount['commission'] += $betNumber->commission;
                    $totalNetAmount['net_amount'] += $betNumber->net_amount;
                    $totalNetAmount['turnover'] += $betNumber->number_turnover;
                    $totalNetAmount['win_lose'] += $betNumber->win_lose;
                    if(empty($data)){
                        $data[] = $betNumber;
                    }else{
                        $betExist = false;
                        $data = array_map(function ($item) use ($betNumber, &$betExist){
                            $status = $item->company_id === $betNumber->company_id &&
                            $item->bet_schedule_id === $betNumber->bet_schedule_id &&
                            $item->generated_number === $betNumber->generated_number;
                            if($status) {
                                if ((float)$item->a_amount && (float)$betNumber->a_amount) {
                                    $item->a_amount = (float)$item->a_amount+(float)$betNumber->a_amount;
                                    $this->sumExistingBet($item, $betNumber);
                                    $betExist = true;
                                }
                                if((float)$item->b_amount && (float)$betNumber->b_amount){
                                    $item->b_amount = (float)$item->b_amount + (float)$betNumber->b_amount;
                                    $this->sumExistingBet($item, $betNumber);
                                    $betExist = true;
                                }
                                if((float)$item->ab_amount && (float)$betNumber->ab_amount){
                                    $item->ab_amount = (float)$item->ab_amount + (float)$betNumber->ab_amount;
                                    $this->sumExistingBet($item, $betNumber);
                                    $betExist = true;
                                }
                                if((float)$item->roll_amount && (float)$betNumber->roll_amount){
                                    $item->roll_amount = (float)$item->roll_amount + (float)$betNumber->roll_amount;
                                    $this->sumExistingBet($item, $betNumber);
                                    $betExist = true;
                                }
                                if((float)$item->roll7_amount && (float)$betNumber->roll7_amount){
                                    $item->roll7_amount = (float)$item->roll7_amount + (float)$betNumber->roll7_amount;
                                    $this->sumExistingBet($item, $betNumber);
                                    $betExist = true;
                                }
                                if((float)$item->roll_parlay_amount && (float)$betNumber->roll_parlay_amount){
                                    $item->roll_parlay_amount = (float)$item->roll_parlay_amount + (float)$betNumber->roll_parlay_amount;
                                    $this->sumExistingBet($item, $betNumber);
                                    $betExist = true;
                                }
                            }
                            return $item;
                        }, $data);
                        if(!$betExist){
                            $data[] = $betNumber;
                        }

                    }
                });

            return view('bet_usd.bet-number', compact('data','totalNetAmount','date','company','company_id','digits','number','members','member_id','digit_type'));
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
