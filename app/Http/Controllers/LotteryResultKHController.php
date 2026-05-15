<?php

namespace App\Http\Controllers;

use App\Models\BetReceiptKH;
use App\Models\BetWinningRecordKH;
use App\Models\BetWinningKH;
use App\Models\AccountKH;
use App\Models\CreditTransactionKH;
use Carbon\Carbon;
use App\Models\User;
use App\Enums\HelperEnum;
use Illuminate\Http\Request;
use App\Models\LotteryResult;
use App\Models\LotterySchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use function PHPUnit\Framework\throwException;

class LotteryResultKHController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public string $currentDate;
    public string $currentDayName;

    // MienNam / MienTrung — A channel (row 1/A)
    public array $rollA    = ['1/A'];       // 2D head
    public array $rollA3D  = ['GiaiBay'];   // 3D head

    // MienNam / MienTrung — individual B/C/D channels
    public array $rollB    = ['KH_B_2D'];
    public array $rollB3D  = ['KH_B_3D'];
    public array $rollC    = ['KH_C_2D'];
    public array $rollC3D  = ['KH_C_3D'];
    public array $rollD    = ['KH_D_2D'];
    public array $rollD3D  = ['KH_D_3D'];

    // MienBac (Hanoi) — A channel (row 1/A)
    public array $HanoiRollA   = ['GiaiBay'];   // 4 × 2D
    public array $HanoiRollA3D = ['GiaiSau'];   // 3 × 3D

    // MienBac (Hanoi) — individual B/C/D channels
    public array $HanoiRollB   = ['KH_B_2D'];
    public array $HanoiRollB3D = ['KH_B_3D'];
    public array $HanoiRollC   = ['KH_C_2D'];
    public array $HanoiRollC3D = ['KH_C_3D'];
    public array $HanoiRollD   = ['KH_D_2D'];
    public array $HanoiRollD3D = ['KH_D_3D'];

    // All KHR prize levels (used by Roll and Roll Parlay)
    public array $rolls = [
        '1/A', 'GiaiBay',
        'GiaiSau', 'GiaiNam', 'GiaiTu', 'GiaiBa', 'GiaiNhi', 'GiaiNhat',
        'KH_B_2D', 'KH_B_3D', 'KH_C_2D', 'KH_C_3D', 'KH_D_2D', 'KH_D_3D',
    ];

    // Roll2: row 1/A (2D+3D) + rows 2-3 + row 4 first-result-only (handled separately)
    public array $roll7 = ['1/A', 'GiaiBay', 'GiaiSau', 'GiaiNam'];

    // Roll Parlay checks all prize levels
    public array $rollParlay = [
        '1/A', 'GiaiBay',
        'GiaiSau', 'GiaiNam', 'GiaiTu', 'GiaiBa', 'GiaiNhi', 'GiaiNhat',
        'KH_B_2D', 'KH_B_3D', 'KH_C_2D', 'KH_C_3D', 'KH_D_2D', 'KH_D_3D',
    ];
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
        return view('admin.lottery-kh-result.index',compact('data'));
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
                "GiaiBay"  => ["name"=>"1/A 2D", "order_count"=>4, "input_length"=>2, "col_count"=>4, "row_count"=>1, "tailwind_class"=>'text-red-600 font-bold text-2xl'],
                "GiaiSau"  => ["name"=>"1/A 3D", "order_count"=>3, "input_length"=>3, "col_count"=>3, "row_count"=>1, "tailwind_class"=>'text-red-600 font-bold text-2xl'],
                "GiaiNam"  => ["name"=>"2",       "order_count"=>6, "input_length"=>4, "col_count"=>3, "row_count"=>2, "tailwind_class"=>'text-gray-800 font-semibold'],
                "GiaiTu"   => ["name"=>"3",       "order_count"=>4, "input_length"=>4, "col_count"=>2, "row_count"=>2, "tailwind_class"=>'text-gray-800 font-semibold'],
                "GiaiBa"   => ["name"=>"4",       "order_count"=>6, "input_length"=>5, "col_count"=>3, "row_count"=>2, "tailwind_class"=>'text-gray-800 font-semibold'],
                "GiaiNhi"  => ["name"=>"5",       "order_count"=>2, "input_length"=>5, "col_count"=>2, "row_count"=>1, "tailwind_class"=>'text-gray-800 font-semibold'],
                "GiaiNhat" => ["name"=>"6",       "order_count"=>1, "input_length"=>5, "col_count"=>1, "row_count"=>1, "tailwind_class"=>'text-gray-800 font-semibold'],
                "KH_B_2D"  => ["name"=>"7/B 2D", "order_count"=>1, "input_length"=>2, "col_count"=>1, "row_count"=>1, "tailwind_class"=>'text-blue-700 font-bold'],
                "KH_B_3D"  => ["name"=>"7/B 3D", "order_count"=>1, "input_length"=>3, "col_count"=>1, "row_count"=>1, "tailwind_class"=>'text-blue-700 font-bold'],
                "KH_C_2D"  => ["name"=>"8/C 2D", "order_count"=>1, "input_length"=>2, "col_count"=>1, "row_count"=>1, "tailwind_class"=>'text-blue-700 font-bold'],
                "KH_C_3D"  => ["name"=>"8/C 3D", "order_count"=>1, "input_length"=>3, "col_count"=>1, "row_count"=>1, "tailwind_class"=>'text-blue-700 font-bold'],
                "KH_D_2D"  => ["name"=>"9/D 2D", "order_count"=>1, "input_length"=>2, "col_count"=>1, "row_count"=>1, "tailwind_class"=>'text-blue-700 font-bold'],
                "KH_D_3D"  => ["name"=>"9/D 3D", "order_count"=>1, "input_length"=>3, "col_count"=>1, "row_count"=>1, "tailwind_class"=>'text-blue-700 font-bold'],
            ];
        }else{
            return [
                "1/A"     => ["name"=>"1/A",      "order_count"=>1, "input_length"=>2, "tailwind_class"=>"text-red-600 font-bold"],
                "GiaiBay" => ["name"=>"1/A 3D",   "order_count"=>1, "input_length"=>3, "tailwind_class"=>"text-red-600 font-bold"],
                "GiaiSau" => ["name"=>"2",         "order_count"=>3, "input_length"=>4, "tailwind_class"=>"text-gray-800 font-semibold"],
                "GiaiNam" => ["name"=>"3",         "order_count"=>1, "input_length"=>4, "tailwind_class"=>"text-gray-800 font-semibold"],
                "GiaiTu"  => ["name"=>"4",         "order_count"=>7, "input_length"=>5, "tailwind_class"=>"text-gray-800 font-semibold"],
                "GiaiBa"  => ["name"=>"5",         "order_count"=>2, "input_length"=>5, "tailwind_class"=>"text-gray-800 font-semibold"],
                "GiaiNhi" => ["name"=>"6",         "order_count"=>1, "input_length"=>5, "tailwind_class"=>"text-gray-800 font-semibold"],
                "GiaiNhat"=> ["name"=>"7",         "order_count"=>1, "input_length"=>5, "tailwind_class"=>"text-gray-800 font-semibold"],
                "KH_B_2D" => ["name"=>"8/B 2D",   "order_count"=>1, "input_length"=>2, "tailwind_class"=>"text-blue-700 font-bold"],
                "KH_B_3D" => ["name"=>"8/B 3D",   "order_count"=>1, "input_length"=>3, "tailwind_class"=>"text-blue-700 font-bold"],
                "KH_C_2D" => ["name"=>"9/C 2D",   "order_count"=>1, "input_length"=>2, "tailwind_class"=>"text-blue-700 font-bold"],
                "KH_C_3D" => ["name"=>"9/C 3D",   "order_count"=>1, "input_length"=>3, "tailwind_class"=>"text-blue-700 font-bold"],
                "KH_D_2D" => ["name"=>"10/D 2D",  "order_count"=>1, "input_length"=>2, "tailwind_class"=>"text-blue-700 font-bold"],
                "KH_D_3D" => ["name"=>"10/D 3D",  "order_count"=>1, "input_length"=>3, "tailwind_class"=>"text-blue-700 font-bold"],
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
                'create' => 'admin.result-kh.create-mien-nam',
                'index' => 'admin.result-kh.index-mien-nam'
            ],
            'data' => []
        ];
        return view('admin.lottery-kh-result.index',compact('data'));
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
                    'create' => 'admin.result-kh.create-mien-nam',
                    'index' => 'admin.result-kh.index-mien-nam'
                ],
                'current_date' => $filterDate,
                'form_result' => $formResult
            ];
            return view('admin.lottery-kh-result.create', compact('data'));
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
                $oldBetWinningRecords = BetWinningRecordKH::query()->whereHas('betLotteryResult', function ($query) use ($resultDate, $scheduleIdsByCurrentBet){
                        $query->where('draw_date', $resultDate)
                            ->whereIn('lottery_schedule_id', $scheduleIdsByCurrentBet);
                    });

                // Capture users with existing wins before deletion (for credit reversal)
                $existingWinnerIds = DB::table('bet_winning_kh')
                    ->join('bet_kh', 'bet_kh.id', '=', 'bet_winning_kh.bet_id')
                    ->whereDate('bet_winning_kh.created_at', $resultDate)
                    ->whereIn('bet_kh.bet_schedule_id', $scheduleIdsByCurrentBet)
                    ->pluck('bet_kh.user_id')
                    ->unique()->toArray();

                $oldBetWinningRecords->each(function ($record){
                    $record->betWinningKH()->forceDelete();
                });
                $oldBetWinningRecords->forceDelete();

                // Reverse existing win credits for those users on this date
                if (!empty($existingWinnerIds)) {
                    CreditTransactionKH::where('type', 'win_credit')
                        ->where('bet_date', $resultDate)
                        ->whereIn('user_id', $existingWinnerIds)
                        ->get()
                        ->each(function ($tx) {
                            $account = AccountKH::where('user_id', $tx->user_id)->first();
                            if ($account) {
                                $account->credit_balance = max(0, (float) $account->credit_balance - (float) $tx->amount);
                                $account->save();
                            }
                        });
                    CreditTransactionKH::where('type', 'win_credit')
                        ->where('bet_date', $resultDate)
                        ->whereIn('user_id', $existingWinnerIds)
                        ->delete();
                }
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
                $insertWinNumber = [...$getNormalWinNumber,...$getHashWinNumber];
                if(count($insertWinNumber)) {
                    $recordsCreated = $this->insertBetWinning($insertWinNumber, $resultDate);
                    if (count($recordsCreated)) {
                        // Update receipt compensate totals
                        DB::table('bet_winning_kh as winning')
                        ->select('winning.bet_receipt_id', DB::raw('SUM(winning.win_amount) as sum_amount'))
                        ->whereDate('winning.created_at', $resultDate)
                        ->whereIn('winning.bet_id', function ($sub) use ($scheduleIdsByCurrentBet) {
                            $sub->select('id')->from('bet_kh')->whereIn('bet_schedule_id', $scheduleIdsByCurrentBet);
                        })
                        ->orderBy('winning.bet_receipt_id')
                        ->groupBy('winning.bet_receipt_id')
                        ->each(function ($winning) {
                            BetReceiptKH::query()->find($winning->bet_receipt_id)->update(['compensate' => $winning->sum_amount]);
                        });

                        // Auto-credit winnings to each member's AccountKH
                        DB::table('bet_winning_kh as winning')
                        ->join('bet_kh', 'bet_kh.id', '=', 'winning.bet_id')
                        ->select('bet_kh.user_id', DB::raw('SUM(winning.win_amount) as sum_amount'))
                        ->whereDate('winning.created_at', $resultDate)
                        ->whereIn('bet_kh.bet_schedule_id', $scheduleIdsByCurrentBet)
                        ->groupBy('bet_kh.user_id')
                        ->orderBy('bet_kh.user_id')
                        ->each(function ($winning) use ($resultDate) {
                            $account = AccountKH::firstOrCreate(
                                ['user_id' => $winning->user_id],
                                ['credit_balance' => 0, 'created_by' => Auth::id()]
                            );
                            $before = (float) $account->credit_balance;
                            $account->credit_balance += $winning->sum_amount;
                            $account->save();
                            CreditTransactionKH::create([
                                'user_id'        => $winning->user_id,
                                'type'           => 'win_credit',
                                'amount'         => $winning->sum_amount,
                                'balance_before' => $before,
                                'balance_after'  => $account->credit_balance,
                                'note'           => 'Win credit ' . $resultDate,
                                'bet_date'       => $resultDate,
                                'created_by'     => Auth::id(),
                            ]);
                        });
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

    public function indexMienTrung()
    {
        $data = [
            'type' => HelperEnum::MienTrungSlug->value,
            'url' => [
                'create' => 'admin.result-kh.create-mien-trung',
                'index' => 'admin.result-kh.index-mien-trung'
            ],
            'data' => []
        ];
        return view('admin.lottery-kh-result.index',compact('data'));
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
                    'create' => 'admin.result-kh.create-mien-trung',
                    'index' => 'admin.result-kh.index-mien-trung'
                ],
                'current_date' => $filterDate,
                'form_result' => $formResult
            ];
            return view('admin.lottery-kh-result.create', compact('data'));
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
                'create' => 'admin.result-kh.create-mien-bac',
                'index' => 'admin.result-kh.index-mien-bac'
            ],
            'data' => []
        ];
        return view('admin.lottery-kh-result.index',compact('data'));
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
                    'create' => 'admin.result-kh.create-mien-bac',
                    'index' => 'admin.result-kh.index-mien-bac'
                ],
                'current_date' => $filterDate,
                'form_result' => $formResult
            ];
            return view('admin.lottery-kh-result.create', compact('data'));
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
            return view('bet_kh.result-show', compact('data'));
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


    public function insertBetWinning($data,$date){
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
                                $sumAmount = $item['prize_amount'];
                                $betAmount = $item;
                            }
                        }else{
                            if ($betAmount['bet_number_id'] !== $item['bet_number_id']) {
                                $sumAmount = $item['prize_amount'];
                                $betAmount = $item;
                            }else{
                                $sumAmount += $item['prize_amount'];
                                $betAmount = $item;
                            }
                        }
                    }
                }

                $matchThese = ['bet_id'=>$item['bet_id']??0,'bet_number_id' => $item['bet_number_id'],'bet_receipt_id'=>$item['receipt_id']];
                $betWin = BetWinningKH::query()->updateOrCreate($matchThese,[
                    'win_amount'=>$sumAmount,
                    'bet_number_id' => $item['bet_number_id'],
                    'created_at' => $date,
                ]);
                $save[] = BetWinningRecordKH::query()->insert([
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

//        $ddd = $this->matchWinNumberFromResultsRoll2('2025-03-29', 33, 154);
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

//        $data1 = $this->generateNormalWinBet($date, $scheduleIds);
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


    public function getBetRoll($a, $b, $c, $d, $ab, $roll7, $roll, $rollParlay, $betType)
    {
        $rollA = $betType === '3D' ? $this->rollA3D : $this->rollA;
        $rollB = $betType === '3D' ? $this->rollB3D : $this->rollB;
        $rollC = $betType === '3D' ? $this->rollC3D : $this->rollC;
        $rollD = $betType === '3D' ? $this->rollD3D : $this->rollD;

        $getRoll = [];
        if ((float)$a)         $getRoll = $rollA;
        if ((float)$b)         $getRoll = $rollB;
        if ((float)$c)         $getRoll = $rollC;
        if ((float)$d)         $getRoll = $rollD;
        if ((float)$ab)        $getRoll = [...$rollA, ...$rollB, ...$rollC, ...$rollD];
        if ((float)$roll7)     $getRoll = $this->roll7;
        if ((float)$roll)      $getRoll = $this->rolls;
        if ((float)$rollParlay)$getRoll = $this->rollParlay;
        return $getRoll;
    }

    public function getBetAmount($a, $b, $c, $d, $ab, $roll7, $roll, $rollParlay)
    {
        $getAmount = 0;
        if ((float)$a)         $getAmount = (float)$a;
        if ((float)$b)         $getAmount = (float)$b;
        if ((float)$c)         $getAmount = (float)$c;
        if ((float)$d)         $getAmount = (float)$d;
        if ((float)$ab)        $getAmount = (float)$ab;
        if ((float)$roll7)     $getAmount = (float)$roll7;
        if ((float)$roll)      $getAmount = (float)$roll;
        if ((float)$rollParlay)$getAmount = (float)$rollParlay;
        return $getAmount;
    }

    public function generateNormalWinBet($date, $idSchedules): array
    {
//        $date = '2025-02-23';
//        $day = 'Sunday';
//        $time = '16:30:00';
        $getBetWinningNumber = [];
         DB::table('bet_kh')
            ->select(
                'bet_number_kh.*',
                'pkg_con.price as pkg_price',
                'pkg_con.bet_type as bet_type',
                'bet_kh.bet_schedule_id',
                'bet_kh.number_format as original_number',
                'schedule.region_slug',
                'bet_kh.bet_receipt_id'
            )
            ->join('bet_number_kh','bet_number_kh.bet_id','=', 'bet_kh.id')
            ->join('bet_lottery_schedules as schedule','schedule.id','=','bet_kh.bet_schedule_id')
            ->join('bet_package_configurations as pkg_con','pkg_con.id','=', 'bet_kh.bet_package_config_id')
            ->whereIn('bet_kh.bet_schedule_id',$idSchedules)
            ->whereIn('pkg_con.bet_type', ['2D','3D','4D'])
            ->whereDate('bet_kh.bet_date', '=',$date)
//             ->where('bet_kh.id', 9)
            ->orderBy('bet_kh.id')
            ->orderBy('bet_number_kh.id')
            ->lazy()
            ->each(function ($bet) use (&$getBetWinningNumber, $date) {
                $getBetRoll = $this->getBetRoll($bet->a_amount, $bet->b_amount, $bet->c_amount, $bet->d_amount, $bet->abcd_amount, $bet->roll2_amount, $bet->roll_amount, $bet->roll_parlay_amount, $bet->bet_type);
                $getAmount = $this->getBetAmount($bet->a_amount, $bet->b_amount, $bet->c_amount, $bet->d_amount, $bet->abcd_amount, $bet->roll2_amount, $bet->roll_amount, $bet->roll_parlay_amount);
                if ($bet->region_slug === HelperEnum::MienBacDienToanSlug->value) {
                    $rollA = $bet->bet_type === '3D' ? $this->HanoiRollA3D : $this->HanoiRollA;
                    $rollB = $bet->bet_type === '3D' ? $this->HanoiRollB3D : $this->HanoiRollB;
                    $rollC = $bet->bet_type === '3D' ? $this->HanoiRollC3D : $this->HanoiRollC;
                    $rollD = $bet->bet_type === '3D' ? $this->HanoiRollD3D : $this->HanoiRollD;

                    if ((float)$bet->a_amount)  $getBetRoll = $rollA;
                    if ((float)$bet->b_amount)  $getBetRoll = $rollB;
                    if ((float)$bet->c_amount)  $getBetRoll = $rollC;
                    if ((float)$bet->d_amount)  $getBetRoll = $rollD;
                    if ((float)$bet->abcd_amount) $getBetRoll = [...$rollA, ...$rollB, ...$rollC, ...$rollD];
                    // Roll / Roll Parlay already set correctly by getBetRoll above; keep them
                }

                if((float)$bet->roll2_amount){
                    $getMatched = $this->matchWinNumberFromResultsRoll2($date, $bet->bet_schedule_id, $bet->generated_number);
                    if (count($getMatched)) {
                        $totalAmount = (float)$bet->roll2_amount * (float)$bet->pkg_price;
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
//        $date = '2025-02-23';
//        $day = 'Sunday';
//        $time = '16:30:00';
        $getBetWinningNumber = [];
        DB::table('bet_kh')
            ->select(
                'bet_number_kh.*',
                'pkg_con.price as pkg_price',
                'pkg_con.bet_type as bet_type',
                'pkg_con.has_special as has_special',
                'bet_kh.bet_schedule_id as bet_schedule_id',
                'bet_kh.number_format as original_number',
                'bet_kh.bet_receipt_id'
            )
            ->join('bet_number_kh','bet_number_kh.bet_id','=', 'bet_kh.id')
            ->join('bet_package_configurations as pkg_con','pkg_con.id','=', 'bet_kh.bet_package_config_id')
            ->whereIn('bet_kh.bet_schedule_id', $idSchedules)
            ->whereIn('pkg_con.bet_type', ['RP2','RP3','RP4'])
            ->whereDate('bet_kh.bet_date', '=',$date)
            ->orderBy('bet_kh.id')
            ->orderBy('bet_number_kh.id')
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
           ->select('result_id', DB::raw("'".$number."' as bet_number"))
            ->where('draw_date', $date)
            ->where('lottery_schedule_id', $scheduleId)
            ->whereIn('prize_level', $roll)
            ->where('winning_number', 'like', '%'.$number)
            ->orderBy('result_id')
            ->get()
            ->toArray();
    }

    public function matchWinNumberFromResultsRoll2($date, $scheduleId, $number): array
    {
        // KHR Roll2: row 1/A (2D + 3D), row 2, row 3, and row 4 first result only
        return DB::table('bet_lottery_results')
            ->select('result_id', DB::raw("'$number' as bet_number"))
            ->where(function ($q) use ($number, $date, $scheduleId) {
                $q->whereIn('prize_level', ['1/A', 'GiaiBay', 'GiaiSau', 'GiaiNam'])
                    ->where('lottery_schedule_id', $scheduleId)
                    ->where('draw_date', $date)
                    ->where('winning_number', 'like', '%'.$number);
            })
            ->orWhere(function ($q) use ($number, $date, $scheduleId) {
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
            DB::table('bet_winning_record_kh as record')
                ->select(
                    'bet_winning_kh.bet_id',
                    'record.bet_number_id',
                    'bet_winning_kh.win_amount as compensate',
                    'record.id as record_id',
                    'record.win_number',
                    DB::raw('(SELECT COUNT(*) FROM bet_winning_record_kh r2 WHERE r2.bet_winning_id = record.bet_winning_id) as total_wins_count'),
                    'record.bet_number_id',
                    'pkg_con.bet_type',
                    'pkg_con.rate as net',
                    'pkg_con.price as odds',
                    'bet_kh.bet_date',
                    'bet_number_kh.generated_number as generated_number',
                    'bet_number_kh.total_amount as turnover',
                    'schedule.province_en',
                    'schedule.code',
                    'bet_receipt_kh.receipt_no',
                    'bet_receipt_kh.receipt_no',
                    'bet_number_kh.a_amount',
                    'bet_number_kh.b_amount',
                    'bet_number_kh.c_amount',
                    'bet_number_kh.d_amount',
                    'bet_number_kh.abcd_amount',
                    'bet_number_kh.roll_amount',
                    'bet_number_kh.roll2_amount',
                    'bet_number_kh.roll_parlay_amount',
                    DB::raw('sum(bet_number_kh.a_check+bet_number_kh.b_check+bet_number_kh.c_check+bet_number_kh.d_check+bet_number_kh.abcd_check+bet_number_kh.roll_check+bet_number_kh.roll2_check+bet_number_kh.roll_parlay_check) as sum_check'),
                    'users.name as account'
                )
                ->join('bet_number_kh','bet_number_kh.id','=', 'record.bet_number_id')
                ->join('bet_winning_kh','bet_winning_kh.bet_number_id','=', 'bet_number_kh.id')
                ->join('bet_kh','bet_winning_kh.bet_id','=', 'bet_kh.id')
                ->join('users','users.id','=', 'bet_kh.user_id')
                ->join('bet_receipt_kh','bet_receipt_kh.id','=', 'bet_winning_kh.bet_receipt_id')
                ->join('bet_lottery_schedules as schedule','schedule.id','=', 'bet_kh.bet_schedule_id')
                ->join('bet_package_configurations as pkg_con','pkg_con.id','=', 'bet_kh.bet_package_config_id')
                ->where('bet_kh.bet_date', $date)
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
                    $q->whereIn('bet_kh.user_id', $memberIds);
                })->when(!in_array('admin', $roles) && !in_array('manager', $roles), function ($q) use ($user) {
                    $q->where('bet_kh.user_id', $user->id);
                })
                ->when($number, function ($q) use ($number){
                    $q->where('bet_kh.number_format', 'like','%'.$number.'%');
                })
                ->orderBy('bet_winning_kh.bet_id')
                ->orderBy('record.bet_winning_id')
                ->orderBy('record.bet_number_id')
                ->groupBy('bet_winning_kh.bet_id')
                ->groupBy('record.id')
                ->groupBy('record.win_number')
                ->groupBy('record.bet_winning_id')
                ->groupBy('record.bet_number_id')
                ->groupBy('bet_type')
                ->groupBy('pkg_con.rate')
                ->groupBy('pkg_con.price')
                ->groupBy('bet_kh.bet_date')
                ->groupBy('bet_kh.total_amount')
                ->groupBy('bet_winning_kh.win_amount')
                ->groupBy('schedule.province_en')
                ->groupBy('schedule.code')
                ->groupBy('bet_receipt_kh.receipt_no')
                ->groupBy('bet_number_kh.generated_number')
                ->groupBy('bet_number_kh.a_amount')
                ->groupBy('bet_number_kh.total_amount')
                ->groupBy('bet_number_kh.b_amount')
                ->groupBy('bet_number_kh.c_amount')
                ->groupBy('bet_number_kh.d_amount')
                ->groupBy('bet_number_kh.abcd_amount')
                ->groupBy('bet_number_kh.roll_amount')
                ->groupBy('bet_number_kh.roll2_amount')
                ->groupBy('bet_number_kh.roll_parlay_amount')
                ->groupBy('users.name')
                ->each(function ($record) use (&$data) {
                    $betType = $record->bet_type;
                    if ($record->sum_check) {
                        $betType = $record->bet_type . '(x)';
                    }

                    $isHN     = ($record->code ?? '') === 'HN';
                    $digitLen = (int) filter_var($record->bet_type ?? '2D', FILTER_SANITIZE_NUMBER_INT);
                    if ($digitLen === 0) $digitLen = 2;

                    $gameTypes = array_filter([
                        'A'     => (float)($record->a_amount ?? 0),
                        'B'     => (float)($record->b_amount ?? 0),
                        'C'     => (float)($record->c_amount ?? 0),
                        'D'     => (float)($record->d_amount ?? 0),
                        'ABCD'  => (float)($record->abcd_amount ?? 0),
                        'Roll'  => (float)($record->roll_amount ?? 0),
                        'Roll2' => (float)($record->roll2_amount ?? 0),
                        'RP'    => (float)($record->roll_parlay_amount ?? 0),
                    ], fn($v) => $v > 0);

                    $isFirst = true;
                    $perRecordCompensate = $record->compensate / max(1, (int)$record->total_wins_count);

                    foreach ($gameTypes as $gameLabel => $gameAmount) {
                        $multiplier = match ($gameLabel) {
                            'A'    => $isHN ? 4 : 1,
                            'ABCD' => $isHN ? ($digitLen == 3 ? 6 : 7) : 4,
                            'Roll' => $isHN
                                ? ($digitLen == 2 ? 32 : ($digitLen == 3 ? 25 : 19))
                                : ($digitLen == 2 ? 23 : ($digitLen == 3 ? 19 : 12)),
                            'Roll2' => $isHN ? 1 : 7,
                            default => 1,
                        };
                        $calcAmount = $gameAmount * $multiplier;
                        $commission = $calcAmount - ($calcAmount * $record->net / 100);
                        $netAmount  = $calcAmount * $record->net / 100;

                        $data[] = [
                            'bet_number_id'   => $record->bet_number_id,
                            'bet_id'          => $record->bet_id,
                            'account'         => $record->account,
                            'amount'          => $calcAmount,
                            'net'             => $record->net,
                            'odds'            => $record->odds,
                            'bet_type'        => $betType,
                            'original_number' => (string)$record->generated_number,
                            'company'         => $record->province_en,
                            'game'            => $gameLabel,
                            'receipt_no'      => $record->receipt_no,
                            'bet_date'        => $record->bet_date,
                            'win_number'      => $record->win_number,
                            'turnover'        => $calcAmount,
                            'commission'      => $commission,
                            'net_amount'      => $netAmount,
                            'compensate'      => $isFirst ? $perRecordCompensate : 0,
                        ];
                        $isFirst = false;
                    }
                });
            return view('bet_kh.report-winning', compact('data','companies', 'date', 'number', 'company'));
        } catch (\Exception $exception) {
            throwException($exception);
            return $exception->getMessage();
        }
    }

}
