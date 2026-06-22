<?php

namespace App\Http\Controllers;

use App\Enums\HelperEnum;
use App\Models\AccountUSD;
use App\Models\AccountVND;
use App\Models\BetLotterySchedule;
use App\Models\BetReceipt;
use App\Models\BetReceiptUSD;
use App\Models\BetWinningRecord;
use App\Models\BetWinningRecordUSD;
use App\Models\LotteryResult;
use App\Models\LotterySchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use function PHPUnit\Framework\throwException;

class WinningRecordController extends Controller
{

    public $resultVNDController;
    public $resultUSDController;
    public function __construct()
    {
        $this->currentDate = Carbon::today()->format('d/m/Y');
        $this->currentDayName = Carbon::today()->dayName;
        $this->resultVNDController = new LotteryResultController();
        $this->resultUSDController = new LotteryResultUSDController();
    }

    public function storeWinningRecord(Request $request)
    {
        try {
            DB::beginTransaction();
            $form = $request->all();
            $betResult = new LotteryResult();
            $BetConfigCompany = BetLotterySchedule::where('region_slug',$request->result_region)->first();

            if (isset($form['data']) && count($form['data'])) {
                $resultRegion = $form['result_region']??'';
                $resultDate = Carbon::createFromFormat('d/m/Y', $form['data'][0]['result_date'])->format('Y-m-d');
                $dayName = Carbon::createFromFormat('d/m/Y', $form['data'][0]['result_date'])->dayName;
                if(strtotime(Carbon::today()->format('Y-m-d')) < strtotime($resultDate)){
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid result date!',
                    ], 500);
                }
                $resultTime = $this->getBetTime($resultRegion);
                $scheduleIdsByCurrentBet = $this->getPluckIdSchedule($dayName, $resultTime);

                // DELETE WINNING RECORD FOR VND
                $oldBetWinningRecords = BetWinningRecord::query()->whereHas('betLotteryResult', function ($query) use ($resultDate, $scheduleIdsByCurrentBet){
                    $query->where('draw_date', $resultDate)
                        ->whereIn('lottery_schedule_id', $scheduleIdsByCurrentBet);
                });
                $oldBetWinningRecords->each(function ($record){
                    $record->betWinning()->forceDelete();
                });
                $oldBetWinningRecords->forceDelete();

                // DELETE WINNING RECORD FOR USD
                $oldBetWinningRecordUSD = BetWinningRecordUSD::query()->whereHas('betLotteryResult', function ($query) use ($resultDate, $scheduleIdsByCurrentBet){
                    $query->where('draw_date', $resultDate)
                        ->whereIn('lottery_schedule_id', $scheduleIdsByCurrentBet);
                });
                $oldBetWinningRecordUSD->each(function ($record){
                    $record->betWinningUSD()->forceDelete();
                });
                $oldBetWinningRecordUSD->forceDelete();

                # CHECK UPDATE RESULT
                foreach ($form['data'] as $item) {
                    $betResult->newQuery()->upsert([
                        [
                            'draw_date' => $resultDate,
                            'province_code' => $item['province_code'],
                            'prize_level' => $item['prize_level'],
                            'winning_number' => $item['winning_number'],
                            'result_order' => $item['result_order'],
                            'lottery_schedule_id' => $item['schedule_id']
                        ]
                    ],
                        uniqueBy: ['draw_date', 'province_code', 'prize_level', 'result_order', 'lottery_schedule_id'],
                        update: ['winning_number']
                    );
                }


                # INSERT RECORDS VND
                $getNormalWinNumberVND = $this->resultVNDController->generateNormalWinBet($resultDate, $scheduleIdsByCurrentBet);
                $getHashWinNumberVND = $this->resultVNDController->generateHashWinBet($resultDate, $scheduleIdsByCurrentBet);
                $insertWinNumberVND = [...$getNormalWinNumberVND,...$getHashWinNumberVND];
                if(count($insertWinNumberVND)) {
                    $recordsCreated = $this->resultVNDController->insertBetWinning($insertWinNumberVND,$resultDate);
                    if (count($recordsCreated)) {
                        DB::table('bet_winning as winning')
                            ->select('winning.bet_receipt_id', DB::raw('SUM(winning.win_amount) as sum_amount'))
                            ->whereDate('winning.created_at', date('Y-m-d'))
                            ->orderBy('winning.bet_receipt_id')
                            ->groupBy('winning.bet_receipt_id')
                            ->each(function ($winning){
                                BetReceipt::where('id', $winning->bet_receipt_id)->update(['compensate' => $winning->sum_amount]);
                            });
                    }
                }

                # INSERT RECORDS USD
                $getNormalWinNumberUSD = $this->resultUSDController->generateNormalWinBet($resultDate, $scheduleIdsByCurrentBet);
                $getHashWinNumberUSD = $this->resultUSDController->generateHashWinBet($resultDate, $scheduleIdsByCurrentBet);
                $insertWinNumberUSD = [...$getNormalWinNumberUSD,...$getHashWinNumberUSD];
                if(count($insertWinNumberUSD)) {
                    $recordsCreated = $this->resultUSDController->insertBetWinning($insertWinNumberUSD,$resultDate);
                    if (count($recordsCreated)) {
                        DB::table('bet_winning_usd as winning')
                            ->select('winning.bet_receipt_id', DB::raw('SUM(winning.win_amount) as sum_amount'))
                            ->whereDate('winning.created_at', date('Y-m-d'))
                            ->orderBy('winning.bet_receipt_id')
                            ->groupBy('winning.bet_receipt_id')
                            ->each(function ($winning) {
                                BetReceiptUSD::query()->find($winning->bet_receipt_id)->update(['compensate' => $winning->sum_amount]);
                            });
                    }
                }


            }

            $reportDate = $resultDate ?? Carbon::today()->format('Y-m-d');
            $winAmountsByUser = BetReceipt::whereDate('date', $reportDate)
                    ->selectRaw('CAST(user_id AS UNSIGNED) as user_id, SUM(compensate) as total_win_amount')
                    ->groupBy('user_id')
                    ->get();
                
                $winAmountsByUserUSD = BetReceiptUSD::whereDate('date', $reportDate)
                    ->selectRaw('CAST(user_id AS UNSIGNED) as user_id, SUM(compensate) as total_win_amount')
                    ->groupBy('user_id')
                    ->get();
                
                $merged = collect($winAmountsByUser)->merge($winAmountsByUserUSD);
                
                // Force consistent user_id and clean group/sum
                $winAmountMerged = $merged
                ->groupBy(function ($item) {
                    return (int) $item->user_id;
                })
                ->map(function ($group) {
                    return (object) [
                        'user_id' => (int) $group->first()->user_id,
                        'total_win_amount' => $group->sum('total_win_amount'),
                    ];
                })
                ->filter(function ($item) {
                    return $item->total_win_amount > 0;
                })
                ->values(); // reset the index

                if ($winAmountMerged->isNotEmpty()) {
                    foreach ($winAmountMerged as $userWin) {
                        $User = User::find($userWin->user_id);
                        if (!$User) continue;
                        $currency     = strtoupper($User->currency ?? 'VND');
                        $acctModel    = $currency === 'USD' ? AccountUSD::class : AccountVND::class;
                        $acct         = $acctModel::where('user_id', $User->id)->first();
                        if ($acct) {
                            $acct->credit_balance += $userWin->total_win_amount;
                            $acct->save();
                        } else {
                            $acctModel::create([
                                'user_id'          => $User->id,
                                'credit_balance'   => $userWin->total_win_amount,
                                'record_status_id' => 1,
                            ]);
                        }
                    }
                }
                

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'save success'
            ]);
        }catch (\Exception $e){
            DB::rollBack();
            throwException($e);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBetTime($region): string
    {
        switch ($region){
            case HelperEnum::MienNamSlug->value:
                return '16:30:00';
            case HelperEnum::MienTrungSlug->value:
                return '17:30:00';
            case HelperEnum::MienBacDienToanSlug->value:
                return '18:30:00';
            default:
                return '';
        }
    }

    public function getPluckIdSchedule($day, $drawTime): array
    {
        return LotterySchedule::query()
            ->where('draw_day',$day)
            ->where('draw_time',$drawTime)
            ->pluck('id')
            ->toArray();
    }

}
