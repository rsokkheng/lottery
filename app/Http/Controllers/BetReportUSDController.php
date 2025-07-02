<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BetUSD;
use App\Models\User;
use App\Models\BetReceiptUSD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\throwException;

class BetReportUSDController extends Controller
{
    public BetReceiptUSD $model;
    public BetUSD $betModel;
    public $currentDate;

    public function __construct(BetReceiptUSD $model, BetUSD $betModel)
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
                FROM bet_usd be
                INNER JOIN bet_lottery_schedules se  ON se.id = be.bet_schedule_id
                GROUP BY be.bet_receipt_id, se.draw_day
                 ) as bee'))
                ->join('bet_receipt_usd as re', 're.id', '=', 'bee.bet_receipt_id')
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

            return view('report_usd.summary', compact('data', 'date'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getDailyReport(Request $request)
    {
        try {
            $company = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"]
            ];

            $date = $this->currentDate;
            $company_id = null;

            $user = Auth::user() ?? 0;
            if ($user) {
                $user = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
            }

            if ($request->has('date')) {
                $date = $request->get('date');

            }
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $data = DB::table('bet_usd')
            ->select(
                'users.username AS account',
                'users.id AS user_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('COALESCE(SUM(bet_winning_usd.win_amount), 0) AS Compensate'),
                DB::raw('DATE(bet_usd.bet_date) AS bet_date'),
                DB::raw('MAX(schedule.draw_day) AS draw_day')
            )
            ->leftJoin('bet_winning_usd', 'bet_winning_usd.bet_id', '=', 'bet_usd.id')
            ->join('users', 'users.id', '=', 'bet_usd.user_id')
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
            ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', 'bet_usd.bet_schedule_id')
            ->when(in_array('manager', $roles), function ($q) use ($user) {
                $memberIds = User::where('manager_id', $user->id)
                    ->whereDoesntHave('roles', fn ($query) => $query->where('name', 'admin'))
                    ->pluck('id')
                    ->toArray();
                $q->whereIn('bet_usd.user_id', $memberIds);
            })
            ->when($date, function ($q) use ($date) {
                $q->whereDate('bet_usd.bet_date', '=', Carbon::parse($date)->format('Y-m-d'));
            })
            ->when($company_id > 0, function ($q) use ($company_id) {
                $q->where('bet_usd.company_id', $company_id);
            })
            ->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                $q->where('bet_usd.user_id', $user->id);
            })
            ->groupBy(
                'bet_usd.user_id',
                'users.username',
                DB::raw('DATE(bet_usd.bet_date)')
            )
            ->orderByRaw('COUNT(DISTINCT bet_usd.bet_receipt_id) DESC')
            ->get();
            return view('report_usd.daily', compact('data', 'date', 'company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
    public function getDailyReportManager(Request $request){
        try {
            $company = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"]
            ];

            $date = $this->currentDate;
            $company_id = null;

            $user = Auth::user() ?? 0;
            if ($user) {
                $user = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
            }

            if ($request->has('date')) {
                $date = $request->get('date');

            }
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $data = DB::table('bet_usd')
            ->select(
                'manag.username AS account',
                'users.manager_id AS manager_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('COALESCE(SUM(bet_winning_usd.win_amount), 0) AS Compensate'),
                DB::raw('DATE(bet_usd.bet_date) AS bet_date'),
                DB::raw('MAX(schedule.draw_day) AS draw_day')
            )
            ->leftJoin('bet_winning_usd', 'bet_winning_usd.bet_id', '=', 'bet_usd.id')
            ->join('users', 'users.id', '=', 'bet_usd.user_id')
            ->join('users as manag', 'users.manager_id', '=', 'manag.id')
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
            ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', 'bet_usd.bet_schedule_id')
            ->when($date, function ($q) use ($date) {
                $q->whereDate('bet_usd.bet_date', '=', Carbon::parse($date)->format('Y-m-d'));
            })
            ->when($company_id > 0, function ($q) use ($company_id) {
                $q->where('bet_usd.company_id', $company_id);
            })
            ->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                $q->where('bet_usd.user_id', $user->id);
            })
            ->groupBy(
                'users.manager_id',
                'manag.username',
                DB::raw('DATE(bet_usd.bet_date)')
            )
            ->orderByRaw('COUNT(DISTINCT bet_usd.bet_receipt_id) DESC')
            ->get();
            return view('report_usd.manager-daily', compact('data', 'date', 'company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
    public function getDailyReportUSD(Request $request){
        try {
            $company = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"]
            ];

            $date = $this->currentDate;
            $company_id = null;

            $user = Auth::user() ?? 0;
            if ($user) {
                $user = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
            }

            if ($request->has('date')) {
                $date = $request->get('date');

            }
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $data = DB::table('bet_usd')
            ->select(
                'users.username AS account',
                'users.id AS user_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('COALESCE(SUM(bet_winning_usd.win_amount), 0) AS Compensate'),
                DB::raw('DATE(bet_usd.bet_date) AS bet_date'),
                DB::raw('MAX(schedule.draw_day) AS draw_day')
            )
            ->leftJoin('bet_winning_usd', 'bet_winning_usd.bet_id', '=', 'bet_usd.id')
            ->join('users', 'users.id', '=', 'bet_usd.user_id')
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
            ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', 'bet_usd.bet_schedule_id')
            ->when(in_array('manager', $roles), function ($q) use ($user) {
                $memberIds = User::where('manager_id', $user->id)
                    ->whereDoesntHave('roles', fn ($query) => $query->where('name', 'admin'))
                    ->pluck('id')
                    ->toArray();
                $q->whereIn('bet_usd.user_id', $memberIds);
            })
            ->when($date, function ($q) use ($date) {
                $q->whereDate('bet_usd.bet_date', '=', Carbon::parse($date)->format('Y-m-d'));
            })
            ->when($company_id > 0, function ($q) use ($company_id) {
                $q->where('bet_usd.company_id', $company_id);
            })
            ->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                $q->where('bet_usd.user_id', $user->id);
            })
            ->groupBy(
                'bet_usd.user_id',
                'users.username',
                DB::raw('DATE(bet_usd.bet_date)')
            )
            ->orderByRaw('COUNT(DISTINCT bet_usd.bet_receipt_id) DESC')
            ->get();
           
            return view('admin.report.daily-usd', compact('data', 'date', 'company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
    public function getDailyReportMeberAgent(Request $request)
    {
        try {
            $company = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"]
            ];

            $date = $this->currentDate;
            $company_id = null;
            $user = Auth::user() ?? 0;
            if ($user) {
                $user = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
            }
            $memberId = $request->id;
            $memberIds = User::where('manager_id', $memberId)->pluck('id')->toArray();
            if ($request->has('date')) {
                $date = $request->get('date');

            }
            $managerName = User::find($memberId );
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $data = DB::table('bet_usd')
            ->select(
                'users.username AS account',
                'users.id AS user_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('COALESCE(SUM(bet_winning_usd.win_amount), 0) AS Compensate'),
                DB::raw('DATE(bet_usd.bet_date) AS bet_date'),
                DB::raw('MAX(schedule.draw_day) AS draw_day')
            )
            ->leftJoin('bet_winning_usd', 'bet_winning_usd.bet_id', '=', 'bet_usd.id')
            ->join('users', 'users.id', '=', 'bet_usd.user_id')
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
            ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', 'bet_usd.bet_schedule_id')
            ->whereIn('bet_usd.user_id', $memberIds) 
            ->when($date, function ($q) use ($date) {
                $q->whereDate('bet_usd.bet_date', '=', Carbon::parse($date)->format('Y-m-d'));
            })
            ->when($company_id > 0, function ($q) use ($company_id) {
                $q->where('bet_usd.company_id', $company_id);
            })
            ->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                $q->where('bet_usd.user_id', $user->id);
            })
            ->groupBy(
                'bet_usd.user_id',
                'users.username',
                DB::raw('DATE(bet_usd.bet_date)')
            )
            ->orderByRaw('COUNT(DISTINCT bet_usd.bet_receipt_id) DESC')
            ->get();

            return view('report_usd.daily-member-agent', compact('data', 'date', 'company', 'company_id','memberId','managerName'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
    public function getMonthlyTracking(Request $request)
    {
        try {

            $dateFilter = $request->get('date');
            $startDate = null;
            $endDate = null;
            switch ($dateFilter) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today();
                    break;

                case 'yesterday':
                    $startDate = Carbon::yesterday();
                    $endDate = Carbon::yesterday();
                    break;

                case 'this_week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;

                case 'last_week':
                    $startDate = Carbon::now()->subWeek()->startOfWeek();
                    $endDate = Carbon::now()->subWeek()->endOfWeek();
                    break;

                case 'this_month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;

                case 'last_month':
                    $startDate = Carbon::now()->subMonth()->startOfMonth();
                    $endDate = Carbon::now()->subMonth()->endOfMonth();
                    break;

                default:
                    // If no filter provided, use today
                    $startDate = Carbon::today();
                    $endDate = Carbon::today();
                    break;
            }
           
            $data = DB::table('bet_usd')
            ->select(
                'users.username AS account',
                'users.id AS user_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('COALESCE(SUM(bet_winning_usd.win_amount), 0) AS Compensate')
            )
            ->leftJoin('bet_winning_usd', 'bet_winning_usd.bet_id', '=', 'bet_usd.id')
            ->join('users', 'users.id', '=', 'bet_usd.user_id')
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
            ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', 'bet_usd.bet_schedule_id')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('bet_usd.bet_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
            })
            ->groupBy(
                'bet_usd.user_id',
                'users.username',
            )
            ->orderByRaw('COUNT(DISTINCT bet_usd.bet_receipt_id) DESC')
            ->get();

            return view('report_usd.monthly', compact('data'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
}
