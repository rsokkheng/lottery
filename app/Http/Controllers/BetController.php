<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\BetLotteryPackageConfiguration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\throwException;

class BetController extends Controller
{
    public $betModel;
    public $currentDate;
    public function __construct(Bet $betModel)
    {
        $this->betModel = $betModel;
        $this->currentDate = Carbon::today()->format('Y-m-d');
    }

    public function getBetNumber(Request $request)
    {
        try {
            $date = $this->currentDate;
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            $user = Auth::user()??0;
            if ($user) {
                $user = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
            }
            $digits = BetLotteryPackageConfiguration::query()
            ->where('package_id', $user->package_id)
            ->orderBy('id')->get(['id', 'bet_type','has_special']);

            $company_id = null;
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
            $digit_type = "2D";
            if ($request->has('digit_type')) {
                $digit_type = $request->get('digit_type');
            }
            
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
            $data =$this->betModel
                ->with([
                'beReceipt',
                'user',
                    'betNumber'=> function ($q) {
                        $q->orderBy('id');
                    },
                    'betNumber.betNumberWin',
                'bePackageConfig',
                'betLotterySchedule'
            ])->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->when(!is_null($date), function ($q) use ($date) {
                $q->where('bet_date', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'));
                $q->where('bet_date', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
            })->when(!is_null($digit_type), function ($q) use ($digit_type) {
                $q->where('digit_format', '=', $digit_type);
            })->when(!is_null($company_id), function ($q) use ($company_id) {
                $q->where('company_id', '=', $company_id);
            })->when(!is_null($number), function ($q) use ($number) {
                $q->whereHas('betNumber', function ($query) use ($number) {
                    $query->where('generated_number', $number);
                });
            })
//                ->where(function ($q){
//                    $q->where('bet_date','2025-05-10')
//                        ->whereIn('bet_receipt_id',[115,116,117]);
//                })
                ->orderBy('company_id')
                ->orderBy('bet_receipt_id')
                ->orderBy('bet_schedule_id')
                ->get();
            return view('bet.bet-number', compact('data', 'date','company','company_id','digits','number'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
}
