<?php

namespace App\Http\Controllers;

use App\Models\AccountUSD;
use App\Models\AccountVND;
use App\Models\CreditTransactionUSD;
use App\Models\CreditTransactionVND;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreditFiatController extends Controller
{
    private function config(string $currency): array
    {
        return match (strtoupper($currency)) {
            'VND' => [
                'label'         => 'VND',
                'model'         => CreditTransactionVND::class,
                'account_model' => AccountVND::class,
                'bets_table'    => 'bets',
                'win_table'     => 'bet_winning',
            ],
            'USD' => [
                'label'         => 'USD',
                'model'         => CreditTransactionUSD::class,
                'account_model' => AccountUSD::class,
                'bets_table'    => 'bet_usd',
                'win_table'     => 'bet_winning_usd',
            ],
            default => abort(404),
        };
    }

    public function index(Request $request, string $currency)
    {
        $cfg   = $this->config($currency);
        /** @var \App\Models\User $auth */
        $auth  = Auth::user();
        $roles = $auth->roles->pluck('name')->toArray();
        $date  = $request->input('date', Carbon::today()->format('Y-m-d'));

        $supervisorRoles = ['master', 'agent'];

        // Members with the given currency (bet_system=vietnam, currency matches)
        $memberQuery = User::with(['manager'])
            ->where('bet_system', 'vietnam')
            ->where('currency', strtoupper($currency))
            ->whereDoesntHave('roles', fn($q) => $q->whereIn('name', array_merge(['admin'], $supervisorRoles)));

        if (!in_array('admin', $roles)) {
            if (in_array('master', $roles)) {
                $memberQuery->where('master_id', $auth->id);
            } elseif (in_array('agent', $roles)) {
                $memberQuery->where('manager_id', $auth->id);
            }
        }

        $members   = $memberQuery->with('manager')->orderBy('name')->get();
        $memberIds = $members->pluck('id');

        // Preload credit balances for all members
        $balances = $cfg['account_model']::whereIn('user_id', $memberIds)
            ->get()
            ->keyBy('user_id');

        // Turnover & net for selected date
        $statsQuery = DB::table($cfg['bets_table'] . ' as b')
            ->select(
                'b.user_id',
                DB::raw('SUM(b.total_amount) as turnover'),
                DB::raw('SUM(b.total_amount * pkg.rate / 100) as net_amount')
            )
            ->join('bet_package_configurations as pkg', 'pkg.id', '=', 'b.bet_package_config_id')
            ->whereDate('b.bet_date', $date)
            ->whereIn('b.user_id', $memberIds)
            ->groupBy('b.user_id');

        $stats = $statsQuery->get()->keyBy('user_id');

        // Win amounts: join through bets table to get user_id
        $betsAlias = $cfg['bets_table'];
        $winQuery  = DB::table($cfg['win_table'] . ' as w')
            ->join("{$betsAlias} as b2", 'b2.id', '=', 'w.bet_id')
            ->select('b2.user_id', DB::raw('SUM(w.win_amount) as compensate'))
            ->whereDate('w.created_at', $date)
            ->whereIn('b2.user_id', $memberIds)
            ->groupBy('b2.user_id');

        $wins = $winQuery->get()->keyBy('user_id');

        return view('admin.credit-fiat.index', compact(
            'members', 'roles', 'date', 'stats', 'wins', 'balances', 'currency', 'cfg'
        ));
    }

    public function deposit(Request $request, string $currency)
    {
        $cfg = $this->config($currency);

        $request->validate([
            'user_id'  => 'required',
            'amount'   => 'required|numeric|min:0.01',
            'type'     => 'required|in:deposit,withdraw,adjustment',
            'password' => 'required|string',
            'note'     => 'nullable|string|max:255',
        ]);

        /** @var \App\Models\User $auth */
        $auth = Auth::user();
        if (!Hash::check($request->password, $auth->password)) {
            return back()->with('error', 'Invalid password. Transaction cancelled.');
        }

        $userId = decrypt($request->user_id);

        DB::beginTransaction();
        try {
            $acctModel = $cfg['account_model'];
            $account   = $acctModel::firstOrCreate(
                ['user_id' => $userId],
                ['credit_balance' => 0, 'record_status_id' => 1, 'created_by' => $auth->id]
            );

            $before = (float) $account->credit_balance;

            if (in_array($request->type, ['withdraw', 'adjustment'])) {
                if ($before < (float) $request->amount) {
                    DB::rollBack();
                    return back()->with('error', 'Insufficient credit balance for withdrawal.');
                }
                $account->credit_balance -= $request->amount;
            } else {
                $account->credit_balance += $request->amount;
            }

            $account->updated_by = $auth->id;
            $account->save();

            $txModel = $cfg['model'];
            $txModel::create([
                'user_id'        => $userId,
                'type'           => $request->type,
                'amount'         => $request->amount,
                'balance_before' => $before,
                'balance_after'  => $account->credit_balance,
                'note'           => $request->note,
                'created_by'     => $auth->id,
            ]);

            DB::commit();
            return back()->with('success',
                ucfirst($request->type) . ' of ' . number_format($request->amount, 2) . ' ' . $currency . ' completed.'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function history(string $currency, int $userId)
    {
        $cfg          = $this->config($currency);
        $member       = User::findOrFail($userId);
        $account      = $cfg['account_model']::where('user_id', $userId)->first();
        $txModel      = $cfg['model'];
        $transactions = $txModel::with('createdBy')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.credit-fiat.history', compact('member', 'account', 'transactions', 'currency', 'cfg'));
    }
}
