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
                $betResult->newQuery()
                    ->where('draw_date', $resultDate)
                    ->whereIn('lottery_schedule_id', $scheduleIdsByCurrentBet)
                    ->forceDelete();
                foreach ($form['data'] as $item) {
                    $betResult->newQuery()->create([
                        'draw_date' => $resultDate,
                        'province_code' => $item['province_code'],
                        'prize_level' => $item['prize_level'],
                        'winning_number' => $item['winning_number'],
                        'result_order' => $item['result_order'],
                        'lottery_schedule_id' => $item['schedule_id'],
                    ]);
                }
                $insertBetWinRecords = $this->generateWinningNumbers($resultDate, $scheduleIdsByCurrentBet);
                BetWinningRecord::insert($insertBetWinRecords);
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
//       $today = Carbon::today()->format('Y-m-d');
//        $dayName = Carbon::today()->dayName;
//        $resultTime = $this->getBetTime(HelperEnum::MienNamSlug->value);
        $date = Carbon::today()->format('Y-m-d');
        $scheduleIds = [20,21,22];
//        $idSchedules = $this->getPluckIdSchedule($dayName, $resultTime);
//         $this->generateWinningNumbersCustom($date, $scheduleIds);
//        $this->insertWinningRecords($date, $scheduleIds, $getBetWin);
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
            })->get();
        BetWinningRecord::insert($insertRecords);
    }


    public function generateWinningNumbers($date, $idSchedules): array
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
                 'bets.id as bet_id',
                 'bet_numbers.generated_number',
                 'bet_numbers.digit_length',
                 DB::raw('bet_numbers.a_amount + bet_numbers.b_amount + bet_numbers.ab_amount + bet_numbers.roll_amount + bet_numbers.roll7_amount + bet_numbers.roll_parlay_amount as sum_bet_amount'),
                'pkg_con.price as pkg_price',
                 'pkg_con.bet_type as bet_type',
                 'bets.bet_schedule_id',
                 'bets.number_format as original_number'
             )
             ->join('bet_numbers','bet_numbers.bet_id','=', 'bets.id')
             ->join('bet_package_configurations as pkg_con','pkg_con.id','=', 'bets.bet_package_config_id')
             ->whereIn('bets.bet_schedule_id',$idSchedules)
            ->orderBy('bets.id')
            ->orderBy('bet_numbers.id')
            ->lazy()
            ->each(function ($bet) use (&$getBetWinningNumber, $date, &$groupRP, &$originalNumber, &$duplicateRPNumber, &$notInResult, &$originalNumberArr) {
                if(str_starts_with($bet->bet_type, 'RP')){
                    $getMatched = [];
                    if(count($originalNumberArr) == 0){
                        $originalNumberArr = explode("#", $bet->original_number);
                        $countDuplicate = array_count_values($originalNumberArr);
                        foreach ($countDuplicate as $number => $count) {
                            if ($count > 1) {
                                $duplicateRPNumber[] = $number;
                            }
                        }
//                        dump($duplicateRPNumber);
                    }

                   if(count($duplicateRPNumber)){
                       if(in_array($bet->generated_number, $duplicateRPNumber)){
                           $resultId = $this->matchWinNumberFromResult($date, $bet->bet_schedule_id, $bet->generated_number, $notInResult);
                           if($resultId){
                               $getMatched[] = $resultId;
                               $notInResult[] = $resultId;
                           }
                       }else{
                           $getMatched = $this->matchWinNumberFromResults($date, $bet->bet_schedule_id, $bet->generated_number);
                       }
                   }else{
                       $getMatched = $this->matchWinNumberFromResults($date, $bet->bet_schedule_id, $bet->generated_number);
                   }

                   if(!$originalNumber){
                       $originalNumber = $bet->original_number;
                   }
                   $groupRP[] = [
                       'results' => $getMatched,
                       'sum_bet_amount' => $bet->sum_bet_amount,
                       'pkg_price' => $bet->pkg_price,
                       'bet_id' => $bet->bet_id,
                       'generated_number' => $bet->generated_number
                   ];

//                   if(count($duplicates)){
//                       if(count($groupRP)>1){
//                           dd($groupRP);
//                       }
//                   }
               }else{
                   $getMatched = $this->matchWinNumberFromResults($date, $bet->bet_schedule_id, $bet->generated_number);
                   if(count($getMatched)){
                       $totalAmount = $bet->sum_bet_amount * $bet->pkg_price;
                       foreach ($getMatched as $val){
                           $getBetWinningNumber[] = [
                               'bet_id' => $bet->bet_id,
                               'result_id' => $val,
                               'prize_amount' => $totalAmount
                           ];
                       }
                   }
               }

//                if(count($groupRP) && count($originalNumberArr) == count($groupRP)){
//                    dump($groupRP);
//                    $groupRP = [];
//                    $originalNumberArr = [];
//                    $duplicateRPNumber = [];
//                    $notInResult = [];
//                }

               if(count($groupRP) && count($originalNumberArr) == count($groupRP)){
                   $getMinLength = 0;
                   foreach ($groupRP as $RP){
                       if(!$getMinLength){
                           $getMinLength = count($RP['results']);
                       }else{
                           if($getMinLength > count($RP['results'])){
                               $getMinLength = count($RP['results']);
                           }
                       }
                   }
                   if($getMinLength){
                       foreach ($groupRP as $RP){
                           $totalAmount = $RP['sum_bet_amount'] * $RP['pkg_price'];
                           foreach ($RP['results'] as $k => $val){
                               if($k < $getMinLength){
                                   $getBetWinningNumber[] = [
                                       'bet_id' => $RP['bet_id'],
                                       'result_id' => $val,
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

            });
        return $getBetWinningNumber;
    }


//    public function generateWinningNumbersCustom($date, $idSchedules): array
//    {
//        $date = '2025-02-23';
//        $idSchedules = [20];
////        $day = 'Sunday';
////        $time = '16:30:00';
//        $getBetWinningNumber = [];
////        $groupRP = [];
////        $originalNumber = '';
////        $duplicateRPNumber = [];
////        $originalNumberArr = [];
////        $notInResult = [];
//
//        DB::table('bets')
//            ->select(
//                'bets.id as bet_id',
//                'bet_numbers.*',
//                DB::raw('bet_numbers.a_amount + bet_numbers.b_amount + bet_numbers.ab_amount + bet_numbers.roll_amount + bet_numbers.roll7_amount + bet_numbers.roll_parlay_amount as sum_bet_amount'),
//                'pkg_con.price as pkg_price',
//                'pkg_con.bet_type as bet_type',
//                'bets.bet_schedule_id',
//                'bets.number_format as original_number'
//            )
//            ->join('bet_numbers','bet_numbers.bet_id','=', 'bets.id')
//            ->join('bet_package_configurations as pkg_con','pkg_con.id','=', 'bets.bet_package_config_id')
//            ->whereIn('bets.bet_schedule_id',$idSchedules)
//            ->orderBy('bets.id')
//            ->orderBy('bet_numbers.id')
//            ->lazy()
//            ->each(function ($bet) {
//                $total_amount = 0;
//                if($bet->a_check){
//                    $total_amount += $bet->a_amount;
//                }
//                if($bet->b_check){
//                    $total_amount += $bet->b_amount;
//                }
//                if($bet->ab_check){
//                    $total_amount += $bet->ab_amount;
//                }
//            });
//        return $getBetWinningNumber;
//    }


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

    public function matchWinNumberFromResults($date, $scheduleId, $number): array
    {
        return DB::table('bet_lottery_results')
            ->where('draw_date', $date)
            ->where('lottery_schedule_id',$scheduleId)
            ->where('winning_number', 'like', '%'.$number)
            ->orderBy('result_id')
            ->pluck('result_id')
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

}
