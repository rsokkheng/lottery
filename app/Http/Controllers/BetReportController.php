<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bet;
use App\Models\User;
use App\Models\BetReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\throwException;

class BetReportController extends Controller
{
    public BetReceipt $model;
    public Bet $betModel;
    public $currentDate;

    public function __construct(BetReceipt $model, Bet $betModel)
    {
        $this->model = $model;
        $this->betModel = $betModel;
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    public function getSummaryReport(Request $request)
    {
        try {

            $start_date = $request->get('start_date') ?? null;
            $end_date = $request->get('end_date') ?? null;
            $user = Auth::user() ?? 0;
            if ($user) {
                $user = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
            }
            
            $date = $this->currentDate;
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            
            $data = DB::table(DB::raw('(
                SELECT 
                    be.bet_receipt_id,
                    se.draw_day 
                FROM bets be
                INNER JOIN bet_lottery_schedules se  ON se.id = be.bet_schedule_id
                GROUP BY be.bet_receipt_id, se.draw_day
                 ) as bee'))
                ->join('bet_receipts as re', 're.id', '=', 'bee.bet_receipt_id')
                ->selectRaw('
                    DATE(re.date) AS date,
                    bee.draw_day,
                    COUNT(re.id) AS total,
                    SUM(re.total_amount) AS Turnover,
                    SUM(re.commission) AS Commission,
                    SUM(re.net_amount) AS NetAmount,
                    SUM(re.compensate) AS Compensate
                ')->when(in_array('manager', $roles), function ($q) use ($user) {
                    // Get all users under this manager
                    $memberIds = User::where('manager_id', $user->id)
                                    ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                                    ->pluck('id')
                                    ->toArray();
                    $q->whereIn('user_id', $memberIds);
                })->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
                    // Apply filter for date range
                    $q->whereBetween('re.date', [
                        Carbon::parse($start_date)->startOfDay()->format('Y-m-d H:i:s'),
                        Carbon::parse($end_date)->endOfDay()->format('Y-m-d H:i:s')
                    ]);
                })->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->when($date && !$start_date && !$end_date, function ($q) use ($date) {
                    // Apply filter for specific date
                    $q->whereDate('re.date', '=', Carbon::parse($date)->format('Y-m-d'));
                })
              
                ->groupBy(DB::raw('DATE(re.date), bee.draw_day'))
                ->get();
           
            return view('reports.summary', compact('data', 'date'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
    public function getDailyReport(Request $request)
    {
        try {

            $date = $request->get('date') ?? null;
            $user = Auth::user() ?? 0;
            if ($user) {
                $user = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
            }
            $date = $this->currentDate;
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            $company_id = null;
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $company = [
                [
                    "label" => "All Company",
                    "id" => 0,
                ],
                [
                    "label" => "4PM Company",
                    "id" => 1,
                ],
                [
                    "label" => "5PM Company",
                    "id" => 2,
                ],
                [
                    "label" => "6PM Company",
                    "id" => 3,
                ]
            ];
    
                $data = DB::table(DB::raw('(
                    SELECT 
                        be.bet_receipt_id,
                        se.draw_day,
                        user.name
                    FROM bets be
                    INNER JOIN bet_lottery_schedules se ON se.id = be.bet_schedule_id
                    INNER JOIN users as user ON user.id = be.user_id
                    GROUP BY be.bet_receipt_id, se.draw_day, user.name
                ) as bee'))
                ->join('bet_receipts as re', 're.id', '=', 'bee.bet_receipt_id')
                ->selectRaw('
                    DATE(re.date) AS date,
                    bee.draw_day,
                    bee.name,
                    COUNT(re.id) AS total,
                    SUM(re.total_amount) AS Turnover,
                    SUM(re.commission) AS Commission,
                    SUM(re.net_amount) AS NetAmount,
                    SUM(re.compensate) AS Compensate
                ')
                ->when(in_array('manager', $roles), function ($q) use ($user) {
                    $memberIds = User::where('manager_id', $user->id)
                                    ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                                    ->pluck('id')
                                    ->toArray();
                    $q->whereIn('re.user_id', $memberIds);
                })
                ->when($date, function ($q) use ($date) {
                    $q->whereDate('re.date', '=', Carbon::parse($date)->format('Y-m-d'));
                })
                ->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                    $q->where('re.user_id', $user->id);
                })
                ->groupBy(DB::raw('DATE(re.date), bee.draw_day, bee.name'))
                ->get(); 
            
            
            return view('reports.daily', compact('data', 'date','company','company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
}
