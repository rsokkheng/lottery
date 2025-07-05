<?php

namespace App\Http\Controllers;

use App\Models\BetWinning;
use Carbon\Carbon;
use App\Models\Bet;
use App\Models\User;
use App\Models\BetReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use function PHPUnit\Framework\throwException;

class BetReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public BetReceipt $model;
    public Bet $betModel;
    public BetWinning $betWinning;
    public $currentDate;

    public function __construct(BetReceipt $model, Bet $betModel, BetWinning $betWinning)
    {
        $this->model = $model;
        $this->betModel = $betModel;
        $this->betWinning = $betWinning;
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    public function index(Request $request)
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
            $no = $request->no ?? null;

            $data = $this->model->newQuery()->with(['user', 'bets.betWinning'])
                ->when(in_array('manager', $roles), function ($q) use ($user) {
                    // Get all users under this manager
                    $memberIds = User::where('manager_id', $user->id)
                                    ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                                    ->pluck('id')
                                    ->toArray();
                    $q->whereIn('user_id', $memberIds);
                })->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->when(!is_null($date), function ($q) use ($date) {
                    $q->where('date', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'));
                    $q->where('date', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
                })->when(!is_null($no), function ($q) use ($no) {
                    $q->where('receipt_no', 'like', $no . '%');
                })->get()->map(function ($item) {
                    $checkWinBet = $item->bets->map(function ($bet){
                        return $bet->betWinning()->count();
                    })->sum();
                    return [
                        'id' => $item->id,
                        "receipt_no" => $item->receipt_no,
                        "user_id" => $item->user_id,
                        "user_username" => $item->user?->username,
                        "user_name" => $item->user?->name,
                        "date" => is_null($item->date) ? null : date('Y-m-d H:i:s', strtotime($item->date)),
                        "currency" => $item->currency,
                        "total_amount" => $item->total_amount,
                        "commission" => $item->commission,
                        "net_amount" => $item->net_amount,
                        "compensate" => $item->compensate,
                        "is_win" => (bool)$checkWinBet
                    ];
                });
            return view('bet.receipt-list', compact('data', 'date', 'no'));
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
                    'beReceipt',
                    'user',
                    'bePackageConfig',
                    'betLotterySchedule',
                    'betNumber.betNumberWin.betWinning',
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
                    $q->whereHas('beReceipt', function ($query) use ($receiptNo) {
                        $query->where('receipt_no', $receiptNo);
                    });
                })->when(!is_null($number), function ($q) use ($number) {
                    $q->whereHas('betNumber', function ($query) use ($number) {
                        $query->where('generated_number', $number);
                    });
                })->get();
            return view('bet.bet-list', compact('data', 'date', 'receiptNo', 'number', 'company', 'company_id'));
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
        'bets.betLotterySchedule',
        'bets.betNumber',
        'betWinning',
        'bets' => function ($q) {
            $q->orderBy('id');
        }
    ])->findOrFail($id);
    
    $betIdWin = $this->betWinning->newQuery()
        ->whereHas('bets', function ($q) use ($id){
            $q->where('bet_receipt_id', $id);
        })
        ->orderBy('bet_id')->pluck('bet_id')->unique()->toArray();
    
    $items = [];
    $amount = '';

    foreach ($result->bets as $bet){
        foreach ($bet['betNumber'] as $betNumber){
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
                $createdAtTimestamp = \Carbon\Carbon::parse($item['created_at'])->format('Y-m-d H:i:s');
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
        'totalAmount' => $result?->total_amount,
        'dueAmount' => $result?->net_amount,
        'is_paid' => $isPaid == 2,
        'items' => $grouped,
    ]);
}
public function printReceiptNo($receiptNo)
{
        $result = $this->model->with(['bets.betLotterySchedule', 'bets.betNumber','user'])
        ->where('receipt_no', '=', $receiptNo)
        ->first();

    if (empty($result)) {
        return abort(404, 'Receipt not found');
    }

    $items = [];
    $amount = '';
    foreach ($result->bets as $bet){
        foreach ($bet['betNumber'] as $betNumber){
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

    return view('bet.print_receipt', [
        'receipt_no' => $result->receipt_no,
        'total_amount' => $result->total_amount,
        'due_amount' => $result->net_amount,
        'bets' => $grouped,
        'receipt_date' => Carbon::parse($result->date)->format('Y-m-d h:i A'),
        'expire_date' => Carbon::parse($result->date)->addDays(3)->format('Y-m-d h:i A'),
        'receipt_by' => $result?->user?->name,
    ]);
}

public function payReceipt($no)
{
    if($no){
        $id = BetReceipt::query()->where('receipt_no', $no)->first()?->id;
        BetWinning::query()->whereHas('betReceipt', function ($q) use ($id){
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
