<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\BetReceipt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use function PHPUnit\Framework\throwException;

class BetReceiptController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public BetReceipt $model;
    public Bet $betModel;
    public $currentDate;

    public function __construct(BetReceipt $model, Bet $betModel)
    {
        $this->model = $model;
        $this->betModel = $betModel;
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    public function index(Request $request)
    {
        try{
            $date = $this->currentDate;
            if($request->has('date')){
                $date = $request->get('date');
            }
            $no = $request->no??null;
//            $ddd = Carbon::createFromFormat('Y-m-d', $date)->firsto();
//            $ddd = Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s');
//            dd($ddd);
//            dd($date, $no, is_null($date));
            $data = $this->model->  newQuery()->with(['user'])
                ->when(!is_null($date), function ($q) use ($date){
//                    $q->whereBetween('date', [Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'), Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s')]);
                    $q->where('date', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'));
                    $q->where('date', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
                })
                ->when(!is_null($no), function ($q) use ($no){
                    $q->where('receipt_no', 'like', $no.'%');
                })
                ->get()->map(function ($item){
                return [
                    "receipt_no" => $item->receipt_no,
                    "user_id" => $item->user_id,
                    "user_username" => $item->user?->username,
                    "user_name" => $item->user?->name,
                    "date" => is_null($item->date) ? null : date('Y-m-d H-i-s', strtotime($item->date)),
                    "currency" => $item->currency,
                    "total_amount" => $item->total_amount,
                    "commission" => $item->commission,
                    "net_amount" => $item->net_amount,
                    "compensate" => $item->compensate,
                ];
            });
            return view('bet.receipt-list', compact('data', 'date', 'no'));
        }catch (\Exception $exception){
            throwException($exception);
            return $exception->getMessage();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BetReceipt $betReceipt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BetReceipt $betReceipt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BetReceipt $betReceipt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BetReceipt $betReceipt)
    {
        //
    }


    public function betList(Request $request)
    {
        try{
            $date = $this->currentDate;
            if($request->has('date')){
                $date = $request->get('date');
            }
            $company_id = -1;
            if($request->has('com_id')){
                $company_id = $request->get('com_id');
            }
            $receiptNo = $request->no??null;
            $number = $request->number??null;
            $company = [
                [
                    "label" => "All Company",
                    "id" => -1,
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
            $data = $this->betModel->get();
            return view('bet.bet-list', compact('data', 'date', 'receiptNo', 'number', 'company', 'company_id'));
        }catch (\Exception $exception){
            throwException($exception);
            return $exception->getMessage();
        }
    }
}
