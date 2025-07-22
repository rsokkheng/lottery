<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserBetLimit;
use App\Models\UserCurrency;
use Illuminate\Http\Request;
use App\Models\BalanceReport;
use Illuminate\Validation\Rule;
use App\Models\AccountManagement;
use App\Models\BetLotteryPackage;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\BetLotteryPackageConfiguration;

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
        $currentCurrency = $user->currencies()->first();
        if (!$currentCurrency) {
            $data = collect();
        } else {
            if ($user->hasRole('admin')) {
                $data = User::with('package', 'roles', 'manager', 'accountManagement', 'currencies')
                    ->whereHas('currencies', function ($query) use ($currentCurrency) {
                        $query->where('currency', $currentCurrency->currency);
                    })
                    ->whereDoesntHave('roles', function ($query) {
                        $query->whereIn('name', ['member', 'admin']);
                    })
                    ->orderBy('id', 'ASC')
                    ->get();
            } elseif ($user->hasRole('manager')) {
                $data = User::select(
                        'users.*',
                        DB::raw('COALESCE(SUM(account_management.bet_credit), 0) AS total_bet_credit'),
                        DB::raw('COALESCE(SUM(account_management.available_credit), 0) AS total_available_credit')
                    )
                    ->leftJoin('account_management', 'account_management.user_id', '=', 'users.id')
                    ->where('users.manager_id', $user->id)
                    ->whereHas('currencies', function ($query) use ($currentCurrency) {
                        $query->where('currency', $currentCurrency->currency);
                    })
                    ->whereDoesntHave('roles', function ($query) {
                        $query->where('name', 'admin');
                    })
                    ->groupBy('users.id')
                    ->orderBy('users.id', 'ASC')
                    ->get();
            }
            
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
            'net_lose' => 0,
            'net_win' => 0,
            'deposit' => 0,
            'withdraw' => 0,
            'adjustment' => 0,
            'balance' => 0,
            'report_date' => Carbon::today()->format('Y-m-d'),
        ]);
        if($user){
                $defaultSettings = [
                    '2D'  => ['min_bet' => 0.10, 'max_bet' => 500],
                    '3D'  => ['min_bet' => 0.10, 'max_bet' => 250],
                    '4D'  => ['min_bet' => 0.10, 'max_bet' => 50],
                    'RP2' => ['min_bet' => 0.10, 'max_bet' => 120],
                    'RP3' => ['min_bet' => 0.10, 'max_bet' => 50],
                ];
                
                foreach ($defaultSettings as $digitKey => $settings) {
                    UserBetLimit::updateOrCreate(
                        [
                            'user_id'   => $user->id,
                            'digit_key' => $digitKey,
                        ],
                        [
                            'min_bet' => $settings['min_bet'],
                            'max_bet' => $settings['max_bet'],
                        ]
                    );
            }
        }

        $user->assignRole($request->role);
        return redirect()->route('admin.user.index')->with('success','User created successfully.');
    }
    public function edit($id)
        {
            $currentUser = auth()->user();
            
            // Filter roles based on current user's role
            if ($currentUser->hasRole('admin')) {
                $roles = Role::whereIn('name', ['manager', 'member'])->get();
            } elseif ($currentUser->hasRole('manager')) {
                $roles = Role::where('name', 'member')->get();
            } else {
                $roles = collect(); // Empty collection if no permissions
            }
            
            $user = User::with('roles')
                ->withSum('accountManagement as total_bet_credit', 'bet_credit')
                ->withSum('accountManagement as total_available_credit', 'available_credit')
                ->findOrFail(decrypt($id));

            return view('admin.user.edit', compact('user', 'roles'));
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
    public function show($id)
    {
        $user = User::where('id', $id)->first();
        
        // Get existing settings for this user
        $settings = UserBetLimit::where('user_id', $id)->get();
        
        // Define the mapping between database digit_key and display labels
        $digitKeys = [
            '2D'  => '2D',
            '3D'  => '3D', 
            '4D'  => '4D',
            'RP2' => 'PL2',
            'RP3' => 'PL3',
        ];
        
        // Format the settings for the view
        $formatted = [];
        foreach ($digitKeys as $dbKey => $displayLabel) {
            // Find existing setting for this digit type
            $existingSetting = $settings->where('digit_key', $dbKey)->first();
            
            $formatted[$dbKey] = [
                'label' => $displayLabel,
                'data'  => $existingSetting, // This will be null if no setting exists
                'min_bet' => $existingSetting ? $existingSetting->min_bet : null,
                'max_bet' => $existingSetting ? $existingSetting->max_bet : null,
            ];
        }
        return view('admin.user.show', compact('user', 'formatted'));
    }

public function saveSetting(Request $request)
{
    $data = $request->all();
    
    // Validate the request
    $request->validate([
        'id' => 'required|exists:users,id',
        'min_digit_2' => 'nullable|numeric|min:0',
        'max_digit_2' => 'nullable|numeric|min:0',
        'min_digit_3' => 'nullable|numeric|min:0',
        'max_digit_3' => 'nullable|numeric|min:0',
        'min_digit_4' => 'nullable|numeric|min:0',
        'max_digit_4' => 'nullable|numeric|min:0',
        'min_digit_rp2' => 'nullable|numeric|min:0',
        'max_digit_rp2' => 'nullable|numeric|min:0',
        'min_digit_rp3' => 'nullable|numeric|min:0',
        'max_digit_rp3' => 'nullable|numeric|min:0',
    ]);
    
    // Define the mapping between form fields and database values
    $digitMappings = [
        'digit_2'   => '2D',
        'digit_3'   => '3D', 
        'digit_4'   => '4D',
        'digit_rp2' => 'RP2',
        'digit_rp3' => 'RP3',
    ];
    
    foreach ($digitMappings as $fieldKey => $dbDigitKey) {
        $minKey = 'min_' . $fieldKey; // min_digit_2, min_digit_rp2, etc.
        $maxKey = 'max_' . $fieldKey; // max_digit_2, max_digit_rp2, etc.
        
        $minValue = $data[$minKey] ?? null;
        $maxValue = $data[$maxKey] ?? null;
        
        // Skip if both values are empty
        if (is_null($minValue) && is_null($maxValue)) {
            continue;
        }
        
        // Validate that max is greater than min if both are provided
        if (!is_null($minValue) && !is_null($maxValue) && $maxValue < $minValue) {
            return redirect()->back()
                ->withErrors(['error' => "Maximum bet for {$dbDigitKey} must be greater than minimum bet"])
                ->withInput();
        }
        
        // Create or update UserBetLimit
        UserBetLimit::updateOrCreate(
            [
                'user_id'   => $request->id,
                'digit_key' => $dbDigitKey, // Store as '2D', '3D', etc.
            ],
            [
                'min_bet' => $minValue,
                'max_bet' => $maxValue,
            ]
        );
    }
    
    return redirect()->route('admin.user.index')
        ->with('success', 'Betting limits updated successfully.');
}

// Alternative method if you want to handle default package logic
public function saveSettingWithPackageType(Request $request)
{
    $data = $request->all();
    $packageType = $request->input('package_type', 'custom');
    
    // If default package is selected, you might want to set predefined values
    if ($packageType === 'default') {
        $defaultSettings = [
            '2D'  => ['min_bet' => 0, 'max_bet' => 0],
            '3D'  => ['min_bet' => 0, 'max_bet' => 0],
            '4D'  => ['min_bet' => 0, 'max_bet' => 0],
            'RP2' => ['min_bet' => 0, 'max_bet' => 0],
            'RP3' => ['min_bet' => 0, 'max_bet' => 0],
        ];
        
        foreach ($defaultSettings as $digitKey => $settings) {
            UserBetLimit::updateOrCreate(
                [
                    'user_id'   => $request->id,
                    'digit_key' => $digitKey,
                ],
                [
                    'min_bet' => $settings['min_bet'],
                    'max_bet' => $settings['max_bet'],
                ]
            );
        }
    } else {
        // Use the custom logic from above
        $digitMappings = [
            'digit_2'   => '2D',
            'digit_3'   => '3D', 
            'digit_4'   => '4D',
            'digit_rp2' => 'RP2',
            'digit_rp3' => 'RP3',
        ];
        
        foreach ($digitMappings as $fieldKey => $dbDigitKey) {
            $minKey = 'min_' . $fieldKey;
            $maxKey = 'max_' . $fieldKey;
            
            $minValue = $data[$minKey] ?? null;
            $maxValue = $data[$maxKey] ?? null;
            
            if (is_null($minValue) && is_null($maxValue)) {
                continue;
            }
            
            UserBetLimit::updateOrCreate(
                [
                    'user_id'   => $request->id,
                    'digit_key' => $dbDigitKey,
                ],
                [
                    'min_bet' => $minValue,
                    'max_bet' => $maxValue,
                ]
            );
        }
    }
    
    return redirect()->route('admin.user.index')
        ->with('success', 'Betting limits updated successfully.');
}

public function editPassword(User $user)
{
    return view('admin.user.change-password', compact('user'));
}

public function updatePassword(Request $request, User $user)
{
    $request->validate([
        'your_password' => ['required'],
        'new_password' => ['required', 'min:6'],
    ]);

    if (!Hash::check($request->your_password, auth()->user()->password)) {
        return back()->withErrors(['your_password' => 'Your current password is incorrect.']);
    }

    $user->password = bcrypt($request->new_password);
    $user->save();

    return redirect()->route('admin.user.index')->with('success', 'Password updated successfully.');
}
public function usersUnderManager($manager_id)
{
    $managerName = User::find($manager_id);
    $data = User::select(
        'users.*',
        DB::raw('COALESCE(SUM(account_management.bet_credit), 0) AS total_bet_credit'),
        DB::raw('COALESCE(SUM(account_management.available_credit), 0) AS total_available_credit')
    )
    ->leftJoin('account_management', 'users.id', '=', 'account_management.user_id')
    ->where('users.manager_id', $manager_id)
    ->groupBy('users.id')  // Important: group by user.id to sum per user
    ->orderBy('users.id', 'ASC')
    ->with(['package', 'roles', 'manager', 'accountManagement', 'currencies'])
    ->get();
    return view('admin.user.under-manager', compact('data','managerName'));
}

public function viewPackageLotto($id)
    {
        $package = User::findOrFail(decrypt($id));
        $bpCode = BetLotteryPackage::findOrFail($package->package_id)->package_code;
        $data = BetLotteryPackageConfiguration::select([
            DB::raw("
                CASE
                    WHEN bet_type = 'RP3' AND has_special = 0 THEN 'PL3'
                    WHEN bet_type IN ('2D', '3D', '4D') THEN bet_type
                    ELSE 'PL2'
                END AS bet_type
            "),
            DB::raw("
                CASE
                    WHEN bet_type IN ('RP2', 'RP3', 'RP4', '2D', '3D', '4D') THEN rate
                    ELSE NULL
                END AS bet_rate
            "),
            DB::raw("
                CASE
                    WHEN bet_type IN ('RP2', 'RP3', 'RP4', '2D', '3D', '4D') THEN price
                    ELSE NULL
                END AS bet_price
            "),
        ])
        ->where('package_id', $package->package_id)
        ->orderBy('bet_type', 'asc')
        ->get();
        return view('admin.user.package-view', compact('data','package','bpCode'));
    }
}
