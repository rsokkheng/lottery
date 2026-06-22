<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\BetKH;
use App\Models\User;
use App\Models\BetReceiptKH;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\throwException;

class BetReportKHController extends Controller
{
    public BetReceiptKH $model;
    public BetKH $betModel;
    public $currentDate;

    public function __construct(BetReceiptKH $model, BetKH $betModel)
    {
        $this->model       = $model;
        $this->betModel    = $betModel;
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    private function tBet(): string { return 'bet_kh_'          . strtolower((string)(session('currency') ?? 'VND')); }
    private function tNum(): string { return 'bet_number_kh_'   . strtolower((string)(session('currency') ?? 'VND')); }
    private function tWin(): string { return 'bet_winning_kh_'  . strtolower((string)(session('currency') ?? 'VND')); }

    public function getSummaryReport(Request $request)
    {
        try {
            $tBet = $this->tBet();
            $tNum = $this->tNum();
            $tWin = $this->tWin();

            $start_date = request()->get('start_date');
            $end_date   = request()->get('end_date');
            $date       = $this->currentDate;
            if ($start_date && $end_date) {
                $start_date = Carbon::parse($start_date)->format('Y-m-d');
                $end_date   = Carbon::parse($end_date)->format('Y-m-d');
            } else {
                $start_date = Carbon::parse($this->currentDate)->format('Y-m-d');
                $end_date   = Carbon::parse($this->currentDate)->format('Y-m-d');
            }
            $user = Auth::user() ?? 0;
            if ($user) {
                $user  = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray();
            }
            if ($request->has('date')) {
                $date = $request->get('date');
            }

            $subQuery = DB::table("{$tNum} as bn")
                ->join("{$tWin} as bw", 'bw.bet_number_id', '=', 'bn.id')
                ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
                ->groupBy('bn.bet_id');

            $data = DB::table($tBet)
                ->select(
                    DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id) AS total"),
                    DB::raw("SUM({$tBet}.total_amount) AS Turnover"),
                    DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS NetAmount"),
                    DB::raw("SUM({$tBet}.total_amount - ({$tBet}.total_amount * bet_package_configurations.rate / 100)) AS Commission"),
                    DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate'),
                    DB::raw("DATE({$tBet}.bet_date) AS date"),
                    DB::raw('MAX(schedule.draw_day) AS draw_day')
                )
                ->leftJoinSub($subQuery, 'win_summary', function ($join) use ($tBet) {
                    $join->on('win_summary.bet_id', '=', "{$tBet}.id");
                })
                ->join('users', 'users.id', '=', "{$tBet}.user_id")
                ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
                ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', "{$tBet}.bet_schedule_id")
                ->when(in_array('agent', $roles), function ($q) use ($user) {
                    $memberIds = User::where('manager_id', $user->id)
                        ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                        ->pluck('id')->toArray();
                    $q->whereIn('user_id', $memberIds);
                })
                ->when($start_date && $end_date, function ($q) use ($start_date, $end_date, $tBet) {
                    $q->whereBetween("{$tBet}.bet_date", [
                        Carbon::parse($start_date)->startOfDay()->format('Y-m-d H:i:s'),
                        Carbon::parse($end_date)->endOfDay()->format('Y-m-d H:i:s'),
                    ]);
                })
                ->when(!in_array('admin', $roles) && !in_array('agent', $roles), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->when($date && !$start_date && !$end_date, function ($q) use ($date, $tBet) {
                    $q->whereDate("{$tBet}.bet_date", '=', Carbon::parse($date)->format('Y-m-d'));
                })
                ->groupBy(DB::raw("DATE({$tBet}.bet_date)"))
                ->orderByRaw("COUNT(DISTINCT {$tBet}.bet_receipt_id) DESC")
                ->get();

            return view('report_kh.summary', compact('data', 'date', 'start_date', 'end_date'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getDailyReport(Request $request)
    {
        try {
            $tBet = $this->tBet();
            $tNum = $this->tNum();
            $tWin = $this->tWin();

            $company    = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"],
            ];
            $date       = $this->currentDate;
            $company_id = $request->get('com_id', null);

            $user = Auth::user() ?? 0;
            if ($user) {
                $user  = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray();
            }
            if ($request->has('date'))   { $date = $request->get('date'); }
            if ($request->has('com_id')) { $company_id = $request->get('com_id'); }

            $subQuery = DB::table("{$tNum} as bn")
                ->join("{$tWin} as bw", 'bw.bet_number_id', '=', 'bn.id')
                ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
                ->groupBy('bn.bet_id');

            $data = DB::table($tBet)
                ->select(
                    'users.username AS account',
                    'users.id AS user_id',
                    DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id) AS total_receipts"),
                    DB::raw("SUM({$tBet}.total_amount) AS total_amount"),
                    DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS net_amount"),
                    DB::raw("SUM({$tBet}.total_amount - ({$tBet}.total_amount * bet_package_configurations.rate / 100)) AS commission"),
                    DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate'),
                    DB::raw("DATE({$tBet}.bet_date) AS bet_date"),
                    DB::raw('MAX(schedule.draw_day) AS draw_day')
                )
                ->leftJoinSub($subQuery, 'win_summary', function ($join) use ($tBet) {
                    $join->on('win_summary.bet_id', '=', "{$tBet}.id");
                })
                ->join('users', 'users.id', '=', "{$tBet}.user_id")
                ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
                ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', "{$tBet}.bet_schedule_id")
                ->when(in_array('agent', $roles), function ($q) use ($user, $tBet) {
                    $memberIds = User::where('manager_id', $user->id)
                        ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                        ->pluck('id')->toArray();
                    $q->whereIn("{$tBet}.user_id", $memberIds);
                })
                ->when($date, function ($q) use ($date, $tBet) {
                    $q->whereDate("{$tBet}.bet_date", '=', Carbon::parse($date)->format('Y-m-d'));
                })
                ->when($company_id > 0, function ($q) use ($company_id, $tBet) {
                    $q->where("{$tBet}.company_id", $company_id);
                })
                ->when(!in_array('admin', $roles) && !in_array('agent', $roles), function ($q) use ($user, $tBet) {
                    $q->where("{$tBet}.user_id", $user->id);
                })
                ->groupBy("{$tBet}.user_id", 'users.username', DB::raw("DATE({$tBet}.bet_date)"))
                ->orderByRaw("COUNT(DISTINCT {$tBet}.bet_receipt_id) DESC")
                ->get();

            return view('report_kh.daily', compact('data', 'date', 'company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getDailyReportManager(Request $request)
    {
        try {
            $tBet = $this->tBet();
            $tNum = $this->tNum();
            $tWin = $this->tWin();

            $company    = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"],
            ];
            $date       = $this->currentDate;
            $company_id = $request->get('com_id', null);

            $user = Auth::user() ?? 0;
            if ($user) {
                $user  = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray();
            }
            if ($request->has('date'))   { $date = $request->get('date'); }
            if ($request->has('com_id')) { $company_id = $request->get('com_id'); }

            $subQuery = DB::table("{$tNum} as bn")
                ->join("{$tWin} as bw", 'bw.bet_number_id', '=', 'bn.id')
                ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
                ->groupBy('bn.bet_id');

            $data = DB::table($tBet)
                ->select(
                    'manag.username AS account',
                    'users.manager_id AS manager_id',
                    DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id) AS total_receipts"),
                    DB::raw("SUM({$tBet}.total_amount) AS total_amount"),
                    DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS net_amount"),
                    DB::raw("SUM({$tBet}.total_amount - ({$tBet}.total_amount * bet_package_configurations.rate / 100)) AS commission"),
                    DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate'),
                    DB::raw("DATE({$tBet}.bet_date) AS bet_date"),
                    DB::raw('MAX(schedule.draw_day) AS draw_day')
                )
                ->leftJoinSub($subQuery, 'win_summary', function ($join) use ($tBet) {
                    $join->on('win_summary.bet_id', '=', "{$tBet}.id");
                })
                ->join('users', 'users.id', '=', "{$tBet}.user_id")
                ->join('users as manag', 'users.manager_id', '=', 'manag.id')
                ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
                ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', "{$tBet}.bet_schedule_id")
                ->when($date, function ($q) use ($date, $tBet) {
                    $q->whereDate("{$tBet}.bet_date", '=', Carbon::parse($date)->format('Y-m-d'));
                })
                ->when($company_id > 0, function ($q) use ($company_id, $tBet) {
                    $q->where("{$tBet}.company_id", $company_id);
                })
                ->when(!in_array('admin', $roles) && !in_array('agent', $roles), function ($q) use ($user, $tBet) {
                    $q->where("{$tBet}.user_id", $user->id);
                })
                ->groupBy('users.manager_id', 'manag.username', DB::raw("DATE({$tBet}.bet_date)"))
                ->orderByRaw("COUNT(DISTINCT {$tBet}.bet_receipt_id) DESC")
                ->get();

            return view('report_kh.manager-daily', compact('data', 'date', 'company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getDailyReportKH(Request $request)
    {
        try {
            $tBet = $this->tBet();
            $tWin = $this->tWin();

            $company    = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"],
            ];
            $date       = $this->currentDate;
            $company_id = $request->get('com_id', null);

            $user = Auth::user() ?? 0;
            if ($user) {
                $user  = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray();
            }
            if ($request->has('date'))   { $date = $request->get('date'); }
            if ($request->has('com_id')) { $company_id = $request->get('com_id'); }

            $data = DB::table($tBet)
                ->select(
                    'users.username AS account',
                    'users.id AS user_id',
                    DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id) AS total_receipts"),
                    DB::raw("SUM({$tBet}.total_amount) AS total_amount"),
                    DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS net_amount"),
                    DB::raw("SUM({$tBet}.total_amount - ({$tBet}.total_amount * bet_package_configurations.rate / 100)) AS commission"),
                    DB::raw("COALESCE(SUM({$tWin}.win_amount), 0) AS Compensate"),
                    DB::raw("DATE({$tBet}.bet_date) AS bet_date"),
                    DB::raw('MAX(schedule.draw_day) AS draw_day')
                )
                ->leftJoin($tWin, "{$tWin}.bet_id", '=', "{$tBet}.id")
                ->join('users', 'users.id', '=', "{$tBet}.user_id")
                ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
                ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', "{$tBet}.bet_schedule_id")
                ->when(in_array('agent', $roles), function ($q) use ($user, $tBet) {
                    $memberIds = User::where('manager_id', $user->id)
                        ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                        ->pluck('id')->toArray();
                    $q->whereIn("{$tBet}.user_id", $memberIds);
                })
                ->when($date, function ($q) use ($date, $tBet) {
                    $q->whereDate("{$tBet}.bet_date", '=', Carbon::parse($date)->format('Y-m-d'));
                })
                ->when($company_id > 0, function ($q) use ($company_id, $tBet) {
                    $q->where("{$tBet}.company_id", $company_id);
                })
                ->when(!in_array('admin', $roles) && !in_array('agent', $roles), function ($q) use ($user, $tBet) {
                    $q->where("{$tBet}.user_id", $user->id);
                })
                ->groupBy("{$tBet}.user_id", 'users.username', DB::raw("DATE({$tBet}.bet_date)"))
                ->orderByRaw("COUNT(DISTINCT {$tBet}.bet_receipt_id) DESC")
                ->get();

            return view('admin.report.daily-kh', compact('data', 'date', 'company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getDailyReportMeberAgent(Request $request)
    {
        try {
            $tBet = $this->tBet();
            $tNum = $this->tNum();
            $tWin = $this->tWin();

            $company    = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"],
            ];
            $date       = $this->currentDate;
            $company_id = $request->get('com_id', null);

            $user = Auth::user() ?? 0;
            if ($user) {
                $user  = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray();
            }
            $memberId    = $request->id;
            $memberIds   = User::where('manager_id', $memberId)->pluck('id')->toArray();
            $managerName = User::find($memberId);
            if ($request->has('date'))   { $date = $request->get('date'); }
            if ($request->has('com_id')) { $company_id = $request->get('com_id'); }

            $subQuery = DB::table("{$tNum} as bn")
                ->join("{$tWin} as bw", 'bw.bet_number_id', '=', 'bn.id')
                ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
                ->groupBy('bn.bet_id');

            $data = DB::table($tBet)
                ->select(
                    'users.username AS account',
                    'users.id AS user_id',
                    DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id) AS total_receipts"),
                    DB::raw("SUM({$tBet}.total_amount) AS total_amount"),
                    DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS net_amount"),
                    DB::raw("SUM({$tBet}.total_amount - ({$tBet}.total_amount * bet_package_configurations.rate / 100)) AS commission"),
                    DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate'),
                    DB::raw("DATE({$tBet}.bet_date) AS bet_date"),
                    DB::raw('MAX(schedule.draw_day) AS draw_day')
                )
                ->leftJoinSub($subQuery, 'win_summary', function ($join) use ($tBet) {
                    $join->on('win_summary.bet_id', '=', "{$tBet}.id");
                })
                ->join('users', 'users.id', '=', "{$tBet}.user_id")
                ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
                ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', "{$tBet}.bet_schedule_id")
                ->whereIn("{$tBet}.user_id", $memberIds)
                ->when($date, function ($q) use ($date, $tBet) {
                    $q->whereDate("{$tBet}.bet_date", '=', Carbon::parse($date)->format('Y-m-d'));
                })
                ->when($company_id > 0, function ($q) use ($company_id, $tBet) {
                    $q->where("{$tBet}.company_id", $company_id);
                })
                ->when(!in_array('admin', $roles) && !in_array('agent', $roles), function ($q) use ($user, $tBet) {
                    $q->where("{$tBet}.user_id", $user->id);
                })
                ->groupBy("{$tBet}.user_id", 'users.username', DB::raw("DATE({$tBet}.bet_date)"))
                ->orderByRaw("COUNT(DISTINCT {$tBet}.bet_receipt_id) DESC")
                ->get();

            return view('report_kh.daily-member-agent', compact('data', 'date', 'company', 'company_id', 'memberId', 'managerName'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getMonthlyTracking(Request $request)
    {
        try {
            $tBet = $this->tBet();
            $tNum = $this->tNum();
            $tWin = $this->tWin();

            $company    = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"],
            ];
            $company_id = $request->get('com_id', null);
            $startDate  = request()->get('startDate');
            $endDate    = request()->get('endDate');

            if ($startDate && $endDate) {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate   = Carbon::parse($endDate)->format('Y-m-d');
            } else {
                $startDate = Carbon::parse($this->currentDate)->format('Y-m-d');
                $endDate   = Carbon::parse($this->currentDate)->format('Y-m-d');
            }
            if ($request->has('com_id')) { $company_id = $request->get('com_id'); }

            $subQuery = DB::table("{$tNum} as bn")
                ->join("{$tWin} as bw", 'bw.bet_number_id', '=', 'bn.id')
                ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
                ->groupBy('bn.bet_id');

            $data = DB::table($tBet)
                ->select(
                    'manag.username AS account',
                    'users.manager_id AS manager_id',
                    DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id) AS total_receipts"),
                    DB::raw("SUM({$tBet}.total_amount) AS total_amount"),
                    DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS net_amount"),
                    DB::raw("SUM({$tBet}.total_amount - ({$tBet}.total_amount * bet_package_configurations.rate / 100)) AS commission"),
                    DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate'),
                )
                ->leftJoinSub($subQuery, 'win_summary', function ($join) use ($tBet) {
                    $join->on('win_summary.bet_id', '=', "{$tBet}.id");
                })
                ->join('users', 'users.id', '=', "{$tBet}.user_id")
                ->join('users as manag', 'users.manager_id', '=', 'manag.id')
                ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
                ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', "{$tBet}.bet_schedule_id")
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate, $tBet) {
                    $q->whereBetween("{$tBet}.bet_date", [$startDate, $endDate]);
                })
                ->when($company_id > 0, function ($q) use ($company_id, $tBet) {
                    $q->where("{$tBet}.company_id", $company_id);
                })
                ->groupBy('manag.username', 'users.manager_id')
                ->orderByRaw("COUNT(DISTINCT {$tBet}.bet_receipt_id) DESC")
                ->get();

            return view('report_kh.monthly', compact('data', 'startDate', 'endDate', 'company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getMonthlyByAgent(Request $request)
    {
        try {
            $tBet = $this->tBet();
            $tNum = $this->tNum();
            $tWin = $this->tWin();

            $startDate  = request()->get('startDate');
            $endDate    = request()->get('endDate');
            $company    = [
                ["id" => 0, "label" => "All Company"],
                ["id" => 1, "label" => "4PM Company"],
                ["id" => 2, "label" => "5PM Company"],
                ["id" => 3, "label" => "6PM Company"],
            ];
            $company_id = $request->get('com_id', null);
            if ($startDate && $endDate) {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate   = Carbon::parse($endDate)->format('Y-m-d');
            } else {
                $startDate = Carbon::parse($this->currentDate)->format('Y-m-d');
                $endDate   = Carbon::parse($this->currentDate)->format('Y-m-d');
            }
            if ($request->has('com_id')) { $company_id = $request->get('com_id'); }

            $user = Auth::user() ?? 0;
            if ($user) {
                $user  = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray();
            }
            $memberId    = $request->id;
            $memberIds   = User::where('manager_id', $memberId)->pluck('id')->toArray();
            $managerName = User::find($memberId);

            $subQuery = DB::table("{$tNum} as bn")
                ->join("{$tWin} as bw", 'bw.bet_number_id', '=', 'bn.id')
                ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
                ->groupBy('bn.bet_id');

            $data = DB::table($tBet)
                ->select(
                    'users.username AS account',
                    'users.id AS user_id',
                    DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id) AS total_receipts"),
                    DB::raw("SUM({$tBet}.total_amount) AS total_amount"),
                    DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS net_amount"),
                    DB::raw("SUM({$tBet}.total_amount - ({$tBet}.total_amount * bet_package_configurations.rate / 100)) AS commission"),
                    DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate')
                )
                ->leftJoinSub($subQuery, 'win_summary', function ($join) use ($tBet) {
                    $join->on('win_summary.bet_id', '=', "{$tBet}.id");
                })
                ->join('users', 'users.id', '=', "{$tBet}.user_id")
                ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
                ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', "{$tBet}.bet_schedule_id")
                ->whereIn("{$tBet}.user_id", $memberIds)
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate, $tBet) {
                    $q->whereBetween("{$tBet}.bet_date", [$startDate, $endDate]);
                })
                ->when($company_id > 0, function ($q) use ($company_id, $tBet) {
                    $q->where("{$tBet}.company_id", $company_id);
                })
                ->groupBy("{$tBet}.user_id", 'users.username')
                ->orderByDesc(DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id)"))
                ->get();

            return view('report_kh.monthly-track-member', compact('data', 'managerName', 'startDate', 'endDate', 'company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getMonthlyAllMember(Request $request)
    {
        try {
            $tBet = $this->tBet();
            $tNum = $this->tNum();
            $tWin = $this->tWin();

            $startDate  = request()->get('startDate');
            $endDate    = request()->get('endDate');
            $company_id = $request->get('com_id', null);

            if ($startDate && $endDate) {
                $startDate = Carbon::parse($startDate)->format('Y-m-d');
                $endDate   = Carbon::parse($endDate)->format('Y-m-d');
            } else {
                $startDate = Carbon::parse($this->currentDate)->format('Y-m-d');
                $endDate   = Carbon::parse($this->currentDate)->format('Y-m-d');
            }
            $company = [
                ["label" => "All Company", "id" => null],
                ["label" => "4PM Company", "id" => 1],
                ["label" => "5PM Company", "id" => 2],
                ["label" => "6PM Company", "id" => 3],
            ];
            if ($request->has('com_id')) { $company_id = $request->get('com_id'); }

            $user        = Auth::user() ?? 0;
            $memberIds   = User::where('manager_id', $user->id)->pluck('id')->toArray();
            $managerName = User::find($user->id);

            $subQuery = DB::table("{$tNum} as bn")
                ->join("{$tWin} as bw", 'bw.bet_number_id', '=', 'bn.id')
                ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
                ->groupBy('bn.bet_id');

            $data = DB::table($tBet)
                ->select(
                    'users.username AS account',
                    'users.id AS user_id',
                    DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id) AS total_receipts"),
                    DB::raw("SUM({$tBet}.total_amount) AS total_amount"),
                    DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS net_amount"),
                    DB::raw("SUM({$tBet}.total_amount - ({$tBet}.total_amount * bet_package_configurations.rate / 100)) AS commission"),
                    DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS Compensate')
                )
                ->leftJoinSub($subQuery, 'win_summary', function ($join) use ($tBet) {
                    $join->on('win_summary.bet_id', '=', "{$tBet}.id");
                })
                ->join('users', 'users.id', '=', "{$tBet}.user_id")
                ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
                ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', "{$tBet}.bet_schedule_id")
                ->whereIn("{$tBet}.user_id", $memberIds)
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate, $tBet) {
                    $q->whereBetween("{$tBet}.bet_date", [$startDate, $endDate]);
                })
                ->when($company_id > 0, function ($q) use ($company_id, $tBet) {
                    $q->where("{$tBet}.company_id", $company_id);
                })
                ->groupBy("{$tBet}.user_id", 'users.username')
                ->orderByDesc(DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id)"))
                ->get();

            return view('report_kh.monthly-all-member', compact('data', 'managerName', 'startDate', 'endDate', 'company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getMonthlyByAgentMember($member_id, Request $request)
    {
        $tBet = $this->tBet();
        $tNum = $this->tNum();
        $tWin = $this->tWin();

        $date      = $this->currentDate;
        $startDate = request()->get('startDate');
        $endDate   = request()->get('endDate');

        if ($startDate && $endDate) {
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate   = Carbon::parse($endDate)->format('Y-m-d');
        } else {
            $startDate = Carbon::parse($this->currentDate)->format('Y-m-d');
            $endDate   = Carbon::parse($this->currentDate)->format('Y-m-d');
        }
        $company = [
            ["label" => "All Company", "id" => null],
            ["label" => "4PM Company", "id" => 1],
            ["label" => "5PM Company", "id" => 2],
            ["label" => "6PM Company", "id" => 3],
        ];
        $company_id = $request->get('com_id', null);
        if ($request->has('com_id')) { $company_id = $request->get('com_id'); }
        $company_id = $company_id == 0 ? null : $company_id;

        $data           = [];
        $totalNetAmount = [
            'turnover'   => 0,
            'commission' => 0,
            'net_amount' => 0,
            'compensate' => 0,
            'win_lose'   => 0,
        ];

        DB::table($tNum)
            ->select(
                "{$tNum}.original_number",
                "{$tNum}.generated_number",
                "{$tNum}.total_amount as number_turnover",
                "{$tNum}.a_amount",
                "{$tNum}.b_amount",
                "{$tNum}.abcd_amount",
                "{$tNum}.roll_amount",
                "{$tNum}.roll2_amount",
                "{$tNum}.roll_parlay_amount",
                "{$tNum}.created_at",
                "{$tBet}.digit_format",
                DB::raw("CASE
                            WHEN {$tNum}.a_amount > 0 THEN 'A'
                            WHEN {$tNum}.b_amount > 0 THEN 'B'
                            WHEN {$tNum}.abcd_amount > 0 THEN 'ABCD'
                            WHEN {$tNum}.roll_amount > 0 THEN 'Roll'
                            WHEN {$tNum}.roll2_amount > 0 THEN 'Roll2'
                            ELSE 'Roll Parlay'
                            END AS bet_game"),
                DB::raw("CASE
                            WHEN {$tNum}.a_amount > 0 THEN {$tNum}.a_amount
                            WHEN {$tNum}.b_amount > 0 THEN {$tNum}.b_amount
                            WHEN {$tNum}.abcd_amount > 0 THEN {$tNum}.abcd_amount
                            WHEN {$tNum}.roll_amount > 0 THEN {$tNum}.roll_amount
                            WHEN {$tNum}.roll2_amount > 0 THEN {$tNum}.roll2_amount
                            ELSE {$tNum}.roll_parlay_amount
                            END AS get_roll_amount"),
                DB::raw("{$tNum}.total_amount - ({$tNum}.total_amount * config.rate /100) as commission"),
                DB::raw("({$tNum}.total_amount * config.rate /100) as net_amount"),
                'config.rate',
                'config.price',
                'config.bet_type',
                'schedules.province_en',
                "{$tBet}.company_id",
                "{$tBet}.bet_schedule_id",
                DB::raw("{$tWin}.win_amount as compensate")
            )
            ->leftJoin($tWin, "{$tWin}.bet_number_id", '=', "{$tNum}.id")
            ->join($tBet, "{$tBet}.id", '=', "{$tNum}.bet_id")
            ->join('bet_package_configurations as config', 'config.id', '=', "{$tBet}.bet_package_config_id")
            ->join('bet_lottery_schedules as schedules', 'schedules.id', '=', "{$tBet}.bet_schedule_id")
            ->join('users', 'users.id', '=', "{$tBet}.user_id")
            ->where("{$tBet}.user_id", $member_id)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate, $tBet) {
                $q->whereBetween("{$tBet}.bet_date", [$startDate, $endDate]);
            })
            ->when(!is_null($company_id), function ($q) use ($company_id, $tBet) {
                $q->where("{$tBet}.company_id", $company_id);
            })
            ->groupBy("{$tWin}.win_amount")
            ->groupBy("{$tNum}.a_amount")
            ->groupBy("{$tNum}.b_amount")
            ->groupBy("{$tNum}.abcd_amount")
            ->groupBy("{$tNum}.roll_amount")
            ->groupBy("{$tNum}.roll2_amount")
            ->groupBy("{$tNum}.roll_parlay_amount")
            ->groupBy("{$tNum}.original_number")
            ->groupBy("{$tNum}.generated_number")
            ->groupBy("{$tNum}.created_at")
            ->groupBy("{$tBet}.total_amount")
            ->groupBy("{$tBet}.digit_format")
            ->groupBy("{$tBet}.company_id")
            ->groupBy("{$tBet}.bet_schedule_id")
            ->groupBy('config.rate')
            ->groupBy('config.price')
            ->groupBy('config.bet_type')
            ->groupBy('schedules.province_en')
            ->groupBy("{$tNum}.total_amount")
            ->orderBy("{$tNum}.created_at", 'ASC')
            ->lazy()
            ->each(function ($betNumber) use (&$data, &$totalNetAmount) {
                $betNumber->win_lose = $betNumber->compensate - $betNumber->net_amount;
                $totalNetAmount['commission'] += $betNumber->commission;
                $totalNetAmount['net_amount'] += $betNumber->net_amount;
                $totalNetAmount['turnover']   += $betNumber->number_turnover;
                $totalNetAmount['compensate'] += $betNumber->compensate;
                $totalNetAmount['win_lose']   += $betNumber->win_lose;

                if (empty($data)) {
                    $data[] = $betNumber;
                } else {
                    $betExist = false;
                    $data = array_map(function ($item) use ($betNumber, &$betExist) {
                        $status = $item->company_id === $betNumber->company_id
                            && $item->bet_schedule_id === $betNumber->bet_schedule_id
                            && $item->generated_number === $betNumber->generated_number;
                        if ($status) {
                            if ((float)$item->a_amount && (float)$betNumber->a_amount) {
                                $item->a_amount = (float)$item->a_amount + (float)$betNumber->a_amount;
                                $this->sumExistingBet($item, $betNumber);
                                $betExist = true;
                            }
                            if ((float)$item->b_amount && (float)$betNumber->b_amount) {
                                $item->b_amount = (float)$item->b_amount + (float)$betNumber->b_amount;
                                $this->sumExistingBet($item, $betNumber);
                                $betExist = true;
                            }
                            if ((float)$item->abcd_amount && (float)$betNumber->abcd_amount) {
                                $item->abcd_amount = (float)$item->abcd_amount + (float)$betNumber->abcd_amount;
                                $this->sumExistingBet($item, $betNumber);
                                $betExist = true;
                            }
                            if ((float)$item->roll_amount && (float)$betNumber->roll_amount) {
                                $item->roll_amount = (float)$item->roll_amount + (float)$betNumber->roll_amount;
                                $this->sumExistingBet($item, $betNumber);
                                $betExist = true;
                            }
                            if ((float)$item->roll2_amount && (float)$betNumber->roll2_amount) {
                                $item->roll2_amount = (float)$item->roll2_amount + (float)$betNumber->roll2_amount;
                                $this->sumExistingBet($item, $betNumber);
                                $betExist = true;
                            }
                            if ((float)$item->roll_parlay_amount && (float)$betNumber->roll_parlay_amount) {
                                $item->roll_parlay_amount = (float)$item->roll_parlay_amount + (float)$betNumber->roll_parlay_amount;
                                $this->sumExistingBet($item, $betNumber);
                                $betExist = true;
                            }
                        }
                        return $item;
                    }, $data);
                    if (!$betExist) {
                        $data[] = $betNumber;
                    }
                }
            });

        return view('report_kh.report-bet-number', compact('data', 'totalNetAmount', 'date', 'startDate', 'endDate', 'company', 'company_id', 'member_id'));
    }

    function sumExistingBet(&$item, &$betNumber)
    {
        $item->commission      += $betNumber->commission;
        $item->compensate      += $betNumber->compensate;
        $item->net_amount      += $betNumber->net_amount;
        $item->win_lose        += $betNumber->win_lose;
        $item->number_turnover += $betNumber->number_turnover;
        $item->get_roll_amount += $betNumber->get_roll_amount;
    }
}
