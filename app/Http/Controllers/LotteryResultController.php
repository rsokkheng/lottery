<?php

namespace App\Http\Controllers;

use App\Enums\HelperEnum;
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
                "GiaiDB" => ["name"=>"Giải Đặc Biệt", "order_count"=>1, "input_length" => 5, "col_count"=>1, "row_count"=>1],
                "GiaiNhat" => ["name"=>"Giải nhất", "order_count"=>1, "input_length" => 5, "col_count"=>1, "row_count"=>1],
                "GiaiNhi" => ["name"=>"Giải nhì", "order_count"=>1, "input_length" => 5, "col_count"=>2, "row_count"=>1],
                "GiaiBa" => ["name"=>"Giải ba", "order_count"=>2, "input_length" => 5, "col_count"=>3, "row_count"=>2],
                "GiaiTu" => ["name"=>"Giải tư", "order_count"=>4, "input_length" => 4, "col_count"=>2, "row_count"=>2],
                "GiaiNam" => ["name"=>"Giải năm", "order_count"=>6, "input_length" => 4, "col_count"=>3, "row_count"=>2],
                "GiaiSau" => ["name"=>"Giải sáu", "order_count"=>3, "input_length" => 3, "col_count"=>1, "row_count"=>1],
                "GiaiBay" => ["name"=>"Giải bảy", "order_count"=>4, "input_length" => 2, "col_count"=>1, "row_count"=>1],
            ];
        }else{
            return [
                "GiaiTam" => ["name"=>"Giải tám", "order_count"=>1, "input_length" => 2],
                "GiaiBay" => ["name"=>"Giải bảy", "order_count"=>1, "input_length" => 3],
                "GiaiSau" => ["name"=>"Giải sáu", "order_count"=>3, "input_length" => 4],
                "GiaiNam" => ["name"=>"Giải năm", "order_count"=>1, "input_length" => 4],
                "GiaiTu" => ["name"=>"Giải tư", "order_count"=>7, "input_length" => 5],
                "GiaiBa" => ["name"=>"Giải ba", "order_count"=>2, "input_length" => 5],
                "GiaiNhi" => ["name"=>"Giải nhì", "order_count"=>1, "input_length" => 5],
                "GiaiNhat" => ["name"=>"Giải nhất", "order_count"=>1, "input_length" => 5],
                "GiaiDB" => ["name"=>"Giải Đặc Biệt", "order_count"=>1, "input_length" => 6]
            ];
        }
    }

    public function getCurrentScheduleResultFilter($day, $regionSlug, $provinceCode = null): array
    {
//        dd($day);
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
                    $result[$key]['provinces'][$item['code']]['row_result'][$i]['draw_date'] = $dateFormatted;
                    if($region === HelperEnum::MienBacDienToanSlug->value){
                        $result[$key]['provinces'][$item['code']]['row_result'][$i]['col_count'] = $prize['col_count'];
                        $result[$key]['provinces'][$item['code']]['row_result'][$i]['row_count'] = $prize['row_count'];
                    }
                    $getResult = $this->getLotteryResultFilter($dateFormatted, $key, null, $item['id']);
                    if(count($getResult)>0){
                        foreach ($getResult as $val){
                            if($val['result_order'] === $i+1){
                                $result[$key]['provinces'][$item['code']]['row_result'][$i]['result_id'] = $val['result_id'];
                                $result[$key]['provinces'][$item['code']]['row_result'][$i]['winning_number'] = $val['winning_number'];
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
            Log::error($exception->getMessage());
            return $exception->getMessage();
        }
    }

    public function storeWinningResult(Request $request)
    {
        try {
            DB::beginTransaction();
            $form = $request->all();
            $betResult = new LotteryResult();
            $betResultSchedule = new LotterySchedule();
            if (isset($form['data']) && count($form['data'])) {
                $resultRegion = $form['result_region']??'';
                $idSchedules = $betResultSchedule->newQuery()
                    ->where('region_slug', $resultRegion)
                    ->pluck('id')
                    ->toArray();
                $resultDate = Carbon::createFromFormat('d/m/Y', $form['data'][0]['result_date'])->format('Y-m-d');
                $betResult->newQuery()
                    ->where('draw_date', $resultDate)
                    ->whereIn('lottery_schedule_id', $idSchedules)
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
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'save success'
            ]);
        }catch (\Exception $e){
            DB::rollBack();
//            dd($e->getMessage());
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
        $filterDate = $request['date']??$this->currentDate;
        $formResult = $this->getMergeResult($filterDate, HelperEnum::MienTrungSlug->value);
        $data = [
            'type' => HelperEnum::MienTrungSlug->value,
            'url' => [
                'create' => 'admin.result.create-mien-trung',
                'index' => 'admin.result.index-mien-trung'
            ],
            'current_date'=> $filterDate,
            'form_result' => $formResult
        ];
        return view('admin.lottery-result.create', compact('data'));
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
        $filterDate = $request['date']??$this->currentDate;
        $formResult = $this->getMergeResult($filterDate, HelperEnum::MienBacDienToanSlug->value);
        $data = [
            'type' => HelperEnum::MienBacDienToanSlug->value,
            'url' => [
                'create' => 'admin.result.create-mien-bac',
                'index' => 'admin.result.index-mien-bac'
            ],
            'current_date'=> $filterDate,
            'form_result' => $formResult
        ];
        return view('admin.lottery-result.create', compact('data'));
    }

    public function getBetResultBy($date, $region)
    {
//        dd($date, $region);
    }

}
