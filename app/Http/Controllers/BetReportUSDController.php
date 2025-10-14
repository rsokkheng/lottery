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
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            $date = $this->currentDate;
            if ($start_date && $end_date) {
                $startDate = Carbon::parse($start_date)->format('Y-m-d');
                $end_date = Carbon::parse($end_date)->format('Y-m-d');
            } else {
                $start_date= Carbon::parse($this->currentDate)->format('Y-m-d');
                $end_date = Carbon::parse($this->currentDate)->format('Y-m-d');
            }
            $user = Auth::user() ?? 0;
            if ($user) {
                $user = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
            }
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            $subQuery = DB::table('bet_number_usd as bn')
            ->join('bet_winning_usd as bw', 'bw.bet_number_id', '=', 'bn.id')
            ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
            ->groupBy('bn.bet_id');
            $data = DB::table('bet_usd')
            ->select(
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total'),
                DB::raw('SUM(bet_usd.total_amount) AS Turnover'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS NetAmount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS Commission'),
                DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate'),
                DB::raw('DATE(bet_usd.bet_date) AS date'),
                DB::raw('MAX(schedule.draw_day) AS draw_day')
            )
            ->leftJoinSub($subQuery, 'win_summary', function ($join) {
                $join->on('win_summary.bet_id', '=', 'bet_usd.id');
            })
            ->join('users', 'users.id', '=', 'bet_usd.user_id')
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
            ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', 'bet_usd.bet_schedule_id')
            ->when(in_array('manager', $roles), function ($q) use ($user) {
                // Get all users under this manager
                $memberIds = User::where('manager_id', $user->id)
                    ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                    ->pluck('id')
                    ->toArray();
                $q->whereIn('user_id', $memberIds);
            })->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
                // Apply filter for date range
                $q->whereBetween('bet_usd.bet_date', [
                    Carbon::parse($start_date)->startOfDay()->format('Y-m-d H:i:s'),
                    Carbon::parse($end_date)->endOfDay()->format('Y-m-d H:i:s')
                ]);
            })->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->when($date && !$start_date && !$end_date, function ($q) use ($date) {
                // Apply filter for specific date
                $q->whereDate('bet_usd.bet_date', '=', Carbon::parse($date)->format('Y-m-d'));
            })
            ->groupBy(
                DB::raw('DATE(bet_usd.bet_date)')
            )
            ->orderByRaw('COUNT(DISTINCT bet_usd.bet_receipt_id) DESC')
            ->get();
            return view('report_usd.summary', compact('data', 'date','start_date','end_date'));
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
            $company_id = $request->get('com_id', null) ?? null;

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
            $subQuery = DB::table('bet_number_usd as bn')
            ->join('bet_winning_usd as bw', 'bw.bet_number_id', '=', 'bn.id')
            ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
            ->groupBy('bn.bet_id');
            $data = DB::table('bet_usd')
            ->select(
                'users.username AS account',
                'users.id AS user_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate'),
                DB::raw('DATE(bet_usd.bet_date) AS bet_date'),
                DB::raw('MAX(schedule.draw_day) AS draw_day')
            )
            ->leftJoinSub($subQuery, 'win_summary', function ($join) {
                $join->on('win_summary.bet_id', '=', 'bet_usd.id');
            })
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
            $company_id = $request->get('com_id', null) ?? null;

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
            $subQuery = DB::table('bet_number_usd as bn')
            ->join('bet_winning_usd as bw', 'bw.bet_number_id', '=', 'bn.id')
            ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
            ->groupBy('bn.bet_id');

            $data = DB::table('bet_usd')
            ->select(
                'manag.username AS account',
                'users.manager_id AS manager_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate'),
                DB::raw('DATE(bet_usd.bet_date) AS bet_date'),
                DB::raw('MAX(schedule.draw_day) AS draw_day')
            )
            ->leftJoinSub($subQuery, 'win_summary', function ($join) {
                $join->on('win_summary.bet_id', '=', 'bet_usd.id');
            })
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
            $company_id = $request->get('com_id', null) ?? null;

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
           $company_id = $request->get('com_id', null) ?? null;
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

            $subQuery = DB::table('bet_number_usd as bn')
            ->join('bet_winning_usd as bw', 'bw.bet_number_id', '=', 'bn.id')
            ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
            ->groupBy('bn.bet_id');

            $data = DB::table('bet_usd')
            ->select(
                'users.username AS account',
                'users.id AS user_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate'),
                DB::raw('DATE(bet_usd.bet_date) AS bet_date'),
                DB::raw('MAX(schedule.draw_day) AS draw_day')
            )
            ->leftJoinSub($subQuery, 'win_summary', function ($join) {
                $join->on('win_summary.bet_id', '=', 'bet_usd.id');
            })
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
            $company = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"]
            ];
            $company_id = $request->get('com_id', null) ?? null;
            $startDate = request()->get('startDate');
            $endDate = request()->get('endDate');

            if ($startDate && $endDate) {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
            } else {
                $startDate = Carbon::parse($this->currentDate)->format('Y-m-d');
                $endDate = Carbon::parse($this->currentDate)->format('Y-m-d');
            }
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }

            $subQuery = DB::table('bet_number_usd as bn')
            ->join('bet_winning_usd as bw', 'bw.bet_number_id', '=', 'bn.id')
            ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
            ->groupBy('bn.bet_id');

            $data = DB::table('bet_usd')
                ->select(
                    'manag.username AS account',
                    'users.manager_id AS manager_id',
                    DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                    DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                    DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                    DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                    DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate'),
                )
                ->leftJoinSub($subQuery, 'win_summary', function ($join) {
                    $join->on('win_summary.bet_id', '=', 'bet_usd.id');
                })
                ->join('users', 'users.id', '=', 'bet_usd.user_id')
                ->join('users as manag', 'users.manager_id', '=', 'manag.id')
                ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
                ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', 'bet_usd.bet_schedule_id')
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('bet_usd.bet_date', [$startDate, $endDate]);
                })
                ->when($company_id > 0, function ($q) use ($company_id) {
                    $q->where('bet_usd.company_id', $company_id);
                })  
                ->groupBy(
                    'manag.username',
                    'users.manager_id',
                )
                ->orderByRaw('COUNT(DISTINCT bet_usd.bet_receipt_id) DESC')
                ->get();

            return view('report_usd.monthly', compact('data','startDate','endDate','company','company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
    public function getMonthlyByAgent(Request $request){
        try {
            $startDate = request()->get('startDate');
            $endDate = request()->get('endDate');
            $company = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"]
            ];
           $company_id = $request->get('com_id', null) ?? null;
            if ($startDate && $endDate) {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
            } else {
                $startDate = Carbon::parse($this->currentDate)->format('Y-m-d');
                $endDate = Carbon::parse($this->currentDate)->format('Y-m-d');
            }
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $user = Auth::user() ?? 0;
            if ($user) {
                $user = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
            }           
            $memberId = $request->id;
            $memberIds = User::where('manager_id', $memberId)->pluck('id')->toArray();
            $managerName = User::find($memberId );
            $subQuery = DB::table('bet_number_usd as bn')
            ->join('bet_winning_usd as bw', 'bw.bet_number_id', '=', 'bn.id')
            ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
            ->groupBy('bn.bet_id');
            $data = DB::table('bet_usd')
            ->select(
                'users.username AS account',
                'users.id AS user_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate')
            )
            ->leftJoinSub($subQuery, 'win_summary', function ($join) {
                $join->on('win_summary.bet_id', '=', 'bet_usd.id');
            })
            ->join('users', 'users.id', '=', 'bet_usd.user_id')
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
            ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', 'bet_usd.bet_schedule_id')
            ->whereIn('bet_usd.user_id', $memberIds)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('bet_usd.bet_date', [$startDate, $endDate]);
            })
            ->when($company_id > 0, function ($q) use ($company_id) {
                $q->where('bet_usd.company_id', $company_id);
            })
            ->groupBy(
                'bet_usd.user_id',
                'users.username'
            )
            ->orderByDesc(DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id)'))
            ->get();
            return view('report_usd.monthly-track-member', compact('data','managerName','startDate','endDate','company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
    public function getMonthlyAllMember(Request $request){
        try {
            $startDate = request()->get('startDate');
            $endDate = request()->get('endDate');
            $company_id = $request->get('com_id', null) ?? null;

            if ($startDate && $endDate) {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate = Carbon::parse($endDate)->format('Y-m-d');
            } else {
                $startDate = Carbon::parse($this->currentDate)->format('Y-m-d');
                $endDate = Carbon::parse($this->currentDate)->format('Y-m-d');
            }
             $company = [
                ["label" => "All Company", "id" => null],
                ["label" => "4PM Company", "id" => 1],
                ["label" => "5PM Company", "id" => 2],
                ["label" => "6PM Company", "id" => 3],
            ];
           
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            } 
            $user = Auth::user() ?? 0;     
            $memberIds = User::where('manager_id', $user->id)->pluck('id')->toArray();
            $managerName = User::find($user->id );
            $subQuery = DB::table('bet_number_usd as bn')
            ->join('bet_winning_usd as bw', 'bw.bet_number_id', '=', 'bn.id')
            ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
            ->groupBy('bn.bet_id');
            $data = DB::table('bet_usd')
            ->select(
                'users.username AS account',
                'users.id AS user_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate')
            )
            ->leftJoinSub($subQuery, 'win_summary', function ($join) {
                $join->on('win_summary.bet_id', '=', 'bet_usd.id');
            })
            ->join('users', 'users.id', '=', 'bet_usd.user_id')
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
            ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', 'bet_usd.bet_schedule_id')
            ->whereIn('bet_usd.user_id', $memberIds)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('bet_usd.bet_date', [$startDate, $endDate]);
            })
            ->when($company_id > 0, function ($q) use ($company_id) {
                $q->where('bet_usd.company_id', $company_id);
            })
            ->groupBy(
                'bet_usd.user_id',
                'users.username'
            )
            ->orderByDesc(DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id)'))
            ->get();
            return view('report_usd.monthly-all-member', compact('data','managerName','startDate','endDate','company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
    public function getMonthlyByAgentMember($member_id,Request $request){

        $date = $this->currentDate;
        $startDate = request()->get('startDate');
        $endDate = request()->get('endDate');

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
        } else {
            $startDate = Carbon::parse($this->currentDate)->format('Y-m-d');
            $endDate = Carbon::parse($this->currentDate)->format('Y-m-d');
        }
        $company = [
            ["label" => "All Company", "id" => null],
            ["label" => "4PM Company", "id" => 1],
            ["label" => "5PM Company", "id" => 2],
            ["label" => "6PM Company", "id" => 3],
        ];
       $company_id = $request->get('com_id', null) ?? null;
        if ($request->has('com_id')) {
            $company_id = $request->get('com_id');
        }

        $company_id = $company_id == 0 ? null : $company_id;
        $data = [];
        $totalNetAmount = [
            'turnover' => 0,
            'commission'=>0,
            'net_amount'=>0,
            'compensate'=>0,
            'win_lose' => 0
        ];
            DB::table('bet_number_usd')
            ->select(
                'bet_number_usd.original_number',
                'bet_number_usd.generated_number',
                'bet_number_usd.total_amount as number_turnover',
                'bet_number_usd.a_amount',
                'bet_number_usd.b_amount',
                'bet_number_usd.ab_amount',
                'bet_number_usd.roll_amount',
                'bet_number_usd.roll7_amount',
                'bet_number_usd.roll_parlay_amount',
                'bet_number_usd.created_at',
                'bet_usd.digit_format',
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
                'bet_usd.bet_schedule_id',
                DB::raw('bet_winning_usd.win_amount as compensate')
            )  
            ->leftJoin('bet_winning_usd','bet_winning_usd.bet_number_id','=', 'bet_number_usd.id')
            ->join('bet_usd','bet_usd.id','=', 'bet_number_usd.bet_id')
            ->join('bet_package_configurations as config','config.id','=', 'bet_usd.bet_package_config_id')
            ->join('bet_lottery_schedules as schedules','schedules.id','=', 'bet_usd.bet_schedule_id')
            ->join('users','users.id','=', 'bet_usd.user_id')
            ->where('bet_usd.user_id', $member_id)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('bet_usd.bet_date', [$startDate, $endDate]);
            })->when(!is_null($company_id), function ($q) use ($company_id) {
                $q->where('bet_usd.company_id', $company_id);
            })
            ->groupBy('bet_winning_usd.win_amount')
            ->groupBy('bet_number_usd.a_amount')
            ->groupBy('bet_number_usd.b_amount')
            ->groupBy('bet_number_usd.ab_amount')
            ->groupBy('bet_number_usd.roll_amount')
            ->groupBy('bet_number_usd.roll7_amount')
            ->groupBy('bet_number_usd.roll_parlay_amount')
            ->groupBy('bet_number_usd.original_number')
            ->groupBy('bet_number_usd.generated_number')
            ->groupBy('bet_number_usd.created_at')
            ->groupBy('bet_usd.total_amount')
            ->groupBy('bet_usd.digit_format')
            ->groupBy('bet_usd.company_id')
            ->groupBy('bet_usd.bet_schedule_id')
            ->groupBy('config.rate')
            ->groupBy('config.price')
            ->groupBy('config.bet_type')
            ->groupBy('schedules.province_en')
            ->groupBy('bet_number_usd.total_amount')
                ->orderBy('bet_number_usd.created_at','ASC')
            ->lazy()
            ->each(function ($betNumber) use (&$data, &$totalNetAmount){
                $betNumber->win_lose = $betNumber->compensate - $betNumber->net_amount;
                $totalNetAmount['commission'] += $betNumber->commission;
                $totalNetAmount['net_amount'] += $betNumber->net_amount;
                $totalNetAmount['turnover'] += $betNumber->number_turnover;
                $totalNetAmount['compensate'] += $betNumber->compensate ;
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

        return view('report_usd.report-bet-number', compact('data','totalNetAmount','date','startDate','endDate','company','company_id','member_id'));
        
    }
    function sumExistingBet(&$item, &$betNumber){
        $item->commission += $betNumber->commission;
        $item->compensate += $betNumber->compensate;
        $item->net_amount += $betNumber->net_amount;
        $item->win_lose += $betNumber->win_lose;
        $item->number_turnover += $betNumber->number_turnover;
        $item->get_roll_amount += $betNumber->get_roll_amount;
    }
}
