<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\BalanceReport;
use Illuminate\Validation\Rule;
use App\Models\AccountManagement;
use App\Models\BetLotteryPackage;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BalanceReportController extends Controller
{
    public function __construct()
    {
        $roles = Role::all();
        $packages = BetLotteryPackage::all();
        view()->share('roles',$roles);
        view()->share('packages', $packages);
    }
    public function index()
    { 
        $user = Auth::user();
        if (($user->hasRole('admin') || $user->hasRole('manager')) &&
            $user->currencies()->first()->currency === 'VND') {
            $currency = "VND";
        }else{
            $currency = "USD";
        }
        $data = DB::table('users as u')
        ->join('user_currencies as cu', 'u.id', '=', 'cu.user_id')
        ->join(DB::raw('
            (SELECT user_id, MIN(id) as account_id, SUM(bet_credit) as total_bet_credit
             FROM account_management
             GROUP BY user_id
            ) as ac_grouped'), 'ac_grouped.user_id', '=', 'u.id')
        ->join('account_management as ac', 'ac.id', '=', 'ac_grouped.account_id')
        ->leftJoin(DB::raw('
            (SELECT user_id, SUM(amount) as amount
             FROM balance_report_outstandings
             GROUP BY user_id
            ) as ou'), 'ou.user_id', '=', 'u.id')
        ->leftJoin('balance_reports as br', function ($join) {
            $join->on('br.user_id', '=', 'u.id')
                ->whereDate('br.report_date', now()->toDateString());
        })
        ->select(
            'u.id as user_id',
            DB::raw('DATE(br.report_date) as report_date'),
            'u.username',
            'ac.id as balance_account_id',
            'u.record_status_id',
            DB::raw('COALESCE(SUM(br.net_lose), 0) as net_lose'),
            DB::raw('COALESCE(SUM(br.net_win), 0) as net_win'),
            DB::raw('COALESCE(SUM(br.deposit), 0) as deposit'),
            DB::raw('COALESCE(SUM(br.withdraw), 0) as withdraw'),
            DB::raw('COALESCE(SUM(br.adjustment), 0) as adjustment'),
            DB::raw('ac_grouped.total_bet_credit as bet_credit'),
            DB::raw('COALESCE(SUM(br.balance), 0) as balance'),
            DB::raw('COALESCE(ou.amount, 0) as outstanding')
        )
        ->where('cu.currency', $currency);
    
    if ($user->hasRole('manager')) {
        $data->where('u.manager_id', $user->id);
    }
    
    $data = $data->groupBy(
        'u.id',
        'u.username',
        'ac.id',
        'br.report_date',
        'u.record_status_id',
        'ac_grouped.total_bet_credit',
        'ou.amount'
    )->get();

        return view('admin.balance-report.index', compact('data'));
    }
    public function create()
    {
        return view('admin.balance-report.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required', 'string', 'max:255',
            'username' => 'required', 'unique:'.User::class,
            'password' => 'required|max:255|min:6',
            'package_id' => 'required',
            'phonenumber' => 'required',
            'role' => 'required'
        ]);
        $user = User::create([
            'package_id' => $request->package_id,
            'name' => $request->name,
            'username' => $request->username,
            'phonenumber' => $request->phonenumber,
            'password' => bcrypt($request->password),
        ]);
        
        $user->assignRole($request->role);
        return redirect()->route('admin.user.index')->with('success','User created successfully.');
    }
    public function edit($id)
    {
        $user = User::where('id',decrypt($id))->first();
        return view('admin.balance-report.edit',compact('user'));
    }
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'package_id' => ['required'],
            'phonenumber' => ['required'],
            'username' => ['required',  Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string']
        ]); 
        $user = User::find($request->id);
        $user->name = $request->name;
        $user->package_id = $request->package_id;
        $user->username = $request->username;
        $user->phonenumber = $request->phonenumber;
        $user->save();
        $user->assignRole($request->role);
        return redirect()->route('admin.user.index')->with('success','User updated successfully.');
    }
    public function destroy($id)
    {
        User::where('id', decrypt($id))->update(['record_status_id' => 0]);
        return redirect()->back()->with('success','User deleted successfully.');
    }
    public function handleTransaction(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'transaction_type' => 'required',
            'password' => 'required',
            'amount' => 'required|numeric|min:1',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->with('error', 'Invalid password');
        }
        $accountManagement = AccountManagement::findOrFail(decrypt($request->balance_account_id));
        if ($request->transaction_type === 'deposit') {
            $accountManagement->bet_credit    += $request->amount;
            $accountManagement->cash_balance  += $request->amount;
        } else {
            $accountManagement->bet_credit    -= $request->amount;
            $accountManagement->cash_balance  -= $request->amount;
        }
        $accountManagement->updated_by = Auth::user()->id ?? 0;
        $accountManagement->updated_at = now();
        $accountManagement->save();

        BalanceReport::create([
            'user_id' => decrypt($request->user_id),
            'name_user' => $request->name_user,
            'report_date' => Carbon::today()->format('Y-m-d'),
            'balance' => $request->amount,
            'net_lose' => 0,
            'net_win' => 0,
            'deposit' => $request->transaction_type === 'deposit' ? $request->amount : 0,
            'withdraw' => $request->transaction_type === 'withdraw' ? $request->amount : 0,
            'adjustment' => 0,
            'text' => $request->remark,
            'created_by' => Auth::user()->id ?? 0,
            'created_at' => now(),
        ]);
        return back()->with('success', ucfirst($request->transaction_type) . ' successful'); 
    }
    public function detailByUser($user_id,Request $request){
        $filter = $request->get('filter');

        $query = BalanceReport::leftJoin('users', 'users.id', '=', 'balance_reports.created_by')
        ->where('user_id', $user_id);
    
        switch ($filter) {
            case 'today':
                $query->whereDate('balance_reports.report_date', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('balance_reports.report_date', Carbon::yesterday());
                break;
            case 'this_week':
                $query->whereBetween('balance_reports.report_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week':
                $query->whereBetween('balance_reports.report_date', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek()
                ]);
                break;
            case 'this_month':
                $query->whereMonth('balance_reports.report_date', Carbon::now()->month)
                      ->whereYear('balance_reports.report_date', Carbon::now()->year);
                break;
            case 'last_month':
                $query->whereMonth('balance_reports.report_date', Carbon::now()->subMonth()->month)
                      ->whereYear('balance_reports.report_date', Carbon::now()->subMonth()->year);
                break;
        }
    
        $data = $query
        ->groupBy(
            'balance_reports.user_id',
            'balance_reports.report_date',
            'users.name',
            'balance_reports.text'
        )
        ->selectRaw('
            balance_reports.report_date,
            users.name as created_by,
            balance_reports.text,
            balance_reports.user_id,
            SUM(balance_reports.net_win) as total_net_win,
            SUM(balance_reports.net_lose) as total_net_lose,
            SUM(balance_reports.deposit) as total_deposit,
            SUM(balance_reports.withdraw) as total_withdraw,
            SUM(balance_reports.adjustment) as total_adjustment,
            SUM(balance_reports.balance) as total_balance
        ')
        ->orderByDesc('balance_reports.report_date')
        ->get();
        return view('admin.balance-report.detail', compact('data', 'user_id'));
    }
}