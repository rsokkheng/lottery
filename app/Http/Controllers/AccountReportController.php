<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BalanceReport;


class AccountReportController extends Controller
{
    public function __construct()
    {
    }
    
    public function index(Request $request){

        $start_date = $request->startDate;
        $end_date = $request->endDate;
        $data = BalanceReport::join('users', 'users.id', '=', 'balance_reports.created_by')
            ->join('user_currencies', 'user_currencies.user_id', '=', 'users.id')
            ->where('user_currencies.currency', '=', 'VND')
            ->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
                $q->whereBetween('balance_reports.report_date', [
                    Carbon::createFromFormat('d/m/Y', $start_date)->startOfDay()->format('Y-m-d'),
                    Carbon::createFromFormat('d/m/Y', $end_date)->endOfDay()->format('Y-m-d'),
                ]);
            })
            ->when(!$start_date && !$end_date, function ($q) {
                $q->whereDate('balance_reports.report_date', now()->format('Y-m-d'));
            })
            ->groupBy(
                'balance_reports.user_id',
                'balance_reports.report_date',
                'balance_reports.created_at',
                'users.name',
                'balance_reports.name_user',
                'balance_reports.text'
            )
            ->selectRaw('
                balance_reports.name_user,
                balance_reports.report_date,
                balance_reports.created_at,
                users.name as created_by,
                balance_reports.text,
                balance_reports.user_id,
                SUM(balance_reports.deposit) as total_deposit,
                SUM(balance_reports.withdraw) as total_withdraw,
                SUM(balance_reports.adjustment) as total_adjustment,
                SUM(balance_reports.balance) as total_balance
            ')
            ->orderByDesc('balance_reports.report_date')
            ->get();
        return view('admin.account-report.index', compact('data'));
    }
    public function transactionUSD(Request $request){
        $start_date = $request->startDate;
        $end_date = $request->endDate;
        $data = BalanceReport::join('users', 'users.id', '=', 'balance_reports.created_by')
            ->join('user_currencies', 'user_currencies.user_id', '=', 'users.id')
            ->where('user_currencies.currency', '=', 'USD')
            ->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
                $q->whereBetween('balance_reports.report_date', [
                    Carbon::createFromFormat('d/m/Y', $start_date)->startOfDay()->format('Y-m-d'),
                    Carbon::createFromFormat('d/m/Y', $end_date)->endOfDay()->format('Y-m-d'),
                ]);
            })
            ->when(!$start_date && !$end_date, function ($q) {
                $q->whereDate('balance_reports.report_date', now()->format('Y-m-d'));
            })
            ->groupBy(
                'balance_reports.user_id',
                'balance_reports.report_date',
                'balance_reports.created_at',
                'users.name',
                'balance_reports.name_user',
                'balance_reports.text'
            )
            ->selectRaw('
                balance_reports.name_user,
                balance_reports.report_date,
                balance_reports.created_at,
                users.name as created_by,
                balance_reports.text,
                balance_reports.user_id,
                SUM(balance_reports.deposit) as total_deposit,
                SUM(balance_reports.withdraw) as total_withdraw,
                SUM(balance_reports.adjustment) as total_adjustment,
                SUM(balance_reports.balance) as total_balance
            ')
            ->orderByDesc('balance_reports.report_date')
            ->get();
       
        return view('admin.account-report.transation-usd', compact('data'));
    }
}