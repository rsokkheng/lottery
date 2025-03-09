<?php

namespace App\Http\Controllers;

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

    public function __construct(BetReceipt $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        try{
            $date = $request->date??null;
            $no = $request->no??null;
//            $ddd = Carbon::createFromFormat('Y-m-d', $date)->firsto();
//            $ddd = Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s');
//            dd($ddd);
//            dd($date, $no, is_null($date));
            $data = $this->model->newQuery()->with(['user'])
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
//            dd($data);
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
}
