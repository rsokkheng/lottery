<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\BetKH;
use App\Models\BetKHUSD;
use App\Models\BetReceiptKH;
use App\Models\BetReceiptKHUSD;
use App\Models\BetWinningKH;
use App\Models\BetWinningKHUSD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\throwException;

class BetReceiptKHController extends Controller
{
    public BetReceiptKH $model;
    public BetKH $betModel;
    public BetWinningKH $betWinning;
    public $currentDate;

    public function __construct(BetReceiptKH $model, BetKH $betModel, BetWinningKH $betWinning)
    {
        $this->model     = $model;
        $this->betModel  = $betModel;
        $this->betWinning = $betWinning;
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    private function khTables(): array
    {
        $cur = strtolower(session('currency', 'VND'));
        return [
            'cur'     => $cur,
            'bet'     => "bet_kh_{$cur}",
            'receipt' => "bet_receipt_kh_{$cur}",
            'number'  => "bet_number_kh_{$cur}",
            'winning' => "bet_winning_kh_{$cur}",
        ];
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $roles = [];
            if ($user) {
                $roles = $user->roles()->pluck('name')->toArray();
            }
            $date = $this->currentDate;
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            $no = $request->no ?? null;

            ['bet' => $tBet, 'receipt' => $tReceipt, 'number' => $tNumber, 'winning' => $tWin] = $this->khTables();

            $subQuery = DB::table("{$tNumber} as bn")
                ->join("{$tWin} as bw", 'bw.bet_number_id', '=', 'bn.id')
                ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
                ->when(!is_null($date), function ($q) use ($date) {
                    $q->where('bw.created_at', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'))
                      ->where('bw.created_at', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
                })
                ->groupBy('bn.bet_id');

            $data = DB::table($tBet)
                ->select(
                    'users.username AS account',
                    'users.id AS user_id',
                    "{$tReceipt}.receipt_no",
                    "{$tReceipt}.id as receipt_id",
                    DB::raw("COUNT(DISTINCT {$tBet}.bet_receipt_id) AS total_receipts"),
                    DB::raw("SUM({$tBet}.total_amount) AS total_amount"),
                    DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS net_amount"),
                    DB::raw("SUM({$tBet}.total_amount - ({$tBet}.total_amount * bet_package_configurations.rate / 100)) AS commission"),
                    DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS compensate'),
                    DB::raw("DATE({$tBet}.bet_date) AS bet_date")
                )
                ->leftJoinSub($subQuery, 'win_summary', function ($join) use ($tBet) {
                    $join->on('win_summary.bet_id', '=', "{$tBet}.id");
                })
                ->join($tReceipt, "{$tReceipt}.id", '=', "{$tBet}.bet_receipt_id")
                ->join('users', 'users.id', '=', "{$tBet}.user_id")
                ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
                ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', "{$tBet}.bet_schedule_id")
                ->when(in_array('agent', $roles), function ($q) use ($user) {
                    $memberIds = User::where('users.manager_id', $user->id)
                                    ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                                    ->pluck('id')
                                    ->toArray();
                    $q->whereIn('users.id', $memberIds);
                })
                ->when(!in_array('admin', $roles) && !in_array('agent', $roles), function ($q) use ($user) {
                    $q->where('users.id', $user->id);
                })
                ->when(!is_null($date), function ($q) use ($date, $tBet) {
                    $q->where("{$tBet}.bet_date", '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'));
                    $q->where("{$tBet}.bet_date", '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
                })
                ->when(!is_null($no), function ($q) use ($no, $tReceipt) {
                    $q->where("{$tReceipt}.receipt_no", 'like', $no . '%');
                })
                ->groupBy(
                    'users.id',
                    'users.username',
                    "{$tReceipt}.id",
                    "{$tReceipt}.receipt_no",
                    DB::raw("DATE({$tBet}.bet_date)")
                )
                ->orderBy("{$tReceipt}.receipt_no", 'asc')
                ->get();

            return view('bet_kh.receipt-list', compact('data', 'date', 'no'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function betList(Request $request)
    {
        try {
            $user = Auth::user() ?? 0;
            if ($user) {
                $user  = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray();
            }
            $date = $this->currentDate;
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            $company_id = null;
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $receiptNo = $request->no     ?? null;
            $number    = $request->number ?? null;
            $company   = [
                ["label" => "All Company", "id" => null],
                ["label" => "4PM Company", "id" => 1],
                ["label" => "5PM Company", "id" => 2],
                ["label" => "6PM Company", "id" => 3],
            ];

            $cur = strtolower(session('currency', 'VND'));
            $betModelInstance = $cur === 'usd' ? new BetKHUSD() : $this->betModel;

            $data = $betModelInstance
                ->with([
                    'beReceiptKH',
                    'user',
                    'bePackageConfig',
                    'betLotterySchedule',
                    'betNumberKH.betNumberWinKH.betWinningKH',
                ])
                ->when(in_array('agent', $roles), function ($q) use ($user) {
                    $memberIds = User::where('manager_id', $user->id)
                                    ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                                    ->pluck('id')
                                    ->toArray();
                    $q->whereIn('user_id', $memberIds);
                })
                ->when(!in_array('admin', $roles) && !in_array('agent', $roles), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->when(!is_null($date), function ($q) use ($date) {
                    $q->where('bet_date', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'));
                    $q->where('bet_date', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
                })
                ->when(!is_null($company_id), function ($q) use ($company_id) {
                    $q->where('company_id', $company_id);
                })
                ->when(!is_null($receiptNo), function ($q) use ($receiptNo) {
                    $q->whereHas('beReceiptKH', function ($query) use ($receiptNo) {
                        $query->where('receipt_no', $receiptNo);
                    });
                })
                ->when(!is_null($number), function ($q) use ($number) {
                    $q->whereHas('betNumberKH', function ($query) use ($number) {
                        $query->where('generated_number', $number);
                    });
                })
                ->get();

            return view('bet_kh.bet-list', compact('data', 'date', 'receiptNo', 'number', 'company', 'company_id'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

    private function addAmount(&$amountArray, $value, $check, $label)
    {
        if ($value > 0) {
            $displayValue = ($value == (int)$value) ? (int)$value : $value;
            $amount = $displayValue . ($check ? "({$label}x)" : "({$label})");
            if (strpos($amountArray, "({$label})") === false && strpos($amountArray, "({$label}x)") === false) {
                $amountArray = !empty($amountArray) ? $amountArray . ', ' . $amount : $amount;
            }
        }
    }

    public function getBetByReceiptId($id)
    {
        ['cur' => $cur, 'bet' => $tBet] = $this->khTables();

        $receiptModel = $cur === 'usd' ? new BetReceiptKHUSD() : $this->model;
        $winModel     = $cur === 'usd' ? new BetWinningKHUSD() : $this->betWinning;

        $result = $receiptModel->with([
            'betsKH.betLotterySchedule',
            'betsKH.betNumberKH',
            'betWinningKH',
            'betsKH' => function ($q) { $q->orderBy('id'); },
        ])->findOrFail($id);

        $betIdWin = $winModel->newQuery()
            ->whereHas('betsKH', function ($q) use ($id) {
                $q->where('bet_receipt_id', $id);
            })
            ->orderBy('bet_id')->pluck('bet_id')->unique()->toArray();

        $items  = [];
        $amount = '';

        $sumAmount = DB::table($tBet)
            ->select(
                DB::raw("SUM({$tBet}.total_amount) AS total_amount"),
                DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS net_amount")
            )
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
            ->where("{$tBet}.bet_receipt_id", $id)
            ->groupBy("{$tBet}.bet_receipt_id")
            ->first();

        foreach ($result->betsKH as $bet) {
            foreach ($bet['betNumberKH'] as $betNumber) {
                $this->addAmount($amount, $betNumber->a_amount    ?? 0, $betNumber->a_check    ?? false, "A");
                $this->addAmount($amount, $betNumber->b_amount    ?? 0, $betNumber->b_check    ?? false, "B");
                $this->addAmount($amount, $betNumber->c_amount    ?? 0, $betNumber->c_check    ?? false, "C");
                $this->addAmount($amount, $betNumber->d_amount    ?? 0, $betNumber->d_check    ?? false, "D");
                $this->addAmount($amount, $betNumber->abcd_amount ?? 0, $betNumber->abcd_check ?? false, "ABCD");
                $this->addAmount($amount, $betNumber->roll_amount ?? 0, $betNumber->roll_check ?? false, "R");
                $this->addAmount($amount, $betNumber->roll2_amount       ?? 0, $betNumber->roll2_check       ?? false, "R2");
                $this->addAmount($amount, $betNumber->roll_parlay_amount ?? 0, $betNumber->roll_parlay_check ?? false, "RP");
            }

            $companyCode  = $bet['betLotterySchedule']?->code;
            $isWin        = in_array($bet->id, $betIdWin);
            $createdAt    = $bet->created_at;
            $digitFormat  = $bet->digit_format;
            $totalAmount  = $bet->total_amount;

            $items[] = [
                'number'       => $bet['number_format'],
                'digit_format' => $digitFormat,
                'company'      => $companyCode,
                'amount'       => $amount,
                'total_amount' => $totalAmount,
                'is_win'       => $isWin,
                'created_at'   => $createdAt,
            ];
            $amount = '';
        }

        $isPaid = $result->betWinningKH?->first()?->paid_status;

        if (count($items) > 1) {
            $grouped = collect($items)
                ->groupBy(function ($item) {
                    $ts = \Carbon\Carbon::parse($item['created_at'])->format('Y-m-d H:i');
                    return "{$item['number']}_{$item['digit_format']}_{$item['total_amount']}_{$ts}";
                })
                ->map(function ($group) {
                    $first        = $group->first();
                    $companyNames = $group->pluck('company')->unique()->implode(', ');
                    return [
                        'number'       => $first['number'],
                        'digit_format' => $first['digit_format'],
                        'company'      => $companyNames,
                        'amount'       => $first['amount'],
                        'total_amount' => $group->sum('total_amount'),
                        'is_win'       => $group->contains('is_win', true),
                        'created_at'   => $first['created_at'],
                    ];
                })
                ->values();
        } else {
            $grouped = $items;
        }

        return response()->json([
            'no_receipt'  => $result?->receipt_no,
            'totalAmount' => $sumAmount->total_amount ?? 0,
            'dueAmount'   => $sumAmount->net_amount   ?? 0,
            'is_paid'     => $isPaid == 2,
            'items'       => $grouped,
        ]);
    }

    public function printReceiptNo($receiptNo)
    {
        ['cur' => $cur, 'bet' => $tBet] = $this->khTables();
        $receiptModel = $cur === 'usd' ? new BetReceiptKHUSD() : $this->model;

        $result = $receiptModel->with(['betsKH.betLotterySchedule', 'betsKH.betNumberKH', 'user'])
            ->where('receipt_no', '=', $receiptNo)
            ->first();

        if (empty($result)) {
            return abort(404, 'Receipt not found');
        }

        $sumAmount = DB::table($tBet)
            ->select(
                DB::raw("SUM({$tBet}.total_amount) AS total_amount"),
                DB::raw("SUM({$tBet}.total_amount * bet_package_configurations.rate / 100) AS net_amount")
            )
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', "{$tBet}.bet_package_config_id")
            ->where("{$tBet}.bet_receipt_id", $result->id)
            ->groupBy("{$tBet}.bet_receipt_id")
            ->first();

        $items  = [];
        $amount = '';

        foreach ($result->betsKH as $bet) {
            foreach ($bet['betNumberKH'] as $betNumber) {
                $this->addAmount($amount, $betNumber->a_amount    ?? 0, $betNumber->a_check    ?? false, "A");
                $this->addAmount($amount, $betNumber->b_amount    ?? 0, $betNumber->b_check    ?? false, "B");
                $this->addAmount($amount, $betNumber->c_amount    ?? 0, $betNumber->c_check    ?? false, "C");
                $this->addAmount($amount, $betNumber->d_amount    ?? 0, $betNumber->d_check    ?? false, "D");
                $this->addAmount($amount, $betNumber->abcd_amount ?? 0, $betNumber->abcd_check ?? false, "ABCD");
                $this->addAmount($amount, $betNumber->roll_amount ?? 0, $betNumber->roll_check ?? false, "R");
                $this->addAmount($amount, $betNumber->roll2_amount       ?? 0, $betNumber->roll2_check       ?? false, "R2");
                $this->addAmount($amount, $betNumber->roll_parlay_amount ?? 0, $betNumber->roll_parlay_check ?? false, "RP");
            }

            $items[] = [
                'number'       => $bet['number_format'],
                'digit_format' => $bet->digit_format,
                'company'      => $bet['betLotterySchedule']?->code,
                'amount'       => $amount,
                'total_amount' => $bet->total_amount,
                'created_at'   => $bet->created_at,
            ];
            $amount = '';
        }

        $grouped = collect($items)
            ->groupBy(function ($item) {
                $ts = \Carbon\Carbon::parse($item['created_at'])->format('Y-m-d H:i');
                return "{$item['number']}_{$item['digit_format']}_{$item['total_amount']}_{$ts}";
            })
            ->map(function ($group) {
                $first        = $group->first();
                $companyNames = $group->pluck('company')->unique()->filter()->implode(', ');
                return [
                    'number'       => $first['number'],
                    'digit_format' => $first['digit_format'],
                    'company'      => $companyNames,
                    'amount'       => $first['amount'],
                    'total_amount' => $group->sum('total_amount'),
                    'created_at'   => $first['created_at'],
                ];
            })
            ->values();

        return view('bet_kh.print_receipt', [
            'receipt_no'   => $result->receipt_no,
            'total_amount' => $sumAmount->total_amount ?? 0,
            'due_amount'   => $sumAmount->net_amount   ?? 0,
            'bets'         => $grouped,
            'receipt_date' => Carbon::parse($result->date)->format('Y-m-d h:i A'),
            'expire_date'  => Carbon::parse($result->date)->addDays(3)->format('Y-m-d h:i A'),
            'receipt_by'   => $result?->user?->name,
        ]);
    }

    public function payReceipt($no)
    {
        if ($no) {
            $cur = strtolower(session('currency', 'VND'));
            if ($cur === 'usd') {
                $id = BetReceiptKHUSD::query()->where('receipt_no', $no)->first()?->id;
                BetWinningKHUSD::query()->whereHas('betReceiptKHUSD', function ($q) use ($id) {
                    $q->where('bet_receipt_id', $id);
                })->update(['paid_at' => date('Y-m-d H:i:s'), 'paid_status' => 2]);
            } else {
                $id = BetReceiptKH::query()->where('receipt_no', $no)->first()?->id;
                BetWinningKH::query()->whereHas('betReceiptKH', function ($q) use ($id) {
                    $q->where('bet_receipt_id', $id);
                })->update(['paid_at' => date('Y-m-d H:i:s'), 'paid_status' => 2]);
            }
            return response()->json(['success' => true, 'message' => 'Receipt was paid.']);
        }
        return response()->json(['success' => false, 'message' => 'No receipt for pay.']);
    }
}
