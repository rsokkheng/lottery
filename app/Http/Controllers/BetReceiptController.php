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
            $company_id = -1;
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $receiptNo = $request->no ?? null;
            $number = $request->number ?? null;
            $company = [
                [
                    "label" => "All Company",
                    "id" => 0,
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
                    'betNumber',
                    'bePackageConfig',
                    'betLotterySchedule'
                ])->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->when(!is_null($date), function ($q) use ($date) {
                    $q->where('bet_date', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'));
                    $q->where('bet_date', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
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
            $getAmount = $value . ($check ? "({$label}x)" : "({$label})");
            $amountArray = $getAmount;
//            if(!empty($amountArray)){
//                $amountArray = $amountArray.', '.$getAmount;
//            }else{
//                $amountArray = $amountArray.$getAmount;
//            }
        }
    }

    public function getBetByReceiptId($id)
    {
        $result = $this->model->with(['bets.betLotterySchedule', 'bets.betNumber', 'betWinningRecords', 'bets'=>function ($q) {
            $q->orderBy('number_format');
        }])
            ->findOrFail($id);
        $betIdWin = $this->betWinningRecord->newQuery()
            ->whereHas('bets', function ($q) use ($id){
                $q->where('bet_receipt_id', $id);
            })
            ->orderBy('bet_id')->pluck('bet_id')->unique()->toArray();
        $items = [];
        $amount = '';
        foreach ($result->bets as $key => $bet){
            $this->addAmount($amount, $bet['betNumber'][0]->a_amount ?? 0, $bet['betNumber'][0]->a_check ?? false, "A");
            $this->addAmount($amount, $bet['betNumber'][0]->b_amount ?? 0, $bet['betNumber'][0]->b_check ?? false, "B");
            $this->addAmount($amount, $bet['betNumber'][0]->ab_amount ?? 0, $bet['betNumber'][0]->ab_check ?? false, "AB");
            $this->addAmount($amount, $bet['betNumber'][0]->roll_amount ?? 0, $bet['betNumber'][0]->roll_check ?? false, "R");
            $this->addAmount($amount, $bet['betNumber'][0]->roll7_amount ?? 0, $bet['betNumber'][0]->roll7_check ?? false, "R7");
            $this->addAmount($amount, $bet['betNumber'][0]->roll_parlay_amount ?? 0, $bet['betNumber'][0]->roll_parlay_check ?? false, "RP");

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
                        'is_win' => in_array($bet->id, $betIdWin)
                    ];
                }else{
                    $items = array_map(function ($val) use ($bet, $companyCode) {
                        if($val['number'] === $bet['number_format']){
                            return [...$val, 'company'=> $val['company'].', '.$companyCode];
                        }
                        return $val;
                    },$items);
                }
            }else{
                $items[] =[
                    'number' => $bet['number_format'],
                    'company' => $companyCode,
                    'amount' => $amount,
                    'is_win' => in_array($bet->id, $betIdWin)
                ];
            }

            $amount = '';


        }
        $isPaid = $result->betWinningRecords?->first()?->paid_status;
        return response()->json([
                'no_receipt'=>$result?->receipt_no,
                'totalAmount' => $result?->total_amount,
                'dueAmount' => $result?->net_amount,
                'is_paid' => $isPaid == 2,
                'items' => $items,
        ]);
    }
    public function printReceiptNo($receiptNo)
    {
        $result = $this->model->with(['bets.betLotterySchedule', 'bets.betNumber','user'])
            ->where('receipt_no', '=', $receiptNo)
            ->first();

        if (!$result) {
            return abort(404, 'Receipt not found');
        }

        $items = [];
        foreach ($result->bets as $bet) {
            $amount = [];

            $this->addAmount($amount, $bet['betNumber'][0]->a_amount ?? 0, $bet['betNumber'][0]->a_check ?? false, "A");
            $this->addAmount($amount, $bet['betNumber'][0]->b_amount ?? 0, $bet['betNumber'][0]->b_check ?? false, "B");
            $this->addAmount($amount, $bet['betNumber'][0]->ab_amount ?? 0, $bet['betNumber'][0]->ab_check ?? false, "AB");
            $this->addAmount($amount, $bet['betNumber'][0]->roll_amount ?? 0, $bet['betNumber'][0]->roll_check ?? false, "R");
            $this->addAmount($amount, $bet['betNumber'][0]->roll7_amount ?? 0, $bet['betNumber'][0]->roll7_check ?? false, "R7");
            $this->addAmount($amount, $bet['betNumber'][0]->roll_parlay_amount ?? 0, $bet['betNumber'][0]->roll_parlay_check ?? false, "RP");

            $items[] = [
                'number' => $bet['number_format'],
                'company' => $bet['betLotterySchedule']?->code,
                'amount' => $amount
            ];
        }

        return view('bet.print_receipt', [
            'receipt_no' => $result->receipt_no,
            'total_amount' => $result->total_amount,
            'due_amount' => $result->net_amount,
            'bets' => $items,
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
