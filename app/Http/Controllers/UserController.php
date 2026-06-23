<?php

namespace App\Http\Controllers;

use App\Models\AccountKH;
use App\Models\AccountKHUSD;
use App\Models\AccountUSD;
use App\Models\AccountVND;
use App\Models\BetLotteryPackage;
use App\Models\BetLotteryPackageConfiguration;
use App\Models\ManagerBetType;
use App\Models\User;
use App\Models\UserBetLimit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    private const SUPERVISOR_ROLES  = ['admin', 'master', 'agent'];
    private const BACK_OFFICE_ROLES = ['operator', 'finance', 'support', 'auditor'];

    public function __construct()
    {
        view()->share('packages', BetLotteryPackage::all());
        view()->share('betTypeOptions', ManagerBetType::OPTIONS);
    }

    // ─── CRUD ─────────────────────────────────────────────────────────────────

    public function index()
    {
        /** @var \App\Models\User $auth */
        $auth = Auth::user();

        $query = User::with('roles', 'manager', 'master')
            ->whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))
            ->orderBy('id');

        if ($auth->hasRole('master')) {
            // Master sees only their own tree; back-office users have no master_id so they're excluded
            $query->where('master_id', $auth->id);
        } elseif ($auth->hasRole('agent')) {
            // Agent sees only their direct members
            $query->where('manager_id', $auth->id);
        }
        // admin: no extra filter — sees everyone (betting users + back-office) except other admins

        $data = $query->get();
        return view('admin.user.index', compact('data'));
    }

    public function create()
    {
        /** @var \App\Models\User $creator */
        $creator = Auth::user();

        if ($creator->hasRole('admin')) {
            $roles          = Role::whereIn('name', array_merge(
                ['master', 'agent'],
                self::BACK_OFFICE_ROLES
            ))->get();
            $betTypeOptions = ManagerBetType::OPTIONS;
        } elseif ($creator->hasRole('master')) {
            $roles          = Role::whereIn('name', ['agent', 'member'])->get();
            $betTypeOptions = array_values(array_filter(
                ManagerBetType::OPTIONS,
                fn($opt) => $opt['bet_system'] === $creator->bet_system
                         && $opt['currency']   === $creator->currency
            )) ?: ManagerBetType::OPTIONS;
        } elseif ($creator->hasRole('agent')) {
            $roles          = Role::where('name', 'member')->get();
            $betTypeOptions = array_values(array_filter(
                ManagerBetType::OPTIONS,
                fn($opt) => $opt['bet_system'] === $creator->bet_system
                         && $opt['currency']   === $creator->currency
            ));
        } else {
            abort(403);
        }

        $backOfficeRoles = self::BACK_OFFICE_ROLES;
        return view('admin.user.create', compact('roles', 'betTypeOptions', 'backOfficeRoles'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $creator */
        $creator      = Auth::user();
        $targetRole   = $request->role;
        $isBackOffice = in_array($targetRole, self::BACK_OFFICE_ROLES);

        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'username'    => ['required', 'unique:users,username'],
            'password'    => ['required', 'min:6', 'max:255'],
            'phonenumber' => ['required'],
            'role'        => ['required'],
        ];
        if (! $isBackOffice) {
            $rules['package_id'] = ['required'];
            $rules['bet_types']  = ['required', 'array', 'min:1'];
        }
        $request->validate($rules);

        // Resolve bet_system + currency (betting users only)
        if ($isBackOffice) {
            $betSystem = null;
            $currency  = null;
        } elseif ($creator->hasRole('agent')) {
            $betSystem = $creator->bet_system;
            $currency  = $creator->currency;
        } else {
            $btRaw     = $request->input('bet_types', [])[0] ?? 'vietnam_VND';
            $btParts   = explode('_', $btRaw, 2);
            $betSystem = $btParts[0] ?? 'vietnam';
            $currency  = $btParts[1] ?? 'VND';
        }

        [$managerId, $masterId] = $isBackOffice ? [null, null] : $this->resolveHierarchy($creator, $targetRole);

        $user = User::create([
            'package_id'       => $isBackOffice ? null : $request->package_id,
            'manager_id'       => $managerId,
            'master_id'        => $masterId,
            'bet_system'       => $betSystem,
            'currency'         => $currency,
            'name'             => $request->name,
            'username'         => $request->username,
            'email'            => $request->username . '@gmail.com',
            'phonenumber'      => $request->phonenumber,
            'password'         => bcrypt($request->password),
            'record_status_id' => 1,
            'is_active'        => 1,
            'created_by'       => $creator->id,
        ]);

        $user->assignRole($targetRole);

        if (! $isBackOffice) {
            $acctModel = $this->resolveAccountModel($betSystem, $currency);
            $acctModel::firstOrCreate(
                ['user_id' => $user->id],
                ['credit_balance' => $request->available_credit ?? 0, 'record_status_id' => 1, 'created_by' => $creator->id]
            );
            $this->seedDefaultBetLimits($user->id);
        }

        return redirect()->route('admin.user.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        /** @var \App\Models\User $auth */
        $auth = Auth::user();

        if ($auth->hasRole('admin')) {
            $roles = Role::whereIn('name', array_merge(
                ['master', 'agent', 'member'],
                self::BACK_OFFICE_ROLES
            ))->get();
        } elseif ($auth->hasRole('master')) {
            $roles = Role::whereIn('name', ['agent', 'member'])->get();
        } elseif ($auth->hasRole('agent')) {
            $roles = Role::where('name', 'member')->get();
        } else {
            abort(403);
        }

        $user = User::with('roles')->findOrFail(decrypt($id));

        // Current bet type as a single key string e.g. 'vietnam_VND'
        $selectedBetTypes = ($user->bet_system && $user->currency)
            ? [$user->bet_system . '_' . $user->currency]
            : [];

        $acctModel = $this->resolveAccountModel($user->bet_system ?? 'vietnam', $user->currency ?? 'VND');
        $acctRow   = $acctModel::where('user_id', $user->id)->first();

        $user->total_bet_credit       = $acctRow?->credit_balance ?? 0;
        $user->total_available_credit = $acctRow?->credit_balance ?? 0;

        $betTypeOptions = ManagerBetType::OPTIONS;

        return view('admin.user.edit', compact('user', 'roles', 'selectedBetTypes', 'betTypeOptions'));
    }

    public function update(Request $request, User $user)
    {
        $targetRole   = $request->role;
        $isBackOffice = in_array($targetRole, self::BACK_OFFICE_ROLES);

        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'phonenumber' => ['required'],
            'username'    => ['required', Rule::unique('users')->ignore($user->id)],
            'role'        => ['required', 'string'],
        ];
        if (! $isBackOffice) {
            $rules['package_id'] = ['required'];
        }
        $request->validate($rules);

        $user->name        = $request->name;
        $user->username    = $request->username;
        $user->phonenumber = $request->phonenumber;
        if (! $isBackOffice) {
            $user->package_id = $request->package_id;
        }
        $user->save();

        if (! $isBackOffice) {
            $btRaw     = $request->input('bet_types', [])[0] ?? ($user->bet_system . '_' . $user->currency);
            $btParts   = explode('_', $btRaw, 2);
            $betSystem = $btParts[0] ?? $user->bet_system ?? 'vietnam';
            $currency  = $btParts[1] ?? $user->currency  ?? 'VND';

            $acctModel = $this->resolveAccountModel($betSystem, $currency);
            $account   = $acctModel::where('user_id', $user->id)->first();
            if ($account) {
                $account->credit_balance = $request->available_credit ?? $account->credit_balance;
                $account->save();
            } else {
                $acctModel::create([
                    'user_id'          => $user->id,
                    'credit_balance'   => $request->available_credit ?? 0,
                    'record_status_id' => 1,
                ]);
            }
            $user->bet_system = $betSystem;
            $user->currency   = $currency;
            $user->save();
        }

        $user->syncRoles([$targetRole]);

        return redirect()->route('admin.user.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        User::where('id', decrypt($id))->update(['record_status_id' => 0]);
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    // ─── Settings & profile ───────────────────────────────────────────────────

    public function show($id)
    {
        $user      = User::findOrFail($id);
        $settings  = UserBetLimit::where('user_id', $id)->get();
        $digitKeys = ['2D' => '2D', '3D' => '3D', '4D' => '4D', 'RP2' => 'PL2', 'RP3' => 'PL3'];

        $formatted = [];
        foreach ($digitKeys as $dbKey => $displayLabel) {
            $existing          = $settings->where('digit_key', $dbKey)->first();
            $formatted[$dbKey] = [
                'label'   => $displayLabel,
                'data'    => $existing,
                'min_bet' => $existing?->min_bet,
                'max_bet' => $existing?->max_bet,
            ];
        }

        return view('admin.user.show', compact('user', 'formatted'));
    }

    public function saveSetting(Request $request)
    {
        $request->validate([
            'id'            => ['required', 'exists:users,id'],
            'min_digit_2'   => ['nullable', 'numeric', 'min:0'],
            'max_digit_2'   => ['nullable', 'numeric', 'min:0'],
            'min_digit_3'   => ['nullable', 'numeric', 'min:0'],
            'max_digit_3'   => ['nullable', 'numeric', 'min:0'],
            'min_digit_4'   => ['nullable', 'numeric', 'min:0'],
            'max_digit_4'   => ['nullable', 'numeric', 'min:0'],
            'min_digit_rp2' => ['nullable', 'numeric', 'min:0'],
            'max_digit_rp2' => ['nullable', 'numeric', 'min:0'],
            'min_digit_rp3' => ['nullable', 'numeric', 'min:0'],
            'max_digit_rp3' => ['nullable', 'numeric', 'min:0'],
        ]);

        $digitMappings = [
            'digit_2'   => '2D',
            'digit_3'   => '3D',
            'digit_4'   => '4D',
            'digit_rp2' => 'RP2',
            'digit_rp3' => 'RP3',
        ];

        foreach ($digitMappings as $fieldKey => $dbDigitKey) {
            $min = $request->input('min_' . $fieldKey);
            $max = $request->input('max_' . $fieldKey);

            if (is_null($min) && is_null($max)) continue;

            if (!is_null($min) && !is_null($max) && $max < $min) {
                return redirect()->back()
                    ->withErrors(['error' => "Max bet for {$dbDigitKey} must be ≥ min bet"])
                    ->withInput();
            }

            UserBetLimit::updateOrCreate(
                ['user_id' => $request->id, 'digit_key' => $dbDigitKey],
                ['min_bet' => $min, 'max_bet' => $max]
            );
        }

        return redirect()->route('admin.user.index')->with('success', 'Betting limits updated successfully.');
    }

    public function editPassword(User $user)
    {
        return view('admin.user.change-password', compact('user'));
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'your_password' => ['required'],
            'new_password'  => ['required', 'min:6'],
        ]);

        if (!Hash::check($request->your_password, Auth::user()->password)) {
            return back()->withErrors(['your_password' => 'Your current password is incorrect.']);
        }

        $user->update(['password' => bcrypt($request->new_password)]);

        return redirect()->route('admin.user.index')->with('success', 'Password updated successfully.');
    }

    public function suspendUser(User $user)
    {
        return view('admin.user.suspend', compact('user'));
    }

    public function processSuspendUser(Request $request, User $user)
    {
        $request->validate([
            'your_password'  => ['required'],
            'suspend_status' => ['required', 'in:0,1'],
        ]);

        if (!Hash::check($request->your_password, Auth::user()->password)) {
            return back()->withErrors(['your_password' => 'Your current password is incorrect.']);
        }

        $user->is_active  = $request->suspend_status;
        $user->updated_by = Auth::id();
        $user->save();

        return redirect()->route('admin.user.index')->with('success', 'User status updated.');
    }

    // ─── Sub-lists ────────────────────────────────────────────────────────────

    public function usersUnderManager($manager_id)
    {
        $managerName = User::findOrFail($manager_id);

        $accountSub = DB::table('account_vnd')->select('user_id', 'credit_balance')
            ->unionAll(DB::table('account_usd')->select('user_id', 'credit_balance'))
            ->unionAll(DB::table('account_kh_vnd')->select('user_id', 'credit_balance'))
            ->unionAll(DB::table('account_kh_usd')->select('user_id', 'credit_balance'));

        $data = User::select(
                'users.*',
                DB::raw('COALESCE(acc.credit_balance, 0) AS total_bet_credit'),
                DB::raw('COALESCE(acc.credit_balance, 0) AS total_available_credit')
            )
            ->leftJoinSub($accountSub, 'acc', 'users.id', '=', 'acc.user_id')
            ->where('users.manager_id', $manager_id)
            ->orderBy('users.id')
            ->with(['package', 'roles', 'manager'])
            ->get();

        return view('admin.user.under-manager', compact('data', 'managerName'));
    }

    public function viewPackageLotto($id)
    {
        $package = User::findOrFail(decrypt($id));
        $bpCode  = BetLotteryPackage::findOrFail($package->package_id)->package_code;

        $data = BetLotteryPackageConfiguration::select([
            DB::raw("CASE
                WHEN bet_type = 'RP3' AND has_special = 0 THEN 'PL3'
                WHEN bet_type IN ('2D', '3D', '4D') THEN bet_type
                ELSE 'PL2' END AS bet_type"),
            DB::raw("CASE
                WHEN bet_type IN ('RP2', 'RP3', 'RP4', '2D', '3D', '4D') THEN rate
                ELSE NULL END AS bet_rate"),
            DB::raw("CASE
                WHEN bet_type IN ('RP2', 'RP3', 'RP4', '2D', '3D', '4D') THEN price
                ELSE NULL END AS bet_price"),
        ])
        ->where('package_id', $package->package_id)
        ->orderBy('bet_type')
        ->get();

        return view('admin.user.package-view', compact('data', 'package', 'bpCode'));
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Resolve [manager_id, master_id] for a new user based on who is creating them.
     *
     * Hierarchy: admin → master → agent → member
     *
     * - master:  no parent (manager_id = null, master_id = null)
     * - agent:   parent is the master who created them
     * - member:  parent is the agent (or master) who created them
     */
    private function resolveHierarchy(User $creator, string $targetRole = ''): array
    {
        if ($creator->hasRole('admin')) {
            // Masters created by admin have no parent hierarchy
            // Agents created directly by admin also have no master
            return [null, null];
        }

        if ($creator->hasRole('master')) {
            // Both agents and members created by master trace back to this master
            return [$creator->id, $creator->id];
        }

        // Agent creating a member: manager = agent, master = agent's master
        return [$creator->id, $creator->master_id];
    }

    private function resolveAccountModel(string $betSystem, string $currency): string
    {
        $sys = strtolower($betSystem);
        $cur = strtoupper($currency);

        if ($sys === 'khmer' && $cur === 'USD') return AccountKHUSD::class;
        if ($sys === 'khmer')                   return AccountKH::class;
        if ($cur === 'USD')                     return AccountUSD::class;
        return AccountVND::class;
    }

    private function seedDefaultBetLimits(int $userId): void
    {
        $defaults = [
            '2D'  => ['min_bet' => 0.10, 'max_bet' => 500],
            '3D'  => ['min_bet' => 0.10, 'max_bet' => 250],
            '4D'  => ['min_bet' => 0.10, 'max_bet' => 50],
            'RP2' => ['min_bet' => 0.10, 'max_bet' => 120],
            'RP3' => ['min_bet' => 0.10, 'max_bet' => 50],
        ];

        foreach ($defaults as $key => $limits) {
            UserBetLimit::updateOrCreate(
                ['user_id' => $userId, 'digit_key' => $key],
                $limits
            );
        }
    }

}
