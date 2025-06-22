<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Enums\HelperEnum;
use App\Models\AccountManagement;
use App\Models\BetReceipt;
use App\Models\BetWinning;
use Illuminate\Http\Request;
use App\Models\BalanceReport;
use App\Models\LotteryResult;
use App\Models\LotterySchedule;
use App\Models\BetWinningRecord;
use App\Models\BetLotterySchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\BalanceReportOutstanding;
use Spatie\Permission\Models\Permission;
use App\Models\BetLotteryPackageConfiguration;
use function PHPUnit\Framework\throwException;

class LotteryResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public string $currentDate;
    public string $currentDayName;
    public array $rollA = ['GiaiTam'];
    public array $rollB = ['GiaiDB'];

    public array $rolls = ['GiaiTam', 'GiaiBay','GiaiSau','GiaiNam','GiaiTu','GiaiBa','GiaiNhi','GiaiNhat','GiaiDB'];
    public array $roll7 = ['GiaiBay','GiaiSau','GiaiNam','GiaiTu','GiaiDB'];  // Roll 7 only have 6 prizes but GiaiTu check win result for only first row
    public array $rollParlay = ['GiaiTam', 'GiaiBay','GiaiSau','GiaiNam','GiaiTu','GiaiBa','GiaiNhi','GiaiNhat','GiaiDB']; // check all prizes

    public array $HanoiRollA = ['GiaiBay'];
    public array $HanoiRollB = ['GiaiDB'];
    public array $companies = [
        [
            "label" => "All Company",
            "id" => 0,
            "draw_time" => null
        ],
        [
            "label" => "4PM Company",
            "id" => 1,
            "draw_time" => '16:30:00'
        ],
        [
            "label" => "5PM Company",
            "id" => 2,
            "draw_time" => '17:30:00'
        ],
        [
            "label" => "6PM Company",
            "id" => 3,
            "draw_time" => '18:30:00'
        ]];
    public function __construct()
    {
        $this->currentDate = Carbon::today()->format('d/m/Y');
        $this->currentDayName = Carbon::today()->dayName;
    }

    public function index()
    {
        $data = Permission::orderBy('id','DESC')->get();
        return view('admin.lottery-result.index',compact('data'));
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
    public function show(LotteryResult $lotteryResult)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LotteryResult $lotteryResult)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LotteryResult $lotteryResult)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LotteryResult $lotteryResult)
    {
        //Hello world
    }

    public function getPrizeLevel($region): array
    {
        if($region === HelperEnum::MienBacDienToanSlug->value){
            return [
                "GiaiDB" => ["name"=>"Giải Đặc Biệt", "order_count"=>1, "input_length" => 5, "col_count"=>1, "row_count"=>1, "tailwind_class"=>'text-red-600 text-5xl max-md:text-3xl'],
                "GiaiNhat" => ["name"=>"Giải nhất", "order_count"=>1, "input_length" => 5, "col_count"=>1, "row_count"=>1, "tailwind_class"=>'text-black text-4xl max-md:text-xl'],
                "GiaiNhi" => ["name"=>"Giải nhì", "order_count"=>2, "input_length" => 5, "col_count"=>2, "row_count"=>1, "tailwind_class"=>'text-black text-4xl max-md:text-xl'],
                "GiaiBa" => ["name"=>"Giải ba", "order_count"=>6, "input_length" => 5, "col_count"=>3, "row_count"=>2, "tailwind_class"=>'text-black text-4xl max-md:text-xl'],
                "GiaiTu" => ["name"=>"Giải tư", "order_count"=>4, "input_length" => 4, "col_count"=>2, "row_count"=>2, "tailwind_class"=>'text-black text-4xl max-md:text-xl'],
                "GiaiNam" => ["name"=>"Giải năm", "order_count"=>6, "input_length" => 4, "col_count"=>3, "row_count"=>2, "tailwind_class"=>'text-black text-4xl max-md:text-xl'],
                "GiaiSau" => ["name"=>"Giải sáu", "order_count"=>3, "input_length" => 3, "col_count"=>3, "row_count"=>1, "tailwind_class"=>'text-blue-800 text-5xl max-md:text-2xl'],
                "GiaiBay" => ["name"=>"Giải bảy", "order_count"=>4, "input_length" => 2, "col_count"=>4, "row_count"=>1, "tailwind_class"=>'text-red-600 text-5xl max-md:text-2xl'],
            ];
        }else{
            return [
                "GiaiTam" => ["name"=>"Giải tám", "order_count"=>1, "input_length" => 2, "tailwind_class" => "text-5xl text-red-600 max-md:text-3xl", "roll" => 'a'],
                "GiaiBay" => ["name"=>"Giải bảy", "order_count"=>1, "input_length" => 3, "tailwind_class" => "text-5xl text-blue-800 max-md:text-3xl", "roll" => 'a'],
                "GiaiSau" => ["name"=>"Giải sáu", "order_count"=>3, "input_length" => 4, "tailwind_class" => "text-black text-4xl max-md:text-xl", "roll" => 'a'],
                "GiaiNam" => ["name"=>"Giải năm", "order_count"=>1, "input_length" => 4, "tailwind_class" => "text-black text-4xl max-md:text-xl", "roll" => 'a'],
                "GiaiTu" => ["name"=>"Giải tư", "order_count"=>7, "input_length" => 5, "tailwind_class" => "text-black text-4xl max-md:text-xl", "roll" => 'a'],
                "GiaiBa" => ["name"=>"Giải ba", "order_count"=>2, "input_length" => 5, "tailwind_class" => "text-black text-4xl max-md:text-xl", "roll" => 'a'],
                "GiaiNhi" => ["name"=>"Giải nhì", "order_count"=>1, "input_length" => 5, "tailwind_class" => "text-black text-4xl max-md:text-xl", "roll" => 'a'],
                "GiaiNhat" => ["name"=>"Giải nhất", "order_count"=>1, "input_length" => 5, "tailwind_class" => "text-black text-4xl max-md:text-xl", "roll" => 'a'],
                "GiaiDB" => ["name"=>"Giải Đặc Biệt", "order_count"=>1, "input_length" => 6, "tailwind_class" => "text-red-600 text-5xl max-md:text-2xl", "roll" => 'a']
            ];
        }
    }

    public function getCurrentScheduleResultFilter($day, $regionSlug, $provinceCode = null): array
    {
        return LotterySchedule::query()
            ->where('draw_day', $day)
            ->when($provinceCode!=null,function($q)use($provinceCode){
                $q->where('code', $provinceCode);
            })
            ->where('region_slug', $regionSlug)
            ->where('record_status_id',1)
            ->orderBy('company_id', 'asc')
            ->orderBy('sequence', 'asc')
            ->get()->toArray();
    }

    public function getLotteryResultFilter($date, $prize=null, $provinceCode=null, $scheduleId=null): array
    {
        $dateFormat = Carbon::parse($date)->format('Y-m-d');
        return LotteryResult::query()
                ->where('draw_date',$dateFormat)
                ->when($prize!=null,function($q) use ($prize){
                    $q->where('prize_level', $prize);
                })
                ->when($provinceCode!=null,function($q)use($provinceCode){
                    $q->where('province_code', $provinceCode);
                })
                ->when($scheduleId!=null,function($q)use($scheduleId){
                    $q->where('lottery_schedule_id', $scheduleId);
                })
                ->get()->toArray();
    }


    public function getMergeResult($date, $region): array
    {
//        $region =  HelperEnum::MienNamSlug->value;
        $prizes = $this->getPrizeLevel($region);
        $dateFormatted = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
//        $dateFormatted = '2025-01-16';
        $day = Carbon::parse($dateFormatted)->dayName;
        $schedule = $this->getCurrentScheduleResultFilter($day, $region);
        $result = [];
        foreach ($prizes as $key=>$prize) {
            foreach ($schedule as $item) {
                $result[$key]['prize_label'] = $prize['name'];
                $result[$key]['provinces'][$item['code']]['province_code'] = $item['code'];
                $result[$key]['provinces'][$item['code']]['province_name'] = $item['province'];
                $result[$key]['provinces'][$item['code']]['schedule_id'] = $item['id'];
                for($i=0; $i<$prize['order_count']; $i++){
                    $result[$key]['provinces'][$item['code']]['row_result'][$i]['prize_level'] = $key;
                    $result[$key]['provinces'][$item['code']]['row_result'][$i]['draw_date'] = $dateFormatted;
                    $result[$key]['provinces'][$item['code']]['row_result'][$i]['result_order'] = $i+1;
                    $result[$key]['provinces'][$item['code']]['row_result'][$i]['input_length'] = $prize['input_length'];
                    $result[$key]['provinces'][$item['code']]['row_result'][$i]['tailwind_class'] = $prize['tailwind_class'];
                    $result[$key]['provinces'][$item['code']]['row_result'][$i]['draw_date'] = $dateFormatted;
                    if($region === HelperEnum::MienBacDienToanSlug->value){
                        $result[$key]['provinces'][$item['code']]['row_result'][$i]['col_count'] = $prize['col_count'];
                        $result[$key]['provinces'][$item['code']]['row_result'][$i]['row_count'] = $prize['row_count'];
                    }
                    $getResult = $this->getLotteryResultFilter($dateFormatted, $key, null, $item['id']);
                    if(count($getResult)>0){
                        foreach ($getResult as $val){
                            if($val['result_order'] === $i+1){
                                $result[$key]['provinces'][$item['code']]['row_result'][$i]['result_id'] = $val['result_id']??0;
                                $result[$key]['provinces'][$item['code']]['row_result'][$i]['winning_number'] = $val['winning_number']??0;
                            }
                        }
                    }
                }
            }
        }
//        dd($result);
        return ['result'=>$result, 'schedule'=>$schedule];
    }

    public function isValidDateRequest($date) {
        $carbonDate = Carbon::createFromFormat('d/m/Y', $date);
        return $carbonDate && $carbonDate->format('d/m/Y') === $date;
    }


    public function indexMienNam()
    {
        $data = [
            'type' => HelperEnum::MienNamSlug->value,
            'url' => [
                'create' => 'admin.result.create-mien-nam',
                'index' => 'admin.result.index-mien-nam'
            ],
            'data' => []
        ];
        return view('admin.lottery-result.index',compact('data'));
    }

    public function createMienNam(Request $request)
    {
        try {
            $filterDate = $request['date'] ?? $this->currentDate;
            if(!$this->isValidDateRequest($filterDate)){
                return __('message.invalid-date-request');
            }
            $formResult = $this->getMergeResult($filterDate, HelperEnum::MienNamSlug->value);
            $data = [
                'type' => HelperEnum::MienNamSlug->value,
                'url' => [
                    'create' => 'admin.result.create-mien-nam',
                    'index' => 'admin.result.index-mien-nam'
                ],
                'current_date' => $filterDate,
                'form_result' => $formResult
            ];
            return view('admin.lottery-result.create', compact('data'));
        }catch (\Exception $exception){
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function storeWinningResult(Request $request)
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
//                $date = '2025-02-23';
//                $dayName = 'Sunday';
//                $time = '16:30:00';
                $resultTime = $this->getBetTime($resultRegion);
                $scheduleIdsByCurrentBet = $this->getPluckIdSchedule($dayName, $resultTime);
                $oldBetWinningRecords = BetWinningRecord::query()->whereHas('betLotteryResult', function ($query) use ($resultDate, $scheduleIdsByCurrentBet){
                        $query->where('draw_date', $resultDate)
                            ->whereIn('lottery_schedule_id', $scheduleIdsByCurrentBet);
                    });
                $oldBetWinningRecords->each(function ($record){
                    $record->betWinning()->forceDelete();
                });
                $oldBetWinningRecords->forceDelete();
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
                $getNormalWinNumber = $this->generateNormalWinBet($resultDate, $scheduleIdsByCurrentBet);
                $getHashWinNumber = $this->generateHashWinBet($resultDate, $scheduleIdsByCurrentBet);
//                $insertWinNumber = [...$getHashWinNumber];
                $insertWinNumber = [...$getNormalWinNumber,...$getHashWinNumber];
                if(count($insertWinNumber)) {
                    $recordsCreated = $this->insertBetWinning($insertWinNumber);
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
                BalanceReportOutstanding::where('date', Carbon::today()->format('Y-m-d'))
                ->where('company_id', $BetConfigCompany->company_id)
                ->update([
                    'amount' => 0,
                ]);
            }
            DB::commit();
            $reportDate = Carbon::today()->format('Y-m-d');
            $winAmountsByUser = BetReceipt::whereDate('date', $reportDate)
            ->selectRaw('user_id, SUM(compensate) as total_win_amount')
            ->groupBy('user_id')
            ->get();
            foreach ($winAmountsByUser as $userWin) {
                $user = AccountManagement::where('user_id', $userWin->user_id)->first();
                if ($user) {
                    $user->cash_balance = $userWin->total_win_amount;
                    $user->save();
                }
                if ($userWin->total_win_amount > 0) {
                    $User = User::find($userWin->user_id);
                    DB::table('balance_reports')->updateOrInsert(
                        [
                            'user_id' => $User->id,
                            'report_date' => $reportDate,
                        ],
                        [
                            'name_user' => $User->name,
                            'net_lose' => 0,
                            'net_win' => $userWin->total_win_amount,
                            'deposit' => 0,
                            'withdraw' => 0,
                            'adjustment' => 0,
                            'balance' => 0,
                        ]
                    );
                }
            }
             
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

    public function indexMienTrung()
    {
        $data = [
            'type' => HelperEnum::MienTrungSlug->value,
            'url' => [
                'create' => 'admin.result.create-mien-trung',
                'index' => 'admin.result.index-mien-trung'
            ],
            'data' => []
        ];
        return view('admin.lottery-result.index',compact('data'));
    }
    public function createMienTrung(Request $request)
    {
        try {
            $filterDate = $request['date'] ?? $this->currentDate;
            if (!$this->isValidDateRequest($filterDate)) {
                return __('message.invalid-date-request');
            }
            $formResult = $this->getMergeResult($filterDate, HelperEnum::MienTrungSlug->value);
            $data = [
                'type' => HelperEnum::MienTrungSlug->value,
                'url' => [
                    'create' => 'admin.result.create-mien-trung',
                    'index' => 'admin.result.index-mien-trung'
                ],
                'current_date' => $filterDate,
                'form_result' => $formResult
            ];
            return view('admin.lottery-result.create', compact('data'));
        }catch (\Exception $exception){
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function indexMienBac()
    {
        $data = [
            'type' => HelperEnum::MienBacDienToanSlug->value,
            'url' => [
                'create' => 'admin.result.create-mien-bac',
                'index' => 'admin.result.index-mien-bac'
            ],
            'data' => []
        ];
        return view('admin.lottery-result.index',compact('data'));
    }
    public function createMienBac(Request $request)
    {
        try {
            $filterDate = $request['date'] ?? $this->currentDate;
            if (!$this->isValidDateRequest($filterDate)) {
                return __('message.invalid-date-request');
            }
            $formResult = $this->getMergeResult($filterDate, HelperEnum::MienBacDienToanSlug->value);
            $data = [
                'type' => HelperEnum::MienBacDienToanSlug->value,
                'url' => [
                    'create' => 'admin.result.create-mien-bac',
                    'index' => 'admin.result.index-mien-bac'
                ],
                'current_date' => $filterDate,
                'form_result' => $formResult
            ];
            return view('admin.lottery-result.create', compact('data'));
        }catch (\Exception $exception){
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getBetResultBy(Request $request)
    {
        try {
            $region = $request['region'] ?? HelperEnum::MienNamSlug->value;
            if(!$this->validateRegion($region)){
                return __('Invalid region request');
            }
            $showDate = $request['date'] ?? $this->currentDate;
            if (!$this->isValidDateRequest($showDate)) {
                return __('message.invalid-date-request');
            }
            $formResult = $this->getMergeResult($showDate, $region);
            $data = [
                'region' => $this->getRegionArray($region),
                'type' => $region,
                'date_show' => $showDate,
                'form_result' => $formResult
            ];
            return view('bet.result-show', compact('data'));
        }catch (\Exception $exception){
            throwException($exception);
            return $exception->getMessage();
        }
    }

    public function getRegionArray($slug): array
    {
        switch ($slug){
            case HelperEnum::MienNamSlug->value:
                return ['slug'=>HelperEnum::MienNamSlug->value, 'name'=> __('lang.mien-nam')];
            case HelperEnum::MienTrungSlug->value:
                return ['slug'=>HelperEnum::MienTrungSlug->value, 'name'=> __('lang.mien-trung')];
            case HelperEnum::MienBacDienToanSlug->value:
                return ['slug'=>HelperEnum::MienBacDienToanSlug->value, 'name'=> __('lang.mien-bac')];
            default:
                return [];
        }
    }


    public function insertBetWinning($data){
        $betAmount = [];
        $sumAmount = 0;
        $save = [];
        if(count($data)){
            foreach ($data as $k=>$item){
                if(empty($betAmount)){
                    $betAmount = $item;
                    $sumAmount = $item['prize_amount'];
                }else{
                    if($betAmount['bet_id'] !== $item['bet_id']){
                        $sumAmount = $item['prize_amount'];
                        $betAmount = $item;
                    }else{
                        if(in_array($item['bet_type'],['RP2','RP3','RP4'])) {
                            if ($betAmount['bet_number_id'] !== $item['bet_number_id']) {
                                $sumAmount += $item['prize_amount'];
                                $betAmount = $item;
                            }
                        }else{
                            $sumAmount += $item['prize_amount'];
                            $betAmount = $item;
                        }
                    }
                }

                $matchThese = ['bet_id'=>$item['bet_id']??0,'bet_receipt_id'=>$item['receipt_id']];
                $betWin = BetWinning::query()->updateOrCreate($matchThese,['win_amount'=>$sumAmount]);
                $save[] = BetWinningRecord::query()->insert([
                    'bet_winning_id'=> $betWin->id,
                    'bet_number_id' => $item['bet_number_id'],
                    'result_id' => $item['result_id'],
                    'win_number' => $item['win_number']
                ]);
            }
        }
        return $save;
    }


    public function callGenerateWinNumber(){

//        $ddd = $this->matchWinNumberFromResultsRoll7('2025-03-29', 33, 154);
//        dd($ddd);
//        return $this->getWinningReport();
//        return $this->getPermutations('154');


//       $today = Carbon::today()->format('Y-m-d');
//        $dayName = Carbon::today()->dayName;
//        $resultTime = $this->getBetTime(HelperEnum::MienNamSlug->value);
//        $date = Carbon::today()->format('Y-m-d');
        $scheduleIds = [50];
        $date = '2025-05-10';

//
//        $original = explode("#", '51#52#35#55');
//        $countDuplicate = array_count_values($original);
//        $duplicateRPNumber = [];
//        foreach ($countDuplicate as $number => $count) {
//            if ($count > 1) {
//                $duplicateRPNumber[] = $number;
//            }
//        }
//        dd($original,$countDuplicate);

        $data1 = $this->generateNormalWinBet($date, $scheduleIds);
//        $data2 = $this->generateHashWinBet($date, $scheduleIds);
//        $data = [...$data1, ...$data2];
//        return $this->insertBetWinning($data);

//        $idSchedules = $this->getPluckIdSchedule($dayName, $resultTime);
//         $this->generateWinningNumbersCustom($date, $scheduleIds);

        //insert for type 2D, 3D, 4D
//        $getBetWin = $this->generateNormalWinBet($date, $scheduleIds);
//        return $getBetWin;
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


    public function getBetRoll($a, $b, $ab, $roll7, $roll, $rollParlay)
    {
        $getRoll = [];
        if ((float)$a){
            $getRoll = $this->rollA;
        }
        if ((float)$b){
            $getRoll = $this->rollB;
        }
        if ((float)$ab){
            $getRoll = [...$this->rollA, ...$this->rollB];
        }
        if ((float)$roll7){
            $getRoll = $this->roll7;
        }
        if ((float)$roll){
            $getRoll = $this->rolls;
        }
        if ((float)$rollParlay){
            $getRoll = $this->rollParlay;
        }
        return $getRoll;
    }

    public function getBetAmount($a, $b, $ab, $roll7, $roll, $rollParlay)
    {
        $getAmount = 0;
        if ((float)$a){
            $getAmount = (float)$a;
        }
        if ((float)$b){
            $getAmount = (float)$b;
        }
        if ((float)$ab){
            $getAmount = (float)$ab;
        }
        if ((float)$roll7){
            $getAmount = (float)$roll7;
        }
        if ((float)$roll){
            $getAmount = (float)$roll;
        }
        if ((float)$rollParlay){
            $getAmount = (float)$rollParlay;
        }
        return $getAmount;
    }

    public function generateNormalWinBet($date, $idSchedules): array
    {
//        $date = '2025-02-23';
//        $day = 'Sunday';
//        $time = '16:30:00';
        $getBetWinningNumber = [];
         DB::table('bets')
            ->select(
                'bet_numbers.*',
                'pkg_con.price as pkg_price',
                'pkg_con.bet_type as bet_type',
                'bets.bet_schedule_id',
                'bets.number_format as original_number',
                'schedule.region_slug',
                'bets.bet_receipt_id'
            )
            ->join('bet_numbers','bet_numbers.bet_id','=', 'bets.id')
            ->join('bet_lottery_schedules as schedule','schedule.id','=','bets.bet_schedule_id')
            ->join('bet_package_configurations as pkg_con','pkg_con.id','=', 'bets.bet_package_config_id')
            ->whereIn('bets.bet_schedule_id',$idSchedules)
            ->whereIn('pkg_con.bet_type', ['2D','3D','4D'])
//             ->where('bets.id', 9)
            ->orderBy('bets.id')
            ->orderBy('bet_numbers.id')
            ->lazy()
            ->each(function ($bet) use (&$getBetWinningNumber, $date) {
                $getBetRoll = $this->getBetRoll($bet->a_amount, $bet->b_amount, $bet->ab_amount, $bet->roll7_amount, $bet->roll_amount, $bet->roll_parlay_amount);
                $getAmount = $this->getBetAmount($bet->a_amount, $bet->b_amount, $bet->ab_amount, $bet->roll7_amount, $bet->roll_amount, $bet->roll_parlay_amount);
                if ($bet->region_slug === HelperEnum::MienBacDienToanSlug->value) {
                    $rollA = $this->HanoiRollA;
                    $rollB = $this->HanoiRollB;
                    if((float)$bet->a_amount){
                        $getBetRoll = $rollA;
                    }
                    if((float)$bet->b_amount){
                        $getBetRoll = $rollB;
                    }
                    if((float)$bet->ab_amount){
                        $getBetRoll = [...$rollA, ...$rollB];
                    }
                }

                if((float)$bet->roll7_amount){
                    $getMatched = $this->matchWinNumberFromResultsRoll7($date, $bet->bet_schedule_id, $bet->generated_number);
                    if (count($getMatched)) {
                        $totalAmount = (float)$bet->roll7_amount * (float)$bet->pkg_price;
                        foreach ($getMatched as $val) {
                            $getBetWinningNumber[] = [
                                'bet_id' => $bet->bet_id,
                                'bet_number_id' => $bet->id,
                                'receipt_id' => $bet->bet_receipt_id,
                                'bet_type' => $bet->bet_type,
                                'win_number' => $val->bet_number,
                                'result_id' => $val->result_id,
                                'prize_amount' => $totalAmount
                            ];
                        }
                    }
                }else{
                    $getMatched = $this->matchWinNumberFromResults($date, $bet->bet_schedule_id, $bet->generated_number, $getBetRoll);
                    if (count($getMatched)) {
                        $totalAmount = $getAmount * (float)$bet->pkg_price;
                        foreach ($getMatched as $val) {
                            $getBetWinningNumber[] = [
                                'bet_id' => $bet->bet_id,
                                'bet_number_id' => $bet->id,
                                'receipt_id' => $bet->bet_receipt_id,
                                'bet_type' => $bet->bet_type,
                                'win_number' => $val->bet_number,
                                'result_id' => $val->result_id,
                                'prize_amount' => $totalAmount
                            ];
                        }
                    }
                }

            });

        return $getBetWinningNumber;
    }

    public function getPermutations($number) {
        $digits = str_split($number); // Convert number to array of digits
        $uniquePermutations = [];
        // If all digits are the same, return the number itself
        if (count(array_unique($digits)) === 1) {
            return [$number];
        }
        $this->permute($digits, 0, count($digits) - 1, $uniquePermutations);

        return array_values($uniquePermutations); // Return unique permutations
    }

    public function permute(&$digits, $left, $right, &$uniquePermutations) {
        if ($left == $right) {
            $perm = implode('', $digits);
            if (!isset($uniquePermutations[$perm])) {
                $uniquePermutations[$perm] = $perm;
            }
        } else {
            for ($i = $left; $i <= $right; $i++) {
                $this->swap($digits, $left, $i);
                $this->permute($digits, $left + 1, $right, $uniquePermutations);
                $this->swap($digits, $left, $i); // Backtrack
            }
        }
    }

    public function swap(&$array, $i, $j) {
        $temp = $array[$i];
        $array[$i] = $array[$j];
        $array[$j] = $temp;
    }

    public function generateHashWinBet($date, $idSchedules): array
    {
        $getBetWinningNumber = [];
        DB::table('bets')
            ->select(
                'bet_numbers.*',
                'pkg_con.price as pkg_price',
                'pkg_con.bet_type as bet_type',
                'pkg_con.has_special as has_special',
                'bets.bet_schedule_id as bet_schedule_id',
                'bets.number_format as original_number',
                'bets.bet_receipt_id'
            )
            ->join('bet_numbers','bet_numbers.bet_id','=', 'bets.id')
            ->join('bet_package_configurations as pkg_con','pkg_con.id','=', 'bets.bet_package_config_id')
            ->whereIn('bets.bet_schedule_id', $idSchedules)
            ->whereIn('pkg_con.bet_type', ['RP2','RP3','RP4'])
            ->orderBy('bets.id')
            ->orderBy('bet_numbers.id')
            ->lazy()
            ->each(function ($bet) use (&$getBetWinningNumber, $date) {
                $numberArr = explode("#", $bet->generated_number);
                $countDuplicate = array_count_values($numberArr);
                $getMatched = [];
                foreach ($countDuplicate as $number => $count) {
                    if($count > 1) {
                        $getResult = $this->matchWinNumberFromResults($date, $bet->bet_schedule_id, $number, $this->rollParlay);
                        if(count($getResult)){
                                for($i=0;$i<$count;$i++){
                                    $getMatched[] = [
                                        'number'=>$number,
                                        'results'=>[]
                                    ];
                                }
                                $j = 0;
                                foreach ($getResult as $val){
                                    $getMatched[$j]['results'][] = $val;
                                    $j++;
                                    if($j == $count){
                                        $j = 0;
                                    }
                                }
                        }
                    }else{
                        $getResult = $this->matchWinNumberFromResults($date, $bet->bet_schedule_id, $number, $this->rollParlay);
                        $getMatched[] = [
                            'number'=>$number,
                            'results'=>$getResult
                        ];
                    }
                }

                $amount = $bet->roll_parlay_amount;
                $matchTimes = null;
                if(count($getMatched)>1){
                    foreach ($getMatched as $match){
                        if($matchTimes === null){
                            $matchTimes = count($match['results']);
                        }else{
                            if($matchTimes > count($match['results'])){
                                $matchTimes = count($match['results']);
                            }
                        }
                    }
                }else{
                    $matchTimes = 0;
                }

                if($matchTimes){
                    foreach ($getMatched as $match){
                        $totalAmount = $amount * $matchTimes * $bet->pkg_price;
                        foreach ($match['results'] as $k => $val){
                            if($k < $matchTimes){
                                $getBetWinningNumber[] = [
                                    'bet_id' => $bet->bet_id,
                                    'receipt_id' => $bet->bet_receipt_id,
                                    'bet_type' => $bet->bet_type,
                                    'win_number' => $val->bet_number,
                                    'result_id' => $val->result_id,
                                    'bet_number_id' => $bet->id,
                                    'prize_amount' => $totalAmount
                                ];
                            }
                        }
                    }
                }
            });
        return $getBetWinningNumber;
    }


    public function getPluckIdSchedule($day, $drawTime): array
    {
        return LotterySchedule::query()
            ->where('draw_day',$day)
            ->where('draw_time',$drawTime)
            ->pluck('id')
            ->toArray();
    }

    public function getBetResultByScheduleIds($date, $ids): array
    {
        return LotteryResult::query()
            ->select('result_id','winning_number','lottery_schedule_id')
            ->where('draw_date', $date)
            ->whereIn('lottery_schedule_id', $ids)
            ->get()->toArray();
    }

    public function matchWinNumberFromResults($date, $scheduleId, $number, $roll = []): array
    {
        return DB::table('bet_lottery_results')
            ->select('result_id', DB::raw($number.' as bet_number'))
            ->where('draw_date', $date)
            ->where('lottery_schedule_id', $scheduleId)
            ->whereIn('prize_level', $roll)
            ->where('winning_number', 'like', '%'.$number)
            ->orderBy('result_id')
            ->get()
            ->toArray();
    }

    public function matchWinNumberFromResultsRoll7($date, $scheduleId, $number): array
    {
        return DB::table('bet_lottery_results')
            ->select('result_id', DB::raw($number.' as bet_number'))
//            ->whereIn('prize_level', ['GiaiBay','GiaiSau','GiaiNam','GiaiDB'])
//            ->whereIn('prize_level', $this->roll7)
            ->where(function ($q) use ($number, $date, $scheduleId){
                $q->whereIn('prize_level', ['GiaiBay','GiaiSau','GiaiNam','GiaiDB'])
                    ->where('lottery_schedule_id', $scheduleId)
                    ->where('draw_date', $date)
                    ->where('winning_number', 'like', '%'.$number);
            })
            ->orWhere(function ($q) use ($number, $date, $scheduleId){
                $q->where('prize_level', 'GiaiTu')
                    ->where('result_order', 1)
                    ->where('lottery_schedule_id', $scheduleId)
                    ->where('draw_date', $date)
                    ->where('winning_number', 'like', '%'.$number);
            })
            ->orderBy('result_id')
            ->get()
            ->toArray();
    }

    public function matchWinNumberFromResult($date, $scheduleId, $number, $resultIds): int | null
    {
        return DB::table('bet_lottery_results')
            ->where('draw_date', $date)
            ->whereNotIn('result_id', $resultIds)
            ->where('lottery_schedule_id',$scheduleId)
            ->where('winning_number', 'like', '%'.$number)
            ->orderBy('result_id')
            ->first()?->result_id;
    }


    public function validateRegion($region){
        return in_array($region,[HelperEnum::MienNamSlug->value, HelperEnum::MienTrungSlug->value, HelperEnum::MienBacDienToanSlug->value]);
    }

    public function getWinningReport(Request $request)
    {
        try{
            $date = date('Y-m-d');
            $companies = $this->companies;
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            $number = $request->number ?? null;
            $company = -1;
            if ($request->has('company')) {
                $company = $request->get('company');
            }
            $user = Auth::user() ?? 0;
            if ($user) {
                $user = User::find($user->id);
                $roles = $user->roles->pluck('name')->toArray(); // Get role names as an array
            }

            $data = [];
            DB::table('bet_winning_records as record')
                ->select(
                    'bet_winning.bet_id',
                    'record.bet_number_id',
                    'bet_winning.win_amount as compensate',
                    DB::raw('COUNT(record.bet_number_id) as count_bet_number'),
                    'record.bet_number_id',
                    'pkg_con.bet_type',
                    'pkg_con.rate as net',
                    'pkg_con.price as odds',
                    'bets.bet_date',
                    'bet_numbers.generated_number as generated_number',
                    'bet_numbers.total_amount as turnover',
                    'schedule.province_en',
                    'bet_receipts.receipt_no',
                    'bet_receipts.receipt_no',
                    'bet_numbers.a_amount',
                    'bet_numbers.b_amount',
                    'bet_numbers.ab_amount',
                    'bet_numbers.roll_amount',
                    'bet_numbers.roll7_amount',
                    'bet_numbers.roll_parlay_amount',
                    DB::raw('sum(bet_numbers.a_check+bet_numbers.b_check+bet_numbers.ab_check+bet_numbers.roll_check+bet_numbers.roll7_check+bet_numbers.roll_parlay_check) as sum_check'),
                    'users.name as account'
                )
                ->join('bet_numbers','bet_numbers.id','=', 'record.bet_number_id')
                ->join('bet_winning','bet_winning.id','=', 'record.bet_winning_id')
                ->join('bets','bet_winning.bet_id','=', 'bets.id')
                ->join('users','users.id','=', 'bets.user_id')
                ->join('bet_receipts','bet_receipts.id','=', 'bet_winning.bet_receipt_id')
                ->join('bet_lottery_schedules as schedule','schedule.id','=', 'bets.bet_schedule_id')
                ->join('bet_package_configurations as pkg_con','pkg_con.id','=', 'bets.bet_package_config_id')
                ->where('bets.bet_date', $date)
                ->when($company, function ($q) use ($company){
                    $q->when($company == 1, function ($q2){
                        $q2->where('schedule.draw_time', '16:30:00');
                    });
                    $q->when($company == 2, function ($q2){
                        $q2->where('schedule.draw_time', '17:30:00');
                    });
                    $q->when($company == 3, function ($q2){
                        $q2->where('schedule.draw_time', '18:30:00');
                    });
                })->when(in_array('manager', $roles), function ($q) use ($user) {
                    // Get all users under this manager
                    $memberIds = User::where('manager_id', $user->id)
                                    ->whereDoesntHave('roles', fn($query) => $query->where('name', 'admin'))
                                    ->pluck('id')
                                    ->toArray();
                    $q->whereIn('bets.user_id', $memberIds);
                })->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                    $q->where('bets.user_id', $user->id);
                })
                ->when($number, function ($q) use ($number){
                    $q->where('bets.number_format', 'like','%'.$number.'%');
                })
                ->orderBy('bet_winning.bet_id')
                ->orderBy('record.bet_winning_id')
                ->orderBy('record.bet_number_id')
                ->groupBy('bet_winning.bet_id')
                ->groupBy('record.bet_winning_id')
                ->groupBy('record.bet_number_id')
                ->groupBy('bet_type')
                ->groupBy('pkg_con.rate')
                ->groupBy('pkg_con.price')
                ->groupBy('bets.bet_date')
                ->groupBy('bets.total_amount')
                ->groupBy('bet_winning.win_amount')
                ->groupBy('schedule.province_en')
                ->groupBy('bet_receipts.receipt_no')
                ->groupBy('bet_numbers.generated_number')
                ->groupBy('bet_numbers.a_amount')
                ->groupBy('bet_numbers.total_amount')
                ->groupBy('bet_numbers.b_amount')
                ->groupBy('bet_numbers.ab_amount')
                ->groupBy('bet_numbers.roll_amount')
                ->groupBy('bet_numbers.roll7_amount')
                ->groupBy('bet_numbers.roll_parlay_amount')
                ->groupBy('users.name')
                ->each(function ($record) use (&$data) {
                    $getBetRoll = '';
                    $amount = 0;
                    if($record->a_amount > 0){
                        $amount = $amount+$record->a_amount;
                        $getBetRoll = $getBetRoll.'Head';
                    }
                    if($record->b_amount > 0){
                        $amount = $amount+$record->b_amount;
                        if($getBetRoll){
                            $getBetRoll = $getBetRoll.',&nbsp;';
                        }
                        $getBetRoll = $getBetRoll.'Last';
                    }
                    if($record->ab_amount > 0){
                        $amount = $amount+$record->ab_amount;
                        if($getBetRoll){
                            $getBetRoll = $getBetRoll.',&nbsp;';
                        }
                        $getBetRoll = $getBetRoll.'Head+Last';
                    }
                    if($record->roll_amount > 0){
                        $amount = $amount+$record->roll_amount;
                        if($getBetRoll){
                            $getBetRoll = $getBetRoll.',&nbsp;';
                        }
                        $getBetRoll = $getBetRoll.'Roll';
                    }
                    if($record->roll7_amount > 0){
                        $amount = $amount+$record->roll7_amount;
                        if($getBetRoll){
                            $getBetRoll = $getBetRoll.',&nbsp;';
                        }
                        $getBetRoll = $getBetRoll.'Roll7';
                    }
                    if($record->roll_parlay_amount > 0){
                        $amount = $amount+$record->roll_parlay_amount;
                        if($getBetRoll){
                            $getBetRoll = $getBetRoll.',&nbsp;';
                        }
                        $getBetRoll = $getBetRoll.'Roll Parlay';
                    }

                    $betType = $record->bet_type;
                    if($record->sum_check){
                        $betType = $record->bet_type.'(x)';
                    }

                    $prepareData = [
                        'bet_number_id' => $record->bet_number_id,
                        'bet_id' => $record->bet_id,
                        'account' => $record->account,
                        'amount' => $amount,
                        'net' => $record->net,
                        'odds' => $record->odds,
                        'bet_type' => $betType,
                        'original_number' => (string)$record->generated_number,
                        'company' => $record->province_en,
                        'game' => $getBetRoll,
                        'receipt_no' => $record->receipt_no,
                        'bet_date' => $record->bet_date,
                    ];
                    $commission = $record->turnover - ($record->turnover * $record->net / 100);
                    $netAmount = $record->turnover * $record->net / 100;
                    $prepareData['win_number'] = $record->generated_number;
                    $prepareData['turnover'] = $record->turnover;
                    $prepareData['commission'] = $commission;
                    $prepareData['net_amount'] = $netAmount;
//                    if(in_array($record->bet_type, ['RP2','RP3','RP4'])){
//                        $compensate = ($record->odds * $record->count_bet_number) / 2;
//                    }else{
                        $compensate = $record->compensate;
//                    }
                    $prepareData['compensate'] = $compensate;
                    $data[] = $prepareData;

                });
            return view('bet.report-winning', compact('data','companies', 'date', 'number', 'company'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

}
