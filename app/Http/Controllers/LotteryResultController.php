<?php

namespace App\Http\Controllers;

use App\Enums\HelperEnum;
use App\Models\BetWinningRecord;
use App\Models\LotteryResult;
use App\Models\LotterySchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use function PHPUnit\Framework\throwException;

class LotteryResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public string $currentDate;
    public string $currentDayName;

    public array $rollA = ['GiaiDB'];
    public array $rollB = ['GiaiTam'];
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
                BetWinningRecord::query()->whereHas('betLotteryResult', function ($query) use ($resultDate, $scheduleIdsByCurrentBet){
                        $query->where('draw_date', $resultDate)
                            ->whereIn('lottery_schedule_id', $scheduleIdsByCurrentBet);
                    })->forceDelete();
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
//                dd($getNormalWinNumber);
//                $getHashWinNumber = [];
                $getHashWinNumber = $this->generateHashWinBet($resultDate, $scheduleIdsByCurrentBet);
                $insertWinNumber = [...$getNormalWinNumber,...$getHashWinNumber];
                BetWinningRecord::insert($insertWinNumber);
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


    public function callGenerateWinNumber(){

//        $ddd = $this->matchWinNumberFromResultsRoll7('2025-03-29', 33, 154);
//        dd($ddd);
//        return $this->getWinningReport();
        return $this->getPermutations('154');


//       $today = Carbon::today()->format('Y-m-d');
//        $dayName = Carbon::today()->dayName;
//        $resultTime = $this->getBetTime(HelperEnum::MienNamSlug->value);
//        $date = Carbon::today()->format('Y-m-d');
//        $scheduleIds = [33];
//        $date = '2025-03-29';
//        return $this->generateNormalWinBet($date, $scheduleIds);
//        return $this->generateHashWinBet($date, $scheduleIds);


//        $idSchedules = $this->getPluckIdSchedule($dayName, $resultTime);
//         $this->generateWinningNumbersCustom($date, $scheduleIds);

        //insert for type 2D, 3D, 4D
//        $getBetWin = $this->generateNormalWinBet($date, $scheduleIds);
//        $this->insertWinningRecords($date, $scheduleIds, $getBetWin);
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


    public function insertWinningRecords($date, $scheduleIds ,$insertRecords){
         BetWinningRecord::whereHas('betLotteryResult', function ($query) use ($date, $scheduleIds){
                $query->where('draw_date', $date)
                    ->whereIn('lottery_schedule_id', $scheduleIds);
            })->forceDelete();
        BetWinningRecord::insert($insertRecords);
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
                'schedule.region_slug'
            )
            ->join('bet_numbers','bet_numbers.bet_id','=', 'bets.id')
            ->join('bet_lottery_schedules as schedule','schedule.id','=','bets.bet_schedule_id')
            ->join('bet_package_configurations as pkg_con','pkg_con.id','=', 'bets.bet_package_config_id')
            ->whereIn('bets.bet_schedule_id',$idSchedules)
            ->whereIn('pkg_con.bet_type', ['2D','3D','4D'])
            ->orderBy('bets.id')
            ->orderBy('bet_numbers.id')
            ->lazy()
            ->each(function ($bet) use (&$getBetWinningNumber, $date) {
//                $rollChecked = [];
//                $rollUnChecked = [];
                $reverseNumber = [];
                $betByRoll = [];
                if($bet->a_check || $bet->b_check || $bet->ab_check || $bet->roll_check || $bet->roll7_check) {
                    //bet 23 => 23, 32
                    //bet 234 => 234,243,324,342,423,432
                    $reverseNumber = $this->getPermutations($bet->generated_number);
                }

                if($bet->region_slug === HelperEnum::MienBacDienToanSlug){
                    $rollA = $this->HanoiRollA;
                    $rollB = $this->HanoiRollB;
                }else{
                    $rollA = $this->rollA;
                    $rollB = $this->rollB;
                }

                if($bet->bet_type === '2D'){
                    if ($bet->a_amount){
                        $rollChecked = [];
                        $rollUnChecked = [];
                        if($bet->a_check){
                            $rollChecked = $rollA;
                        }else{
                            $rollUnChecked = $rollA;
                        }
                        array_push($betByRoll, array('amount' => $bet->a_amount, 'rollCheck' => $rollChecked, 'rollUncheck' => $rollUnChecked));
                    }
                    if ($bet->b_amount){
                        $rollChecked = [];
                        $rollUnChecked = [];
                        if($bet->b_check){
                            $rollChecked = $rollB;
                        }else{
                            $rollUnChecked = $rollB;
                        }
                        array_push($betByRoll, array('amount' => $bet->b_amount, 'rollCheck' => $rollChecked, 'rollUncheck' => $rollUnChecked));
                    }
                    if ($bet->ab_amount) {
                        $rollChecked = [];
                        $rollUnChecked = [];
                        if($bet->ab_check){
                            array_push($rollChecked, ...$rollA, ...$rollB);
                        }else{
                            array_push($rollUnChecked, ...$rollA, ...$rollB);
                        }
                        array_push($betByRoll, array('amount' => $bet->ab_amount, 'rollCheck' => $rollChecked, 'rollUncheck' => $rollUnChecked));
                    }

                    if ($bet->roll_amount) {
                        $rollChecked = [];
                        $rollUnChecked = [];
                        if($bet->roll_check){
                            array_push($rollChecked, ...$this->rolls);
                        }else{
                            array_push($rollUnChecked, ...$this->rolls);
                        }
                        array_push($betByRoll, array('amount' => $bet->roll_amount, 'rollCheck' => $rollChecked, 'rollUncheck' => $rollUnChecked));
                    }

                }else if($bet->bet_type === '3D'){
                    if ($bet->roll_amount) {
                        $rollChecked = [];
                        $rollUnChecked = [];
                        if($bet->roll_check){
                            array_push($rollChecked, ...$this->rolls);
                        }else{
                            array_push($rollUnChecked, ...$this->rolls);
                        }
                        array_push($betByRoll, array('amount' => $bet->roll_amount, 'rollCheck' => $rollChecked, 'rollUncheck' => $rollUnChecked));
                    }
                    if ($bet->roll7_amount) {
                        if($bet->roll7_check){
                            foreach ($reverseNumber as $betNumber){
                                $getMatched = $this->matchWinNumberFromResultsRoll7($date, $bet->bet_schedule_id, $betNumber);
                                if(count($getMatched)){
                                    $totalAmount = $bet->roll7_amount * $bet->pkg_price;
                                    foreach ($getMatched as $val){
                                        $getBetWinningNumber[] = [
                                            'bet_id' => $bet->bet_id,
                                            'win_number' => $val->bet_number,
                                            'result_id' => $val->result_id,
                                            'prize_amount' => $totalAmount
                                        ];
                                    }
                                }
                            }
                        }else{
                            $getMatched = $this->matchWinNumberFromResultsRoll7($date, $bet->bet_schedule_id, $bet->generated_number);
                            if(count($getMatched)){
                                $totalAmount = $bet->roll7_amount * $bet->pkg_price;
                                foreach ($getMatched as $val){
                                    $getBetWinningNumber[] = [
                                        'bet_id' => $bet->bet_id,
                                        'win_number' => $val->bet_number,
                                        'result_id' => $val->result_id,
                                        'prize_amount' => $totalAmount
                                    ];
                                }
                            }
                        }
                    }
                }else{
                    if ($bet->roll_amount) {
                        $rollChecked = [];
                        $rollUnChecked = [];
                        if($bet->roll_check){
                            array_push($rollChecked, ...$this->rolls);
                        }else{
                            array_push($rollUnChecked, ...$this->rolls);
                        }
                        array_push($betByRoll, array('amount' => $bet->roll_amount, 'rollCheck' => $rollChecked, 'rollUncheck' => $rollUnChecked));
                    }
                }

                foreach ($betByRoll as $getBet){
                    if(count($getBet['rollCheck'])){
                        foreach ($reverseNumber as $betNumber){
                            $getMatched = $this->matchWinNumberFromResults($date, $bet->bet_schedule_id, $betNumber, $getBet['rollCheck']);
                            if(count($getMatched)){
                                $totalAmount = $getBet['amount'] * $bet->pkg_price;
                                foreach ($getMatched as $val){
                                    $getBetWinningNumber[] = [
                                        'bet_id' => $bet->bet_id,
                                        'win_number' => $val->bet_number,
                                        'result_id' => $val->result_id,
                                        'prize_amount' => $totalAmount
                                    ];
                                }
                            }
                        }
                    }

                    if(count($getBet['rollUncheck'])){
                        $getMatched = $this->matchWinNumberFromResults($date, $bet->bet_schedule_id, $bet->generated_number, $getBet['rollUncheck']);
                        if(count($getMatched)){
                            $totalAmount = $getBet['amount'] * $bet->pkg_price;
                            foreach ($getMatched as $val){
                                $getBetWinningNumber[] = [
                                    'bet_id' => $bet->bet_id,
                                    'win_number' => $val->bet_number,
                                    'result_id' => $val->result_id,
                                    'prize_amount' => $totalAmount
                                ];
                            }
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
//        $date = '2025-02-23';
//        $day = 'Sunday';
//        $time = '16:30:00';
        $getBetWinningNumber = [];
        $groupRP = [];
        $originalNumber = '';
        $duplicateRPNumber = [];
        $originalNumberArr = [];
        $notInResult = [];
        DB::table('bets')
            ->select(
                'bet_numbers.*',
                'pkg_con.price as pkg_price',
                'pkg_con.bet_type as bet_type',
                'pkg_con.has_special as has_special',
                'bets.bet_schedule_id as bet_schedule_id',
                'bets.number_format as original_number'
            )
            ->join('bet_numbers','bet_numbers.bet_id','=', 'bets.id')
            ->join('bet_package_configurations as pkg_con','pkg_con.id','=', 'bets.bet_package_config_id')
            ->whereIn('bets.bet_schedule_id', $idSchedules)
            ->whereIn('pkg_con.bet_type', ['RP2','RP3'])
//            ->where('bets.id', 41)
//            ->where('has_special', '0')
            ->orderBy('bets.id')
            ->orderBy('bet_numbers.id')
            ->lazy()
            ->each(function ($bet) use (&$getBetWinningNumber, $date, &$groupRP, &$originalNumber, &$duplicateRPNumber, &$notInResult, &$originalNumberArr) {

                    if(count($originalNumberArr) == 0){
                        $originalNumberArr = explode("#", $bet->original_number);
                        $countDuplicate = array_count_values($originalNumberArr);
                        foreach ($countDuplicate as $number => $count) {
                            if ($count > 1) {
                                $duplicateRPNumber[] = $number;
                            }
                        }
                    }
                    $getMatched = [];
                    if(count($duplicateRPNumber)){
                        if(in_array($bet->generated_number, $duplicateRPNumber)){
                            $resultId = $this->matchWinNumberFromResult($date, $bet->bet_schedule_id, $bet->generated_number, $notInResult);
                            if($resultId){
                                $getMatched[] = (object)[
                                    'result_id' => $resultId,
                                    'bet_number' => $bet->generated_number
                                ];
                                $notInResult[] = $resultId;
                            }
                        }else{
                            $getMatched = $this->matchWinNumberFromResults($date, $bet->bet_schedule_id, $bet->generated_number, $this->rollParlay);
                        }
                    }else{
                        $getMatched = $this->matchWinNumberFromResults($date, $bet->bet_schedule_id, $bet->generated_number, $this->rollParlay);
                    }
                    if(!$originalNumber){
                        $originalNumber = $bet->original_number;
                    }
                    $groupRP[] = [
                        'results' => $getMatched,
                        'roll_parlay_amount' => $bet->roll_parlay_amount,
                        'pkg_price' => $bet->pkg_price,
                        'bet_id' => $bet->bet_id,
                        'generated_number' => $bet->generated_number
                    ];
                    if($bet->roll_parlay_check){
                        if(count($groupRP) && count($originalNumberArr) == count($groupRP)){
                            $getMinLength = null;
                            $RPExistResult = [];
                            foreach ($groupRP as $RP){
                                if(count($RP['results'])){
                                    $RPExistResult[] = $RP;
                                }
                            }
                            $multiplier = 0;
                            // example.
                            // - if bet number 23#34 matched result, so $multiplier = 1,
                            // - if 23#34#34, so $multiplier = 3;
                            // - if 23#34#34#45, so $multiplier = 6;
                            switch (count($RPExistResult)) {
                                case 2:
                                    $multiplier = 1;
                                    break;
                                case 3:
                                    $multiplier = 3;
                                    break;
                                case 4:
                                    $multiplier = 6;
                                    break;
                            }
                            
                            if(count($RPExistResult)>1){
                                foreach ($RPExistResult as $RP){
                                    if($getMinLength === null){
                                        $getMinLength = count($RP['results']);
                                    }else{
                                        if($getMinLength > count($RP['results'])){
                                            $getMinLength = count($RP['results']);
                                        }
                                    }
                                }
                            }else{
                                $getMinLength = 0;
                            }

                            if($getMinLength){
                                foreach ($groupRP as $RP){
                                    $totalAmount = $RP['roll_parlay_amount'] * $multiplier * $RP['pkg_price'];
                                    foreach ($RP['results'] as $k => $val){
                                        if($k < $getMinLength){
                                            $getBetWinningNumber[] = [
                                                'bet_id' => $RP['bet_id'],
                                                'win_number' => $val->bet_number,
                                                'result_id' => $val->result_id,
                                                'prize_amount' => $totalAmount
                                            ];
                                        }
                                    }
                                }
                            }
                            $groupRP = [];
                            $originalNumberArr = [];
                            $duplicateRPNumber = [];
                            $notInResult = [];
                        }

                    }else{

                        if(count($groupRP) && count($originalNumberArr) == count($groupRP)){
                            $getMinLength = null;
                            foreach ($groupRP as $RP){
                                if($getMinLength === null){
                                    $getMinLength = count($RP['results']);
                                }else{
                                    if($getMinLength > count($RP['results'])){
                                        $getMinLength = count($RP['results']);
                                    }
                                }
                            }

                            if($getMinLength){
                                foreach ($groupRP as $RP){
                                    $totalAmount = $RP['roll_parlay_amount'] * $RP['pkg_price'];
                                    foreach ($RP['results'] as $k => $val){
                                        if($k < $getMinLength){
                                            $getBetWinningNumber[] = [
                                                'bet_id' => $RP['bet_id'],
                                                'win_number' => $val->bet_number,
                                                'result_id' => $val->result_id,
                                                'prize_amount' => $totalAmount
                                            ];
                                        }
                                    }
                                }
                            }
                            $groupRP = [];
                            $originalNumberArr = [];
                            $duplicateRPNumber = [];
                            $notInResult = [];
                        }
                    }
            });
//        dd($getBetWinningNumber);
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
//            dd($request->all());
            $date = date('Y-m-d');
            $companies = $this->companies;
//            $date = '2025-03-29';
            if ($request->has('date')) {
                $date = $request->get('date');
            }
            $number = $request->number ?? null;
            $company = -1;
            if ($request->has('company')) {
                $company = $request->get('company');
            }

            $data = [];
             DB::table('bet_winning_records as record')
                ->select(
                    'record.bet_id',
                    'record.win_number',
                    DB::raw('sum(record.prize_amount) as sum_prize_amount'),
                    'pkg_con.bet_type',
                    'pkg_con.rate as net',
                    'pkg_con.price as odds',
                    'bets.number_format as original_number',
                    'bets.bet_date',
                    DB::raw('sum(bets.total_amount) as total_turnover'),
                    'bets.total_amount as turnover',
                    'schedule.province',
                    'bet_receipts.receipt_no',
                    'bet_numbers.a_amount',
                    'bet_numbers.b_amount',
                    'bet_numbers.ab_amount',
                    'bet_numbers.roll_amount',
                    'bet_numbers.roll7_amount',
                    'bet_numbers.roll_parlay_amount',
                    'users.name as account'
                )
                 ->join('bets','record.bet_id','=', 'bets.id')
                 ->join('users','users.id','=', 'bets.user_id')
                 ->join('bet_numbers','bet_numbers.bet_id','=', 'bets.id')
                 ->join('bet_receipts','bet_receipts.id','=', 'bets.bet_receipt_id')
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
                 })
                 ->when($number, function ($q) use ($number){
                     $q->where('bets.number_format', 'like','%'.$number.'%');
                 })
                ->orderBy('record.bet_id')
                ->groupBy('record.bet_id')
                 ->groupBy('record.win_number')
                 ->groupBy('bet_type')
                 ->groupBy('pkg_con.rate')
                 ->groupBy('pkg_con.price')
                 ->groupBy('bets.number_format')
                 ->groupBy('bets.bet_date')
                 ->groupBy('bets.total_amount')
                 ->groupBy('schedule.province')
                 ->groupBy('bet_receipts.receipt_no')
                 ->groupBy('bet_numbers.a_amount')
                 ->groupBy('bet_numbers.b_amount')
                 ->groupBy('bet_numbers.ab_amount')
                 ->groupBy('bet_numbers.roll_amount')
                 ->groupBy('bet_numbers.roll7_amount')
                 ->groupBy('bet_numbers.roll_parlay_amount')
                 ->groupBy('users.name')
                ->each(function ($record) use (&$data) {
                    $getBetRoll = '';
                    $amount = 0;
                    if($record->a_amount){
                        $amount = $amount+$record->a_amount;
                        $getBetRoll = $getBetRoll.'Head';
                    }
                    if($record->b_amount){
                        $amount = $amount+$record->b_amount;
                        if($getBetRoll){
                            $getBetRoll = $getBetRoll.',&nbsp;';
                        }
                        $getBetRoll = $getBetRoll.'Last';
                    }
                    if($record->ab_amount){
                        $amount = $amount+$record->ab_amount;
                        if($getBetRoll){
                            $getBetRoll = $getBetRoll.',&nbsp;';
                        }
                        $getBetRoll = $getBetRoll.'Head+Last';
                    }
                    if($record->roll_amount){
                        $amount = $amount+$record->roll_amount;
                        if($getBetRoll){
                            $getBetRoll = $getBetRoll.',&nbsp;';
                        }
                        $getBetRoll = $getBetRoll.'Roll';
                    }
                    if($record->roll7_amount){
                        $amount = $amount+$record->roll7_amount;
                        if($getBetRoll){
                            $getBetRoll = $getBetRoll.',&nbsp;';
                        }
                        $getBetRoll = $getBetRoll.'Roll7';
                    }
                    if($record->roll_parlay_amount){
                        $amount = $amount+$record->roll_parlay_amount;
                        if($getBetRoll){
                            $getBetRoll = $getBetRoll.',&nbsp;';
                        }
                        $getBetRoll = $getBetRoll.'Roll Parlay';
                    }

                    $prepareData = [
                        'bet_id' => $record->bet_id,
                        'account' => $record->account,
                        'amount' => $amount,
                        'net' => $record->net,
                        'odds' => $record->odds,
                        'bet_type' => $record->bet_type,
                        'original_number' => $record->original_number,
                        'company' => $record->province,
                        'game' => $getBetRoll,
                        'receipt_no' => $record->receipt_no,
                        'bet_date' => $record->bet_date,
                    ];

                    if(in_array($record->bet_type, ['RP2','RP3'])){
                        $existBet = array_filter($data, function ($val) use ($record) {
                            return $val['bet_id'] === $record->bet_id;
                        });
                        if(!count($existBet)){
                            $commission = $record->turnover - ($record->turnover * $record->net / 100);
                            $netAmount = $record->turnover * $record->net / 100;
                            $prepareData['compensate'] = BetWinningRecord::query()->where('bet_id', $record->bet_id)->first()->prize_amount??0;
                            $prepareData['win_number'] = $record->original_number;
                            $prepareData['turnover'] = $record->turnover;
                            $prepareData['commission'] = $commission;
                            $prepareData['net_amount'] = $netAmount;
                            $data[] = $prepareData;
                        }
                    }else{
                        $commission = $record->total_turnover - ($record->total_turnover * $record->net / 100);
                        $netAmount = $record->total_turnover * $record->net / 100;
                        $prepareData['turnover'] = $record->total_turnover;
                        $prepareData['win_number'] = $record->win_number;
                        $prepareData['compensate'] = $record->sum_prize_amount;
                        $prepareData['commission'] = $commission;
                        $prepareData['net_amount'] = $netAmount;
                        $data[] = $prepareData;
                    }
                });

//             return $data;

            return view('bet.report-winning', compact('data','companies', 'date', 'number', 'company'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

}
