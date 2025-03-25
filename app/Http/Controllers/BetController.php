<?php

namespace App\Http\Controllers;

use App\Models\Bet;
use App\Models\BetLotteryPackageConfiguration;
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
            $user = Auth::user();
            $digits = BetLotteryPackageConfiguration::query()->where('package_id', $user->package_id)
                    ->orderBy('id')->get(['id', 'bet_type']);

            $company_id = -1;
            if ($request->has('com_id')) {
                $company_id = $request->get('com_id');
            }
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
            $data =$this->betModel->with([
                'beReceipt',
                'user',
                'betNumber',
                'bePackageConfig'
            ])->get();

            return view('bet.bet-number', compact('data', 'date','company','company_id','digits','number'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
}
