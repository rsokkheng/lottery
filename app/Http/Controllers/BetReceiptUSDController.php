<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\BetUSD;
use Illuminate\Http\Request;
use App\Models\BetReceiptUSD;
use App\Models\BetWinningUSD;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\throwException;

class BetReceiptUSDController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public BetReceiptUSD $model;
    public BetUSD $betModel;
    public BetWinningUSD $betWinning;
    public $currentDate;

    public function __construct(BetReceiptUSD $model, BetUSD $betModel, BetWinningUSD $betWinning)
    {
        $this->model = $model;
        $this->betModel = $betModel;
        $this->betWinning = $betWinning;
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $roles = [];
            if ($user) {
                $roles = $user->roles()->pluck('name')->toArray(); // Use relation method to avoid issues
            }
            $date = $this->currentDate;
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            $no = $request->no ?? null;
            // Get all users under this manager
       
            $subQuery = DB::table('bet_number_usd as bn')
            ->join('bet_winning_usd as bw', 'bw.bet_number_id', '=', 'bn.id')
            ->select('bn.bet_id', DB::raw('SUM(bw.win_amount) as total_win_amount'))
            ->when(!is_null($date), function ($q) use ($date) {
                $q->where('bw.created_at', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'))
                  ->where('bw.created_at', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
            })
            ->groupBy('bn.bet_id');

        $data = DB::table('bet_usd')
            ->select(
                'users.username AS account',
                'users.id AS user_id',
                'bet_receipt_usd.receipt_no',
                'bet_receipt_usd.id as receipt_id',
                DB::raw('COUNT(DISTINCT bet_usd.bet_receipt_id) AS total_receipts'),
                DB::raw('SUM(bet_usd.total_amount) AS total_amount'),
                DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount'),
                DB::raw('SUM(bet_usd.total_amount - (bet_usd.total_amount * bet_package_configurations.rate / 100)) AS commission'),
                DB::raw('SUM(IFNULL(win_summary.total_win_amount, 0)) AS compensate'),
                DB::raw('DATE(bet_usd.bet_date) AS bet_date')
            )
            ->leftJoinSub($subQuery, 'win_summary', function ($join) {
                $join->on('win_summary.bet_id', '=', 'bet_usd.id');
            })
            ->join('bet_receipt_usd','bet_receipt_usd.id','=','bet_usd.bet_receipt_id')
            ->join('users', 'users.id', '=', 'bet_usd.user_id')
            ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
            ->join('bet_lottery_schedules as schedule', 'schedule.id', '=', 'bet_usd.bet_schedule_id')
            ->when(in_array('manager', $roles), function ($q) use ($user) {
                // Get all users under this manager
                $memberIds = User::where('users.manager_id', $user->id)
                                ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                                ->pluck('id')
                                ->toArray();
                $q->whereIn('users.id', $memberIds);
            })->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->when(!is_null($date), function ($q) use ($date) {
                $q->where('bet_usd.bet_date', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'));
                $q->where('bet_usd.bet_date', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
            })
            ->when(!is_null($no), function ($q) use ($no) {
                $q->where('bet_receipt_usd.receipt_no', 'like', $no . '%');
            })
            ->groupBy(
                'users.id',
                'users.username',
                'bet_receipt_usd.id',
                'bet_receipt_usd.receipt_no',
                DB::raw('DATE(bet_usd.bet_date)')
            )
            ->orderBy('bet_receipt_usd.receipt_no', 'asc')
            ->get();

            return view('bet_usd.receipt-list', compact('data', 'date', 'no'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

public function betList(Request $request)
{
    try {
        $user = Auth::user()??0;
        if ($user) {
            $user = User::find($user->id);
            $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
        }
        $date = $this->currentDate;
        if ($request->has('date')) {
            $date = $request->get('date');
        }
        $company_id = null;
        if ($request->has('com_id')) {
            $company_id = $request->get('com_id');
        }
        $receiptNo = $request->no ?? null;
        $number = $request->number ?? null;
        $company = [
            [
                "label" => "All Company",
                "id" => null,
            ],
            [
                "label" => "4PM Company",
                "id" => 1,
            ],
            [
                "label" => "5PM Company",
                "id" => 2,
            ],
            [
                "label" => "6PM Company",
                "id" => 3,
            ]
        ];
        $data = $this->betModel
            ->with([
                'beReceiptUSD',
                'user',
                'bePackageConfig',
                'betLotterySchedule',
                'betNumberUSD.betNumberWinUSD.betWinningUSD',
            ])->when(in_array('manager', $roles), function ($q) use ($user) {
                // Get all users under this manager
                $memberIds = User::where('manager_id', $user->id)
                                ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                                ->pluck('id')
                                ->toArray();
                $q->whereIn('user_id', $memberIds);
            })->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->when(!is_null($date), function ($q) use ($date) {
                $q->where('bet_date', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'));
                $q->where('bet_date', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
            })->when(!is_null($company_id), function ($q) use ($company_id) {
                $q->where('company_id', $company_id);
            })->when(!is_null($receiptNo), function ($q) use ($receiptNo) {
                $q->whereHas('beReceiptUSD', function ($query) use ($receiptNo) {
                    $query->where('receipt_no', $receiptNo);
                });
            })->when(!is_null($number), function ($q) use ($number) {
                $q->whereHas('betNumberUSD', function ($query) use ($number) {
                    $query->where('generated_number', $number);
                });
            })->get();
        return view('bet_usd.bet-list', compact('data', 'date', 'receiptNo', 'number', 'company', 'company_id'));
    } catch (\Exception $exception) {
        throwException($exception);
        return $exception->getMessage();
    }
}

private function addAmount(&$amountArray, $value, $check, $label)
{
    if ($value > 0) {
        // Convert value to integer if it is a whole number
        $displayValue = ($value == (int)$value) ? (int)$value : $value;
    
        // Format the amount with the appropriate label
        $amount = $displayValue . ($check ? "({$label}x)" : "({$label})");
        
        // Check if the label is already present in the array to avoid duplicates
        if (strpos($amountArray, "({$label})") === false && strpos($amountArray, "({$label}x)") === false) {
            // If it's not in the array, append the new formatted amount
            if (!empty($amountArray)) {
                $amountArray .= ', ' . $amount;
            } else {
                $amountArray = $amount;
            }
        }
    }
    
}

public function getBetByReceiptId($id)
{
    $result = $this->model->with([
        'betsUSD.betLotterySchedule',
        'betsUSD.betNumberUSD',
        'betWinningUSD',
        'betsUSD' => function ($q) {
            $q->orderBy('id');
        }
    ])->findOrFail($id);

    
    $betIdWin = $this->betWinning->newQuery()
        ->whereHas('betsUSD', function ($q) use ($id){
            $q->where('bet_receipt_id', $id);
        })
        ->orderBy('bet_id')->pluck('bet_id')->unique()->toArray();
    $items = [];
    $amount = '';

    $sumAmount = DB::table('bet_usd')
    ->select(
        DB::raw('SUM(bet_usd.total_amount) AS total_amount'), // typo fixed here: "totatlAmount" → "total_amount"
        DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount')
    )
    ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
    ->where('bet_usd.bet_receipt_id', $id)
    ->groupBy('bet_usd.bet_receipt_id')
    ->first();

    foreach ($result->betsUSD as $bet){
        foreach ($bet['betNumberUSD'] as $betNumber){
            $this->addAmount($amount, $betNumber->a_amount ?? 0, $betNumber->a_check ?? false, "A");
            $this->addAmount($amount, $betNumber->b_amount ?? 0, $betNumber->b_check ?? false, "B");
            $this->addAmount($amount, $betNumber->ab_amount ?? 0, $betNumber->ab_check ?? false, "AB");
            $this->addAmount($amount, $betNumber->roll_amount ?? 0, $betNumber->roll_check ?? false, "R");
            $this->addAmount($amount, $betNumber->roll7_amount ?? 0, $betNumber->roll7_check ?? false, "R7");
            $this->addAmount($amount, $betNumber->roll_parlay_amount ?? 0, $betNumber->roll_parlay_check ?? false, "RP");
        }
        
        $companyCode = $bet['betLotterySchedule']?->code;
        $isWin = in_array($bet->id, $betIdWin);
        $createdAt = $bet->created_at;
        $digitFormat = $bet->digit_format;
        $totalAmount = $bet->total_amount;
        
        $items[] = [
            'number' => $bet['number_format'],
            'digit_format' => $digitFormat,
            'company' => $companyCode,
            'amount' => $amount,
            'total_amount' => $totalAmount,
            'is_win' => $isWin,
            'created_at' => $createdAt
        ];
        $amount = '';
    }
    
    $isPaid = $result->betWinning?->first()?->paid_status;
    $companyMap = $bet['betLotterySchedule']?->id;
    
    if(count($items) > 1){
        $grouped = collect($items)
            // Group by number_format + digit_format + total_amount + created_at (matching SQL GROUP BY)
            ->groupBy(function ($item) {
                $createdAtTimestamp = \Carbon\Carbon::parse($item['created_at'])->format('Y-m-d H:i');
                return "{$item['number']}_{$item['digit_format']}_{$item['total_amount']}_{$createdAtTimestamp}";
            })
            ->map(function ($group) {
                $first = $group->first();
                
                // Collect unique company names for this group
                $companyNames = $group->pluck('company')
                    ->unique()
                    ->implode(', ');

                return [
                    'number' => $first['number'],
                    'digit_format' => $first['digit_format'],
                    'company' => $companyNames,
                    'amount' => $first['amount'],
                    'total_amount' => $first['total_amount'],
                    'is_win' => $group->contains('is_win', true),
                    'created_at' => $first['created_at'],
                ];
            })
            ->values();
    } else {
        $grouped = $items;
    }

    return response()->json([
        'no_receipt' => $result?->receipt_no,
        'totalAmount' => $sumAmount->total_amount ?? 0,
        'dueAmount'   => $sumAmount->net_amount ?? 0,
        'is_paid' => $isPaid == 2,
        'items' => $grouped,
    ]);
}
public function printReceiptNo($receiptNo)
    {
        $result = $this->model->with(['betsUSD.betLotterySchedule', 'betsUSD.betNumberUSD','user'])
        ->where('receipt_no', '=', $receiptNo)
        ->first();
        if (empty($result)) {
            return abort(404, 'Receipt not found');
        }


        $sumAmount = DB::table('bet_usd')
        ->select(
            DB::raw('SUM(bet_usd.total_amount) AS total_amount'), // typo fixed here: "totatlAmount" → "total_amount"
            DB::raw('SUM(bet_usd.total_amount * bet_package_configurations.rate / 100) AS net_amount')
        )
        ->join('bet_package_configurations', 'bet_package_configurations.id', '=', 'bet_usd.bet_package_config_id')
        ->where('bet_usd.bet_receipt_id', $result->id)
        ->groupBy('bet_usd.bet_receipt_id')
        ->first();
        
        $items = [];
        $amount = '';
        foreach ($result->betsUSD as $bet){
            foreach ($bet['betNumberUSD'] as $betNumber){
                $this->addAmount($amount, $betNumber->a_amount ?? 0, $betNumber->a_check ?? false, "A");
                $this->addAmount($amount, $betNumber->b_amount ?? 0, $betNumber->b_check ?? false, "B");
                $this->addAmount($amount, $betNumber->ab_amount ?? 0, $betNumber->ab_check ?? false, "AB");
                $this->addAmount($amount, $betNumber->roll_amount ?? 0, $betNumber->roll_check ?? false, "R");
                $this->addAmount($amount, $betNumber->roll7_amount ?? 0, $betNumber->roll7_check ?? false, "R7");
                $this->addAmount($amount, $betNumber->roll_parlay_amount ?? 0, $betNumber->roll_parlay_check ?? false, "RP");
            }
            
            $companyCode = $bet['betLotterySchedule']?->code;
            $digitFormat = $bet->digit_format;
            $totalAmount = $bet->total_amount;
            $createdAt = $bet->created_at;
            
            $items[] = [
                'number' => $bet['number_format'],
                'digit_format' => $digitFormat,
                'company' => $companyCode,
                'amount' => $amount,
                'total_amount' => $totalAmount,
                'created_at' => $createdAt,
            ];
            
            $amount = '';
        }

        // Group by number_format + digit_format + total_amount + created_at (matching SQL GROUP BY)
        $grouped = collect($items)
            ->groupBy(function ($item) {
                $createdAtTimestamp = \Carbon\Carbon::parse($item['created_at'])->format('Y-m-d H:i:s');
                return "{$item['number']}_{$item['digit_format']}_{$item['total_amount']}_{$createdAtTimestamp}";
            })
            ->map(function ($group) {
                $first = $group->first();
                
                // Collect unique company names for this group
                $companyNames = $group->pluck('company')
                    ->unique()
                    ->filter() // Remove null/empty values
                    ->implode(', ');

                return [
                    'number' => $first['number'],
                    'digit_format' => $first['digit_format'],
                    'company' => $companyNames,
                    'amount' => $first['amount'],
                    'total_amount' => $first['total_amount'],
                    'created_at' => $first['created_at'],
                ];
            })
            ->values();

        return view('bet_usd.print_receipt', [
            'receipt_no' => $result->receipt_no,
            'total_amount' => $sumAmount->total_amount ?? 0,
            'due_amount' => $sumAmount->net_amount ?? 0,
            'bets' => $grouped,
            'receipt_date' => Carbon::parse($result->date)->format('Y-m-d h:i A'),
            'expire_date' => Carbon::parse($result->date)->addDays(3)->format('Y-m-d h:i A'),
            'receipt_by' => $result?->user?->name,
        ]);
    }

    public function payReceipt($no)
    {
        if($no){
            $id = BetReceiptUSD::query()->where('receipt_no', $no)->first()?->id;
            BetWinningUSD::query()->whereHas('betReceiptUSD', function ($q) use ($id){
                $q->where('bet_receipt_id', $id);
            })->update(['paid_at'=> date('Y-m-d H:i:s'), 'paid_status'=>2]);
            return response()->json([
                'success'=> true,
                'message'=> 'Receipt was paid.'
            ]);
        }
        return response()->json([
        'success'=> false,
            'message'=> 'No receipt for pay.'
        ]);
    }
}
