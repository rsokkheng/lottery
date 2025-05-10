<?php

namespace App\Http\Controllers;

use App\Models\BetWinningRecord;
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
    public BetWinningRecord $betWinningRecord;
    public $currentDate;

    public function __construct(BetReceipt $model, Bet $betModel, BetWinningRecord $betWinningRecord)
    {
        $this->model = $model;
        $this->betModel = $betModel;
        $this->betWinningRecord = $betWinningRecord;
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

            $data = $this->model->newQuery()->with(['user', 'bets.betWinningRecords'])
                ->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->when(!is_null($date), function ($q) use ($date) {
                    $q->where('date', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'));
                    $q->where('date', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
                })->when(!is_null($no), function ($q) use ($no) {
                    $q->where('receipt_no', 'like', $no . '%');
                })->get()->map(function ($item) {
                    $checkWinBet = $item->bets->map(function ($bet){
                        return $bet->betWinningRecords()->count();
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
                    'betNumber.betNumberWin'
                ])->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
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
            'betWinningRecords',
            'bets' => function ($q) {
                $q->orderBy('id');
            }
        ])->findOrFail($id);
        $betIdWin = $this->betWinningRecord->newQuery()
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
            if(count($items)){
                $itemsFilter = array_filter($items, function ($val) use ($bet){
                    return $val['number'] === $bet['number_format'];
                });

                if(empty($itemsFilter)){
                    $items[] =[
                        'number' => $bet['number_format'],
                        'company' => $companyCode,
                        'amount' => $amount,
                        'is_win' => $isWin
                    ];
                }else{
                    $items[] =[
                        'number' => $bet['number_format'],
                        'company' => $companyCode,
                        'amount' => $amount,
                        'is_win' => $isWin
                    ];
                }
            }else{
                $items[] =[
                    'number' => $bet['number_format'],
                    'company' => $companyCode,
                    'amount' => $amount,
                    'is_win' => $isWin
                ];
            }
            $amount = '';
        }
        

        $isPaid = $result->betWinningRecords?->first()?->paid_status;
        $companyMap =$bet['betLotterySchedule']?->id;
        if(count($items) > 1){
            $grouped = collect($items)
                ->flatMap(function ($item) {
                    // Split amount string into parts like "30(R)"
                    $amounts = explode(',', $item['amount']);
                    return collect($amounts)->map(function ($amt) use ($item) {
                        if (preg_match('/^([\d.]+)\((\w+)\)$/', trim($amt), $matches)) {
                            return [
                                'number' => $item['number'],
                                'company' => $item['company'],
                                'type' => $matches[2],
                                'amount' => $matches[1],
                                'is_win' => $item['is_win'],
                            ];
                        }
                        return null;
                    })->filter();
                })
                // Step 1: Group by number + company + type to sum only within the same company
                ->groupBy(function ($item) {
                    return "{$item['number']}_{$item['company']}_{$item['type']}";
                })
                ->map(function ($group) {
                    $first = $group->first();
                    return [
                        'number' => $first['number'],
                        'company' => $first['company'],
                        'type' => $first['type'],
                        'amount' => $group->sum('amount'),
                        'is_win' => $group->contains('is_win', true),
                    ];
                })
                // Step 2: Group by number to prepare merging company display
                ->groupBy('number')
                ->map(function ($groupedItems, $number) use ($companyMap) {
                    // Collect unique company names for this number
                    $companyNames = $groupedItems->pluck('company')
                        ->unique()
                        ->map(fn($id) => $companyMap[$id] ?? $id)
                        ->implode(', ');

                    // Group by type + amount to avoid duplicating identical values across companies
                    $amountGrouped = $groupedItems
                        ->groupBy(function ($item) {
                            return "{$item['type']}_{$item['amount']}";
                        })
                        ->map(function ($items) {
                            // If multiple companies have the same amount+type, just show once
                            $first = $items->first();
                            return "{$first['amount']}({$first['type']})";
                        })
                        ->values()
                        ->implode(', ');

                    return [
                        'number' => $number,
                        'company' => $companyNames,
                        'amount' => $amountGrouped,
                        'is_win' => $groupedItems->contains('is_win', true),
                    ];
                })
                ->values();
            }else{
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
            if(count($items)){
                $itemsFilter = array_filter($items, function ($val) use ($bet, $companyCode){
                    return $val['number'] === $bet['number_format'];
                });

                if(empty($itemsFilter)){
                    $items[] =[
                        'number' => $bet['number_format'],
                        'company' => $companyCode,
                        'amount' => $amount,
                    ];
                }else{
                    $items[] =[
                        'number' => $bet['number_format'],
                        'company' => $companyCode,
                        'amount' => $amount,
                    ];
                }
            }else{
                $items[] =[
                    'number' => $bet['number_format'],
                    'company' => $companyCode,
                    'amount' => $amount,
                ];
            }
            $amount = '';
        }

        $companyMap =$bet['betLotterySchedule']?->id;
        $grouped = collect($items)
            ->flatMap(function ($item) {
                // Split amount string into parts like "30(R)"
                $amounts = explode(',', $item['amount']);
                return collect($amounts)->map(function ($amt) use ($item) {
                    if (preg_match('/^([\d.]+)\((\w+)\)$/', trim($amt), $matches)) {
                        return [
                            'number' => $item['number'],
                            'company' => $item['company'],
                            'type' => $matches[2],
                            'amount' => $matches[1],
                        ];
                    }
                    return null;
                })->filter();
            })
            // Step 1: Group by number + company + type to sum only within the same company
            ->groupBy(function ($item) {
                return "{$item['number']}_{$item['company']}_{$item['type']}";
            })
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'number' => $first['number'],
                    'company' => $first['company'],
                    'type' => $first['type'],
                    'amount' => $group->sum('amount'),
                ];
            })
            // Step 2: Group by number to prepare merging company display
            ->groupBy('number')
            ->map(function ($groupedItems, $number) use ($companyMap) {
                // Collect unique company names for this number
                $companyNames = $groupedItems->pluck('company')
                    ->unique()
                    ->map(fn($id) => $companyMap[$id] ?? $id)
                    ->implode(', ');

                // Group by type + amount to avoid duplicating identical values across companies
                $amountGrouped = $groupedItems
                    ->groupBy(function ($item) {
                        return "{$item['type']}_{$item['amount']}";
                    })
                    ->map(function ($items) {
                        // If multiple companies have the same amount+type, just show once
                        $first = $items->first();
                        return "{$first['amount']}({$first['type']})";
                    })
                    ->values()
                    ->implode(', ');

                return [
                    'number' => $number,
                    'company' => $companyNames,
                    'amount' => $amountGrouped,
                 
                ];
            })
            ->values();

        return view('bet.print_receipt', [
            'receipt_no' => $result->receipt_no,
            'total_amount' => $result->total_amount,
            'due_amount' => $result->net_amount,
            'bets' =>$grouped,
            'receipt_date' => Carbon::parse($result->date)->format('Y-m-d h:i A'),
            'expire_date' => Carbon::parse($result->date)->addDays(3)->format('Y-m-d h:i A'),
            'receipt_by' => $result?->user?->name,
        ]);
    }

    public function payReceipt($no)
    {
        if($no){
            $id = BetReceipt::query()->where('receipt_no', $no)->first()?->id;
            BetWinningRecord::query()->whereHas('betReceipt', function ($q) use ($id){
                $q->where('receipt_id', $id);
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
