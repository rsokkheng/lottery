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

        $supervisorRoles = ['master', 'agent'];
        $isSupervisor    = !empty(array_intersect($supervisorRoles, $roles));

        $memberQuery = User::with(['accountKH', 'accountKHUSD', 'manager'])
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', array_merge(['admin'], $supervisorRoles)));

        if (!in_array('admin', $roles)) {
            if (in_array('master', $roles)) {
                $memberQuery->where('master_id', $user->id);
            } elseif (in_array('agent', $roles)) {
                $memberQuery->where('manager_id', $user->id);
            }
        }

        $members = $memberQuery->orderBy('name')->get();

        // Per-member win/loss stats for selected date
        $winSubQuery = DB::table('bet_number_kh_vnd as bn')
            ->join('bet_winning_kh_vnd as bw', 'bw.bet_number_id', '=', 'bn.id')
            ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win'))
            ->groupBy('bn.bet_id');

        $statsQuery = DB::table('bet_kh_vnd')
            ->select(
                'bet_kh_vnd.user_id',
                DB::raw('SUM(bet_kh_vnd.total_amount) as turnover'),
                DB::raw('SUM(bet_kh_vnd.total_amount * pkg.rate / 100) as net_amount'),
                DB::raw('SUM(IFNULL(win_sub.total_win, 0)) as compensate')
            )
            ->leftJoinSub($winSubQuery, 'win_sub', fn($j) => $j->on('win_sub.bet_id', '=', 'bet_kh_vnd.id'))
            ->join('bet_package_configurations as pkg', 'pkg.id', '=', 'bet_kh_vnd.bet_package_config_id')
            ->whereDate('bet_kh_vnd.bet_date', $date)
            ->whereIn('bet_kh_vnd.user_id', $members->pluck('id'));

        if (!in_array('admin', $roles)) {
            if (in_array('master', $roles)) {
                $statsQuery->whereIn('bet_kh_vnd.user_id', function ($sub) use ($user) {
                    $sub->select('id')->from('users')->where('master_id', $user->id);
                });
            } elseif (in_array('agent', $roles)) {
                $statsQuery->whereIn('bet_kh_vnd.user_id', function ($sub) use ($user) {
                    $sub->select('id')->from('users')->where('manager_id', $user->id);
                });
            }
        }

        $stats = $statsQuery->groupBy('bet_kh_vnd.user_id')->get()->keyBy('user_id');

        // Outstanding: today's unsettled bets (exclude companies that already have results)
        $settledCompanyIds = DB::table('bet_lottery_results')
            ->join('bet_lottery_schedules', 'bet_lottery_schedules.id', '=', 'bet_lottery_results.lottery_schedule_id')
            ->whereDate('bet_lottery_results.draw_date', Carbon::today())
            ->pluck('bet_lottery_schedules.company_id')
            ->unique()
            ->toArray();

        return view('admin.credit-kh.index', compact('members', 'roles', 'date', 'stats'));
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
