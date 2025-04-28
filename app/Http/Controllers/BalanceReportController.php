<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\BetLotteryPackage;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

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
        $data = User::with('package','roles','userWallet')->where('record_status_id','=',1)->orderBy('id','ASC')->get();
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
}
