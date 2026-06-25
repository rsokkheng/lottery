<?php

namespace App\Http\Controllers;

use App\Models\AccountKH;
use App\Models\AccountKHUSD;
use App\Models\CreditTransactionKH;
use App\Models\CreditTransactionKHUSD;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreditKHController extends Controller
{
    private function getAccountModel(string $currency)
    {
        return match ($currency) {
            'usd' => AccountKHUSD::class,
            default => AccountKH::class,
        };
    }

    private function getTransactionModel(string $currency)
    {
        return match ($currency) {
            'usd' => CreditTransactionKHUSD::class,
            default => CreditTransactionKH::class,
        };
    }

    public function index(Request $request)
    {
        $user  = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();
        $date  = $request->input('date', Carbon::today()->format('Y-m-d'));

        // Determine currency: query param → user's own currency → default vnd
        $currency = strtolower($request->query('currency', $user->currency ?? 'vnd'));
        if (!in_array($currency, ['vnd', 'usd'])) {
            $currency = 'vnd';
        }

        $supervisorRoles = ['master', 'agent'];

        $eagerLoad = $currency === 'usd' ? ['accountKHUSD', 'manager'] : ['accountKH', 'manager'];

        $memberQuery = User::with($eagerLoad)
            ->where('currency', strtoupper($currency))
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', array_merge(['admin'], $supervisorRoles)));

        if (!in_array('admin', $roles)) {
            if (in_array('master', $roles)) {
                $memberQuery->where('master_id', $user->id);
            } elseif (in_array('agent', $roles)) {
                $memberQuery->where('manager_id', $user->id);
            }
        }

        $members = $memberQuery->orderBy('name')->get();

        // Per-member stats for selected date (currency-specific tables)
        $betTable    = $currency === 'usd' ? 'bet_kh_usd'        : 'bet_kh_vnd';
        $numTable    = $currency === 'usd' ? 'bet_number_kh_usd'  : 'bet_number_kh_vnd';
        $winTable    = $currency === 'usd' ? 'bet_winning_kh_usd' : 'bet_winning_kh_vnd';

        $winSubQuery = DB::table("{$numTable} as bn")
            ->join("{$winTable} as bw", 'bw.bet_number_id', '=', 'bn.id')
            ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win'))
            ->groupBy('bn.bet_id');

        $statsQuery = DB::table("{$betTable} as b")
            ->select(
                'b.user_id',
                DB::raw('SUM(b.total_amount) as turnover'),
                DB::raw('SUM(b.total_amount * pkg.rate / 100) as net_amount'),
                DB::raw('SUM(IFNULL(win_sub.total_win, 0)) as compensate')
            )
            ->leftJoinSub($winSubQuery, 'win_sub', fn($j) => $j->on('win_sub.bet_id', '=', 'b.id'))
            ->join('bet_package_configurations as pkg', 'pkg.id', '=', 'b.bet_package_config_id')
            ->whereDate('b.bet_date', $date)
            ->whereIn('b.user_id', $members->pluck('id'))
            ->groupBy('b.user_id');

        $stats = $statsQuery->get()->keyBy('user_id');

        return view('admin.credit-kh.index', compact('members', 'roles', 'date', 'stats', 'currency'));
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'user_id'  => 'required',
            'amount'   => 'required|numeric|min:0.01',
            'type'     => 'required|in:deposit,withdraw,adjustment',
            'currency' => 'required|in:vnd,usd',
            'password' => 'required|string',
            'note'     => 'nullable|string|max:255',
        ]);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->with('error', 'Invalid password. Transaction cancelled.');
        }

        $userId = decrypt($request->user_id);
        $currency = $request->currency;
        $AccountModel = $this->getAccountModel($currency);
        $TransactionModel = $this->getTransactionModel($currency);
        $currencyLabel = strtoupper($currency);

        DB::beginTransaction();
        try {
            $account = $AccountModel::firstOrCreate(
                ['user_id' => $userId],
                ['credit_balance' => 0, 'created_by' => Auth::id()]
            );

            $before = (float) $account->credit_balance;

            if (in_array($request->type, ['withdraw', 'adjustment'])) {
                if ($before < (float) $request->amount) {
                    DB::rollBack();
                    return back()->with('error', "Insufficient credit balance for withdrawal ({$currencyLabel}).");
                }
                $account->credit_balance -= $request->amount;
            } else {
                $account->credit_balance += $request->amount;
            }

            $account->updated_by = Auth::id();
            $account->save();

            $TransactionModel::create([
                'user_id'        => $userId,
                'type'           => $request->type,
                'amount'         => $request->amount,
                'balance_before' => $before,
                'balance_after'  => $account->credit_balance,
                'note'           => $request->note,
                'created_by'     => Auth::id(),
            ]);

            DB::commit();
            return back()->with('success', ucfirst($request->type) . ' of ' . number_format($request->amount, 2) . " {$currencyLabel} completed successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function history(int $userId, string $currency = 'vnd')
    {
        $member = User::findOrFail($userId);
        $AccountModel = $this->getAccountModel($currency);
        $TransactionModel = $this->getTransactionModel($currency);

        $account = $AccountModel::where('user_id', $userId)->first();
        $transactions = $TransactionModel::with('createdBy')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.credit-kh.history', compact('member', 'account', 'transactions', 'currency'));
    }
}
