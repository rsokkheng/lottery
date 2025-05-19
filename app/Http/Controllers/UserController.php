<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\BetLotteryPackage;
use App\Models\BetUserWallet;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

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
            $data = User::with('package', 'roles', 'userWallet','manager')
                        ->where('record_status_id', '=', 1)
                        ->orderBy('id', 'ASC')
                        ->get();
        } elseif ($user->hasRole('manager')) {
            // For manager: show only users under the manager and exclude admins
            $data = User::with('package', 'roles', 'userWallet','manager')
                        ->where('record_status_id', '=', 1)
                        ->where('manager_id', '=', $user->id)  // Only show users managed by this manager
                        ->whereDoesntHave('roles', function($query) {
                            $query->where('name', 'admin');  // Exclude admin role
                        })
                        ->orderBy('id', 'ASC')
                        ->get();
        
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

        BetUserWallet::create([
            'user_id' => $user->id,
            'currency' => "VDN",
            'status' => "Active",
            'beginning' => 0,
            'net_win_loss' => 0,
            'deposit' => 0,
            'withdraw' => 0,
            'balance' => 0,
            'given_credit' => 0,
            'available_credit' => 0,
            'adjustment' => 0,
            'outstanding' => 0,
        ]);
        
        $user->assignRole($request->role);
        return redirect()->route('admin.user.index')->with('success','User created successfully.');
    }
    public function edit($id)
    {
        $user = User::where('id',decrypt($id))->first();
        return view('admin.user.edit',compact('user'));
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
