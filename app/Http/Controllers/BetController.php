<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\BetLotteryPackageConfiguration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\throwException;

class BetController extends Controller
{
    public $betModel;
    public $currentDate;
    public function __construct(Bet $betModel)
    {
        $this->betModel = $betModel;
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    public function getBetNumberOld(Request $request)
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
            if ($member_id === 'undefined' || empty($member_id)) {
                $member_id = null;
            }
            $roles = [];
            if ($user) {
                $user = User::find($user->id); // If needed to reload relations
                $roles = $user->roles->pluck('name')->toArray();
            }
            $number = $request->number ?? null;

            $company = [
                ["label" => "All Company", "id" => null],
                ["label" => "4PM Company", "id" => 1],
                ["label" => "5PM Company", "id" => 2],
                ["label" => "6PM Company", "id" => 3],
            ];
            $data =$this->betModel
                ->with([
                'beReceipt',
                'user',
                    'betNumber'=> function ($q) {
                        $q->orderBy('id');
                    },
                    'betNumber.betNumberWin',
                'bePackageConfig',
                'betLotterySchedule'
            ])->when(!in_array('admin', $roles) && !in_array('manager', $roles),
                function ($q) use ($member_id, $user) {
                    $q->where('user_id', $member_id ?? $user->id);
                }
            )->when(
                in_array('admin', $roles) || in_array('manager', $roles),
                function ($q) use ($member_id) {
                    if (!is_null($member_id)) {
                        $q->where('user_id', $member_id);
                    }
                }
            )->when(!is_null($date), function ($q) use ($date) {
                $q->where('bet_date', '>=', Carbon::parse($date)->startOfDay())
                  ->where('bet_date', '<=', Carbon::parse($date)->endOfDay());
            })->when(!is_null($digit_type), function ($q) use ($digit_type) {
                $q->where('digit_format', $digit_type);
            })->when(!is_null($company_id), function ($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })->when(!is_null($number), function ($q) use ($number) {
                $q->whereHas('betNumber', function ($query) use ($number) {
                    $query->where('generated_number', $number);
                });
            })
//                ->where(function ($q){
//                    $q->where('bet_date','2025-05-10');
////                        ->whereIn('bet_receipt_id',[115,116,117]);
//                })
                ->orderBy('company_id')
                ->orderBy('bet_schedule_id')
                ->orderBy('number_format')
//                ->orderBy('bet_receipt_id')
//                ->orderBy('total_amount','DESC')
                ->get();
            return view('bet.bet-number', compact('data', 'date','company','company_id','digits','number','members','member_id','digit_type'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
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
            if ($member_id === 'undefined' || empty($member_id)) {
                $member_id = null;
            }
            $roles = [];
            if ($user) {
                $user = User::find($user->id); // If needed to reload relations
                $roles = $user->roles->pluck('name')->toArray();
            }
            $number = $request->number ?? null;

            $company = [
                ["label" => "All Company", "id" => null],
                ["label" => "4PM Company", "id" => 1],
                ["label" => "5PM Company", "id" => 2],
                ["label" => "6PM Company", "id" => 3],
            ];

//            $data =$this->betModel
//                ->with([
//                    'beReceipt',
//                    'user',
//                    'betNumber'=> function ($q) {
//                        $q->orderBy('id');
//                    },
//                    'betNumber.betNumberWin',
//                    'bePackageConfig',
//                    'betLotterySchedule'
//                ])->when(!in_array('admin', $roles) && !in_array('manager', $roles),
//                    function ($q) use ($member_id, $user) {
//                        $q->where('user_id', $member_id ?? $user->id);
//                    }
//                )->when(
//                    in_array('admin', $roles) || in_array('manager', $roles),
//                    function ($q) use ($member_id) {
//                        if (!is_null($member_id)) {
//                            $q->where('user_id', $member_id);
//                        }
//                    }
//                )->when(!is_null($date), function ($q) use ($date) {
//                    $q->where('bet_date', '>=', Carbon::parse($date)->startOfDay())
//                        ->where('bet_date', '<=', Carbon::parse($date)->endOfDay());
//                })->when(!is_null($digit_type), function ($q) use ($digit_type) {
//                    $q->where('digit_format', $digit_type);
//                })->when(!is_null($company_id), function ($q) use ($company_id) {
//                    $q->where('company_id', $company_id);
//                })->when(!is_null($number), function ($q) use ($number) {
//                    $q->whereHas('betNumber', function ($query) use ($number) {
//                        $query->where('generated_number', $number);
//                    });
//                })
//                ->where(function ($q){
//                    $q->where('bet_date','2025-05-10');
////                        ->whereIn('bet_receipt_id',[115,116,117]);
//                })
//                ->orderBy('company_id')
//                ->orderBy('bet_schedule_id')
//                ->orderBy('number_format')
//                ->get();

            $data = DB::table('bet_numbers')
                ->select(
                    'bet_numbers.id as bet_number_id',
                    'bet_numbers.original_number',
                    'bet_numbers.generated_number',
                    'bet_numbers.total_amount as number_turnover',
                    'bet_numbers.a_amount',
                    'bet_numbers.b_amount',
                    'bet_numbers.ab_amount',
                    'bet_numbers.roll_amount',
                    'bet_numbers.roll7_amount',
                    'bet_numbers.roll_parlay_amount',
                    'bets.bet_date',
                    'bets.digit_format',
                    DB::raw("CASE 
                                WHEN config.bet_type LIKE 'RP%' THEN COUNT(winning_records.bet_number_id)*config.price/2
                                ELSE COUNT(winning_records.bet_number_id)*config.price
                             END AS total_amount_number_win"
                    ),
                    'config.rate',
                    'config.price',
                    'config.bet_type',
                    'schedules.province_en',
                    'bets.company_id',
                    'bets.bet_schedule_id'
                )
                ->join('bets','bets.id','=', 'bet_numbers.bet_id')
                ->leftJoin('bet_winning_records as winning_records','winning_records.bet_number_id','=', 'bet_numbers.id')
                ->join('bet_package_configurations as config','config.id','=', 'bets.bet_package_config_id')
                ->join('bet_lottery_schedules as schedules','schedules.id','=', 'bets.bet_schedule_id')
                ->join('users','users.id','=', 'bets.user_id')
                ->where(function ($q){
                    $q->where('bet_date','2025-05-10')
//                        ->whereIn('bet_receipt_id',[114,115,116]);
                    ->whereIn('bet_receipt_id',[117]);
                })
                ->groupBy('winning_records.bet_number_id')
                ->groupBy('bet_numbers.id')
                ->groupBy('bet_numbers.a_amount')
                ->groupBy('bet_numbers.b_amount')
                ->groupBy('bet_numbers.ab_amount')
                ->groupBy('bet_numbers.roll_amount')
                ->groupBy('bet_numbers.roll7_amount')
                ->groupBy('bet_numbers.roll_parlay_amount')
                ->groupBy('bet_numbers.original_number')
                ->groupBy('bet_numbers.generated_number')
                ->groupBy('bets.bet_date')
                ->groupBy('bets.digit_format')
                ->groupBy('bets.company_id')
                ->groupBy('bets.bet_schedule_id')
                ->groupBy('config.rate')
                ->groupBy('config.price')
                ->groupBy('config.bet_type')
                ->groupBy('schedules.province_en')
                ->groupBy('bet_numbers.total_amount')
                ->orderBy('bets.company_id')
                ->orderBy('bets.bet_schedule_id')
                ->orderBy('bet_numbers.generated_number')
                ->orderByRaw("
                    CASE 
                        WHEN bet_numbers.a_amount IS NOT NULL THEN bet_numbers.a_amount
                        WHEN bet_numbers.b_amount IS NOT NULL THEN bet_numbers.b_amount
                        WHEN bet_numbers.ab_amount IS NOT NULL THEN bet_numbers.ab_amount
                        WHEN bet_numbers.roll_amount IS NOT NULL THEN bet_numbers.roll_amount
                        WHEN bet_numbers.roll7_amount IS NOT NULL THEN bet_numbers.roll7_amount
                        ELSE bet_numbers.roll_parlay_amount
                    END ASC
                ")
                ->get();
            dump($data);
//            return $data;

            return view('bet.bet-number', compact('data', 'date','company','company_id','digits','number','members','member_id','digit_type'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
}
