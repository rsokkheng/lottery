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
                'bePackageConfig',
                'betLotterySchedule'
            ])->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->when(!is_null($date), function ($q) use ($date) {
                $q->where('bet_date', '>=', Carbon::parse($date)->startOfDay()->format('Y-m-d H:i:s'));
                $q->where('bet_date', '<=', Carbon::parse($date)->endOfDay()->format('Y-m-d H:i:s'));
            })->get();

            return view('bet.bet-number', compact('data', 'date','company','company_id','digits','number'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }
}
