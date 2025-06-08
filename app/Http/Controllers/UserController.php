<?php

namespace App\Http\Controllers;

use App\Models\UserCurrency;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\BalanceReport;
use Illuminate\Validation\Rule;
use App\Models\AccountManagement;
use App\Models\BetLotteryPackage;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $packages = BetLotteryPackage::all();
        view()->share('packages', $packages);
    }
    public function index()
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            // For admin: show all users
            $data = User::with('package', 'roles','manager','accountManagement')
                        ->orderBy('id', 'ASC')
                        ->get();
        } elseif ($user->hasRole('manager')) {
            // For manager: show only users under the manager and exclude admins
        $data = User::with('package', 'roles','manager','accountManagement')
                    ->where('manager_id', '=', $user->id)  // Only show users managed by this manager
                    ->whereDoesntHave('roles', function($query) {
                        $query->where('name', 'admin');  // Exclude admin role
                    })->orderBy('id', 'ASC')->get();
        }
        return view('admin.user.index', compact('data'));
    }
    public function create()
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            $roles = Role::where('name', 'manager')->get();
        } elseif ($user->hasRole('manager')) {
            $roles = Role::where('name', 'member')->get();
        } else {
            $roles = collect(); // or handle unauthorized
        }
        return view('admin.user.create',compact('roles'));
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
            'manager_id' => Auth::user()->id??0,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username.'@gmail.com',
            'phonenumber' => $request->phonenumber,
            'password' => bcrypt($request->password),
        ]);

        UserCurrency::create([
            'user_id' => $user->id,
            'currency' => $request->currency ?? null,
        ]);
        AccountManagement::create([
            'user_id' => $user->id,
            'name_user' => $user->name,
            'available_credit' => $request->available_credit,
            'bet_credit' => $request->available_credit,
            'cash_balance' => 0,
            'currency' => $request->currency ?? null, // default fallback
        ]);
        BalanceReport::create([
            'user_id' => $user->id,
            'name_user' => $user->name,
            'beginning' => 0,
            'net_lose' => 0,
            'net_win' => 0,
            'deposit' => 0,
            'withdraw' => 0,
            'adjustment' => 0,
            'balance' => 0,
            'outstanding' => 0,
            'report_date' => Carbon::today()->format('Y-m-d'),
        ]);

        $user->assignRole($request->role);
        return redirect()->route('admin.user.index')->with('success','User created successfully.');
    }
    public function edit($id)
    {
        $roles = Role::all();
        $user = User::with('accountManagement')->where('id',decrypt($id))->first();
        return view('admin.user.edit',compact('user','roles'));
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

        $account = AccountManagement::where('user_id', $user->id)->first();
        if ($account) {
            $oldCredit = $account->available_credit;
            $newCredit = $request->available_credit;

            // Adjust bet_credit based on increase or decrease
            if ($newCredit > $oldCredit) {
                $account->available_credit = $newCredit;
                $account->bet_credit += ($newCredit - $oldCredit);
            } elseif ($newCredit < $oldCredit) {
                $account->available_credit = $newCredit;
                $account->bet_credit -= ($oldCredit - $newCredit);
            }else{
                $account->available_credit = $newCredit;
                $account->bet_credit -= $newCredit;
            }
            $account->cash_balance = 0;
            $account->currency = $request->currency ?? null;
            $account->save();
        }
        else {
            // Optionally create it if not exists
            AccountManagement::create([
                'user_id' => $user->id,
                'name_user' => $user->name,
                'available_credit' => $request->available_credit,
                'bet_credit' => $request->available_credit,
                'cash_balance' => 0,
                'currency' => $request->currency ?? null,
            ]);
        }
        $user->assignRole($request->role);
        return redirect()->route('admin.user.index')->with('success','User updated successfully.');
    }
    public function destroy($id)
    {
        User::where('id', decrypt($id))->update(['record_status_id' => 0]);
        return redirect()->back()->with('success','User deleted successfully.');
    }
}
