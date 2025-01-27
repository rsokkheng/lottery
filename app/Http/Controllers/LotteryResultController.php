<?php

namespace App\Http\Controllers;

use App\Enums\HelperEnum;
use App\Models\LotteryResult;
use App\Models\LotterySchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class LotteryResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public $currentDate;
    public $currentDayName;
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

    public function getCurrentScheduleResult(): array
    {
        return LotterySchedule::query()->where(function ($query){
            $query->where('draw_day', $this->currentDayName)
                ->where('record_status_id',1)
                ->where('region_slug', HelperEnum::MienNamSlug);
        })->get()->toArray();
    }

    public function getPrizeLevel(): array
    {
        return [
            "GiaiTam" => ["name"=>"Giải tám", "order_count"=>1],
            "GiaiBay" => ["name"=>"Giải bảy", "order_count"=>1],
            "GiaiSau" => ["name"=>"Giải sáu", "order_count"=>3],
            "GiaiNam" => ["name"=>"Giải năm", "order_count"=>1],
            "GiaiTu" => ["name"=>"Giải tư", "order_count"=>7],
            "GiaiBa" => ["name"=>"Giải ba", "order_count"=>2],
            "GiaiNhi" => ["name"=>"Giải nhì", "order_count"=>1],
            "GiaiNhat" => ["name"=>"Giải nhất", "order_count"=>1],
            "GiaiDB" => ["name"=>"Giải Đặc Biệt", "order_count"=>1]
        ];
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
                ->when($prize!=null,function($q)use($prize){
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
        $prizes = $this->getPrizeLevel();
//        $region =  HelperEnum::MienNamSlug->value;
        $dateFormatted = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
//        $dateFormatted = '2025-01-16';
        $day = Carbon::parse($dateFormatted)->dayName;
        $schedule = $this->getCurrentScheduleResultFilter($day, $region);
        $result = [];
        foreach ($prizes as $key=>$prize) {
            foreach ($schedule as $item) {
                $result[$key]['label'] = $prize['name'];
                for($i=0; $i<$prize['order_count']; $i++){
//                    $result[$key][$item['code']]['label'] = $item['province'];
                    $result[$key][$item['code']][$i]['prize_level'] = $key;
                    $result[$key][$item['code']][$i]['draw_date'] = $dateFormatted;
                    $result[$key][$item['code']][$i]['result_order'] = $i+1;
                    $result[$key][$item['code']][$i]['draw_date'] = $dateFormatted;
                    $result[$key][$item['code']][$i]['province_code'] = $item['code'];
                    $getResult = $this->getLotteryResultFilter($dateFormatted, $key, null, $item['id']);
                    if(!empty($getResult)){
                        foreach ($getResult as $val){
                            if($val['result_order'] === $result[$key][$item['code']][$i]['result_order']){
                                $result[$key][$item['code']][$i]['result_id'] = $val['result_id'];
                                $result[$key][$item['code']][$i]['winning_number'] = $val['winning_number'];
                            }
                        }
                    }
                }
            }
        }
//        dd($result);
        return $result;
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

    public function createMienNam()
    {
        $formResult = $this->getMergeResult($this->currentDate, HelperEnum::MienNamSlug->value);
//        dd($formResult);
        $result = LotteryResult::query()->get()->toArray();
        $data = [
            'type' => HelperEnum::MienNamSlug->value,
            'url' => [
                'create' => 'admin.result.create-mien-nam',
                'index' => 'admin.result.index-mien-nam'
            ],
            'lottery_schedule' => $this->getCurrentScheduleResultFilter($this->currentDayName, HelperEnum::MienNamSlug->value),
            'current_date'=> $this->currentDate,
            'form_result' => $formResult
        ];

        return view('admin.lottery-result.create', compact('data','result'));
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
    public function createMienTrung()
    {
        $getData = [
            'Giai tam',
            'Giai bay',
            'Giai sau',
            'Giai nam',
            'Giai tu',
            'Giai ba',
            'Giai nhi',
            'Giai nhat',
            'Giai Dac Biet'
        ];
        $data = [
            'type' => HelperEnum::MienTrungSlug->value,
            'url' => [
                'create' => 'admin.result.create-mien-trung',
                'index' => 'admin.result.index-mien-trung'
            ],
            'data' => $getData
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
    public function createMienBac()
    {
        $getData = [
            'Giai tam',
            'Giai bay',
            'Giai sau',
            'Giai nam',
            'Giai tu',
            'Giai ba',
            'Giai nhi',
            'Giai nhat',
            'Giai Dac Biet'
        ];
        $data = [
            'type' => HelperEnum::MienBacDienToanSlug->value,
            'url' => [
                'create' => 'admin.result.create-mien-bac',
                'index' => 'admin.result.index-mien-bac'
            ],
            'data' => $getData
        ];
        return view('admin.lottery-result.create', compact('data'));
    }
}
