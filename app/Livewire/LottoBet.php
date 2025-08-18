<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Bet;
use DragonCode\Support\Facades\Helpers\Digit;
use Livewire\Component;
use App\Models\BetNumber;
use App\Models\BetReceipt;
use App\Enums\MultiplierEnum;
use App\Models\BalanceReport;
use App\Enums\MultiplierHNEnum;
use App\Models\AccountManagement;
use App\Models\BetLotterySchedule;
use Illuminate\Support\Facades\DB;
use App\Enums\MultiplierHashtagEnum;
use Illuminate\Support\Facades\Auth;
use App\Enums\MultiplierHashtagHNEnum;
use App\Models\BalanceReportOutstanding;
use App\Models\BetLotteryPackageConfiguration;
use App\Models\UserBetLimit;
use Illuminate\Support\Facades\Log;

class LottoBet extends Component
{
    protected $betModel;
    protected $betLotteryScheduleModel;
    public $betPackageConfiguration;
    public $betReceipt;

    // set the total row to 15
    public $totalRow = 15;

    // define properties
    public $number = [];
    public $digit = [];
    public $a_amount = [];
    public $b_amount = [];
    public $ab_amount = [];
    public $roll_amount = [];
    public $roll7_amount = [];
    public $store_roll7_amount = [];
    public $roll_parlay_amount = [];
    public $total_amount = [];
    public $amountHN = [];
    public $amountNotHN = [];

    public $a_check = [];
    public $b_check = [];
    public $ab_check = [];
    public $roll_check = [];
    public $roll7_check = [];
    public $roll_parlay_check = [];

    public $province_check = [];
    public $province_body_check = [];

    public $enableChanelA = [];
    public $enableChanelB = [];
    public $enableChanelAB = [];
    public $enableChanelRoll = [];
    public $enableChanelRoll7 = [];
    public $enableChanelRollParlay = [];
    public $enableCheckRollParlay = [];

    public $schedules = [];
    public $currentDate;
    public $currentDay;
    public $currentTime;
    public $user;

    public $timeClose = [];
    public $invoices = [];
    public $permutations = [];
    public $permutationsLength = [];

    public $totalInvoice = 0;
    public $totalDue = 0;
    public $totalProvisional = 0;

    public $packageRate = [];
    public $lengthNum = [];
    public $isCheckHN = [];
    public $roll7AmountProvisional =0;

    public $betAccount;

    public $outstandingSummary;

    public $totalOutstanding;

    public $packagePrice;

    public function mount(
        Bet                            $betModel,
        BetLotterySchedule             $betLotteryScheduleModel,
        BetLotteryPackageConfiguration $betPackageConfiguration,
        BetReceipt                     $betReceipt,
    ) {
        // Store models
        $this->betLotteryScheduleModel = $betLotteryScheduleModel;
        $this->betModel = $betModel;
        $this->betPackageConfiguration = $betPackageConfiguration;
        $this->betReceipt = $betReceipt;
    
        // Cache current datetime calculations
        $now = Carbon::now();
        $this->currentDate = $now->format('Y-m-d');
        $this->currentDay = $now->format('l');
        $this->currentTime = $now->format('H:i:s');
    
        // Cache user
        $this->user = Auth::user();
        $userId = $this->user->id;
    
        // OPTIMIZATION 1: Single query for schedules with both data sets
        $schedulesData = $this->betLotteryScheduleModel
            ->where('draw_day', $this->currentDay)
            ->where('time_close', '>=', $this->currentTime)
            ->orderBy('company_id', 'asc')
            ->orderBy('sequence', 'asc')
            ->get(['id', 'code', 'company_id', 'time_close']);
    
            $schedulesData = $this->betLotteryScheduleModel
            ->where('draw_day', $this->currentDay)
            ->where('time_close', '>=', $this->currentTime)
            ->orderBy('company_id', 'asc')
            ->orderBy('sequence', 'asc')
            ->get(['id', 'code', 'company_id', 'time_close']);
    
            // Use the same collection for both - they're Eloquent models so they work as both objects and arrays
            $this->schedules = $schedulesData;
            $this->timeClose = $schedulesData;
            // OPTIMIZATION 2: Use raw SQL with proper indexing for better performance
            $this->betAccount = DB::table('account_management')
                ->where('user_id', $userId)
                ->sum('bet_credit');
        
        // OPTIMIZATION 3: Simplified outstanding query with better date handling
        $this->outstandingSummary = DB::table('balance_report_outstandings')
            ->select(
                'user_id',
                DB::raw('DATE(date) as report_date'),
                DB::raw('SUM(amount) as total_outstanding')
            )
            ->where('user_id', $userId)
            ->whereDate('date', $now->toDateString()) // Use Carbon instance
            ->groupBy('user_id', DB::raw('DATE(date)'))
            ->orderByDesc('report_date')
            ->first(); // Use first() since we only need one record
    
        $this->totalOutstanding = $this->outstandingSummary->total_outstanding ?? 0;
    
        // OPTIMIZATION 4: More efficient package price query
        $this->packagePrice = $this->betPackageConfiguration
            ->where('package_id', $this->user->package_id)
            ->whereIn('bet_type', ['2D', '3D', '4D'])
            ->pluck('price', 'bet_type')
            ->toArray(); // Convert to array for better performance
    
        $this->initializeProperty();
    }

    public function render()
    {
        return view('livewire.lotto-bet');
    }

    public function initializeProperty()
    {
        foreach ($this->schedules as $key => $schedule) {
            $this->province_check[$key] = false;
            $this->province_body_check[$key] = array_fill(0, $this->totalRow, false);
        }

        $this->a_amount = array_fill(0, $this->totalRow, null);
        $this->b_amount = array_fill(0, $this->totalRow, null);
        $this->ab_amount = array_fill(0, $this->totalRow, null);
        $this->roll_amount = array_fill(0, $this->totalRow, null);
        $this->roll7_amount = array_fill(0, $this->totalRow, null);
        $this->roll_parlay_amount = array_fill(0, $this->totalRow, null);
        $this->a_check = array_fill(0, $this->totalRow, false);
        $this->b_check = array_fill(0, $this->totalRow, false);
        $this->ab_check = array_fill(0, $this->totalRow, false);
        $this->roll_check = array_fill(0, $this->totalRow, false);
        $this->roll7_check = array_fill(0, $this->totalRow, false);
        $this->roll_parlay_check = array_fill(0, $this->totalRow, false);
        $this->number = array_fill(0, $this->totalRow, null);
        $this->digit = array_fill(0, $this->totalRow, "");
        $this->permutationsLength = array_fill(0, $this->totalRow, 0);
        $this->packageRate = array_fill(0, $this->totalRow, 0);
        $this->lengthNum = array_fill(0, $this->totalRow, 0);

        $this->total_amount = array_fill(0, $this->totalRow, 0);
        $this->amountHN = array_fill(0, $this->totalRow, 0);
        $this->amountNotHN = array_fill(0, $this->totalRow, 0);
        $this->store_roll7_amount = array_fill(0, $this->totalRow, null);
        $this->isCheckHN = array_fill(0, $this->totalRow, false);
    }

    public function handleProvinceCheck($index)
    {

        if ($this->province_check[$index]) {
            $this->province_body_check[$index] = array_fill(0, $this->totalRow, true);
        } else {
            $this->province_body_check[$index] = array_fill(0, $this->totalRow, false);
        }
    }

    public function handleProvinceBodyCheck($key_sch, $key_num, $item)
    {
        if ($this->province_body_check[$key_sch][$key_num]) {
            $this->province_body_check[$key_sch][$key_num] = true;
        } else {
            $this->province_body_check[$key_sch][$key_num] = false;
        }
        if ($this->lengthNum[$key_num] == 3) {
            $this->handleCheckHN($key_num);
        }

    }

    public function handleInputNumber()
    {
        foreach ($this->number as $key => $value) {
            $this->number[$key] = str_replace(' ', '', (string)$value);
            $normalizedNumber = $this->number[$key];
            if ($this->isInvalidInput($normalizedNumber)) {
                return;
            }
            if (strpos($normalizedNumber, '#') !== false) {
                $this->handleComplexBet($normalizedNumber, $key);
            } else {
                $this->handleSimpleBet($normalizedNumber, $key);
                $this->generatePermutations($normalizedNumber, $key);
            }
        }
    }

    private function generatePermutations($number, $key)
    {
        $digits = str_split((string)$number);
        if (count($digits) > 10 || !is_numeric($number)) {
            $this->permutations = [];
            return;
        }
        $this->permutations = $this->getPermutations($digits);

        $this->permutations = array_map(function ($perm) {
            return implode('', $perm);
        }, $this->permutations);

        $this->permutations = array_unique($this->permutations);
        $this->permutationsLength[$key] = count($this->permutations);
    }

    private function getPermutations($array)
    {
        if (count($array) <= 1) {
            return [$array];
        }

        $result = [];
        for ($i = 0; $i < count($array); $i++) {
            $current = $array[$i];
            $remaining = array_merge(
                array_slice($array, 0, $i),
                array_slice($array, $i + 1)
            );
            $subPerms = $this->getPermutations($remaining);

            foreach ($subPerms as $perm) {
                $result[] = array_merge([$current], $perm);
            }
        }
        return $result;
    }

    private function isInvalidInput($number)
    {
        if (strlen($number) == 1 || (strlen($number) == 5 && ctype_digit($number))) {
            return true;
        }

        if (strpos($number, '#') !== false) {
            $parts = explode('#', $number);

            foreach ($parts as $part) {
                if ($part === '' || strlen($part) == 1) {
                    return true;
                }
            }

            if (count($parts) > 4) {
                return true;
            }
        }

        return false;
    }

    private function handleSimpleBet($number, $key)
    {
        $length = strlen($number);
        $this->lengthNum[$key] = $length;
        switch ($length) {
            case 2:
                $this->setBetType($key, "2D", true, true, true, true, false, false);
                $this->roll7_amount[$key] = null;
                $this->roll_parlay_amount[$key] = null;
                $this->roll7_check[$key] = false;
                $this->roll_parlay_check[$key] = false;
                break;
            case 3:
                $this->setBetType($key, "3D", false, false, true, true, true, false);
                $this->a_amount[$key] = null;
                $this->b_amount[$key] = null;
                $this->roll_parlay_amount[$key] = null;
                $this->a_check[$key] = false;
                $this->b_check[$key] = false;
                $this->roll_parlay_check[$key] = false;
                break;
            case 4:
                $this->setBetType($key, "4D", false, false, false, true, false, false);
                $this->a_amount[$key] = null;
                $this->b_amount[$key] = null;
                $this->ab_amount[$key] = null;
                $this->roll7_amount[$key] = null;
                $this->roll_parlay_amount[$key] = null;
                $this->a_check[$key] = false;
                $this->b_check[$key] = false;
                $this->ab_check[$key] = false;
                $this->roll7_check[$key] = false;
                $this->roll_parlay_check[$key] = false;
                break;
            default:
                $this->resetBetType($key);
        }
    }

    private function handleComplexBet($normalizedNumber, $key)
    {
        $parts = explode('#', $normalizedNumber);
        $length = count($parts);
        $counts = array_count_values($parts);
        $isThreeNumTheSame = max($counts) >= 3;
        if ($length >= 2 && $length <= 4) {
            $this->digit[$key] = "RP" . $length;
            $this->setBetTypeForComplex($key);
        }

        if ($length == 2) {
            $this->enableChanelRollParlay[$key] = true;
            $this->enableCheckRollParlay[$key] = false;
        } elseif ($length == 3) {
            if ($isThreeNumTheSame) {
                $this->enableChanelRollParlay[$key] = true;
                $this->enableCheckRollParlay[$key] = false;
            } else {
                $this->roll_parlay_check[$key] = false;
                $this->enableChanelRollParlay[$key] = true;
                $this->enableCheckRollParlay[$key] = true;
            }
        } elseif ($length == 4 && !$isThreeNumTheSame) {
            $this->roll_parlay_check[$key] = true;
            $this->enableChanelRollParlay[$key] = true;
            $this->enableCheckRollParlay[$key] = false;
        } else {
            $this->roll_parlay_check[$key] = false;
        }
    }


    private function setBetType($key, $digit, $enableA, $enableB, $enableAB, $enableRoll, $enableRoll7, $chanelRollParlay)
    {
        $this->digit[$key] = $digit;
        $this->enableChanelA[$key] = $enableA;
        $this->enableChanelB[$key] = $enableB;
        $this->enableChanelAB[$key] = $enableAB;
        $this->enableChanelRoll[$key] = $enableRoll;
        $this->enableChanelRoll7[$key] = $enableRoll7;
        $this->enableChanelRollParlay[$key] = $chanelRollParlay;
        $this->enableCheckRollParlay[$key] = $chanelRollParlay;
        $this->handleCheckHN($key);

        // get rate
        $packageConfig = $this->betPackageConfiguration->where(['package_id' => $this->user->package_id, 'bet_type' => $digit])->first();
        $this->packageRate[$key] = $packageConfig->rate;
    }

    private function setBetTypeForComplex($key)
    {
        $this->enableChanelA[$key] = false;
        $this->enableChanelB[$key] = false;
        $this->enableChanelAB[$key] = false;
        $this->enableChanelRoll[$key] = false;
        $this->enableChanelRoll7[$key] = false;
    }

    private function resetBetType($key)
    {
        $this->digit[$key] = null;
        $this->enableChanelA[$key] = false;
        $this->enableChanelB[$key] = false;
        $this->enableChanelAB[$key] = false;
        $this->enableChanelRoll[$key] = false;
        $this->enableChanelRoll7[$key] = false;
        $this->enableChanelRollParlay[$key] = false;
        $this->enableCheckRollParlay[$key] = false;
    }

    private function resetChanelValues()
    {
        $fieldReset = [
            'enableChanelA',
            'enableChanelB',
            'enableChanelAB',
            'enableChanelRoll',
            'enableChanelRoll7',
            'enableChanelRollParlay',
            'enableCheckRollParlay',
        ];
        $this->reset($fieldReset);
    }

    public function handleSave()
    {
        $isCreateBetSuccess = false;
        DB::beginTransaction();
        try {
            $betReceipt = null;
            if ($this->totalInvoice > 0 && $this->totalDue > 0) {
                $account = AccountManagement::where('user_id', auth()->id())->first();
                if (!$account) {
                    $this->dispatch('bet-saved', message: 'គណនីមិនមានទឹកលុយ សូមបញ្ជូលទឹកលុយទៅគណនីលោកអ្នក!', type: 'error');
                    return back();
                }
                $newBalance = round( $this->betAccount - $this->totalDue, 2);
                if ($newBalance < 0) {
                    $this->dispatch('bet-saved', message: 'សូមបញ្ចូលទឹកលុយ', type: 'error');
                    return back();
                }else{
                    // ✅ Update AccountManagement
                    $account->bet_credit -= $this->totalDue;
                    $account->save();
                    // create initial report if not exist
                    BalanceReport::create([
                            'user_id' => auth()->id(),
                            'name_user' => auth()->user()->name,
                            'net_lose' => $this->totalDue,
                            'net_win' => 0,
                            'deposit' => 0,
                            'withdraw' => 0,
                            'adjustment' => 0,
                            'balance' => 0,
                            'report_date' => $this->currentDate,
                        ]);
                }            
                // generate no invoice
                $invoiceNumber = 'INV-' . str_pad($this->betReceipt->max('id') + 1, 8, '0', STR_PAD_LEFT);
                // create bet receipt
                $betReceipt = $this->betReceipt->create([
                    'receipt_no' => $invoiceNumber,
                    'user_id' => $this->user->id ?? 0,
                    'date' => now(),
                    'currency' => 'VND',
                    'total_amount' => $this->totalInvoice,
                    'commission' => $this->totalInvoice - $this->totalDue,
                    'net_amount' => $this->totalDue,
                    'compensate' => 0
                ]);
            }

            foreach ($this->number as $key => $number) {
                if (!empty($number)) {
                    $has_spacial = $this->roll_parlay_check[$key] ? 1 : 0;
                    $betPackage = $this->betPackageConfiguration::where('package_id','=',$this->user->package_id)
                    ->where(['bet_type' => $this->digit[$key], 'has_special' => $has_spacial])->first();
                    $rate = $betPackage?->rate / 100;
                    foreach ($this->schedules as $key_prov => $schedule) {
                        if ($this->province_body_check[$key_prov][$key] && $this->total_amount[$key] > 0) {
                            $betLimit = $this->validationBetLimitAmount($number, $key, $this->digit[$key]);
                            if ($betLimit) {
                                $this->dispatch('bet-saved', message: $betLimit, type: 'error');
                                return back();
                            }
                          //insert bet
                            $betItem = [
                                'bet_receipt_id' => $betReceipt->id,
                                'company_id' => $schedule->company_id,
                                'user_id' => $this->user->id ?? 0,
                                'bet_schedule_id' => $schedule->id,
                                'bet_package_config_id' => $betPackage->id ?? 0,
                                'number_format' => $number,
                                'digit_format' => $this->digit[$key],
                                'bet_date' => $this->currentDate,
                                'total_amount' => $this->calculateAmountOutstanding($number, $key, $schedule['code'], 1),
                            ];
                            $respone = Bet::create($betItem);
                            if ($respone) {
                                $isCreateBetSuccess = true;
                                $amountOutstanding = $this->calculateAmountOutstanding($number, $key, $schedule['code'], $rate);
                                BalanceReportOutstanding::create([
                                    'user_id' => $this->user->id ?? 0,
                                    'company_id' => $schedule->company_id,
                                    'amount' => $amountOutstanding,
                                    'date' => $this->currentDate,

                                ]);
                            }
                            //insert bet number
                            $betNumber1 = [
                                'bet_id' => $respone->id,
                                'original_number' => $number,
                                'a_amount' => $this->a_amount[$key] ?? 0,
                                'b_amount' => $this->b_amount[$key] ?? 0,
                                'ab_amount' => $this->ab_amount[$key] ?? 0,
                                'roll_amount' => $this->roll_amount[$key] ?? 0,
                                'roll7_amount' => $this->roll7_amount[$key] ?? 0,
                                'roll_parlay_amount' => $this->roll_parlay_amount[$key] ?? 0,
                                'a_check' => $this->a_check[$key],
                                'b_check' => $this->b_check[$key],
                                'ab_check' => $this->ab_check[$key],
                                'roll_check' => $this->roll_check[$key],
                                'roll7_check' => $this->roll7_check[$key],
                                'roll_parlay_check' => $this->roll_parlay_check[$key],
                            ];
                            // Define all amount/check pairs
                            $betTypes = [
                                'a' => [
                                    'amount' => $this->a_amount[$key] ?? 0,
                                    'check' => $this->a_check[$key],
                                ],
                                'b' => [
                                    'amount' => $this->b_amount[$key] ?? 0,
                                    'check' => $this->b_check[$key],
                                ],
                                'ab' => [
                                    'amount' => $this->ab_amount[$key] ?? 0,
                                    'check' => $this->ab_check[$key],
                                ],
                                'roll' => [
                                    'amount' => $this->roll_amount[$key] ?? 0,
                                    'check' => $this->roll_check[$key],
                                ],
                                'roll7' => [
                                    'amount' => $this->roll7_amount[$key] ?? 0,
                                    'check' => $this->roll7_check[$key],
                                ],
                                'roll_parlay' => [
                                    'amount' => $this->roll_parlay_amount[$key] ?? 0,
                                    'check' => $this->roll_parlay_check[$key],
                                ],
                            ];


                            if (strpos($number, '#') !== false) {
                                $multiplierHashtag =0;
                                $countHashtag = substr_count($number, '#');
                                if($schedule->code=="HN") {
                                    $multiplierHashtag = match ($countHashtag) {
                                        1 => MultiplierHashtagHNEnum::one,
                                        2 => MultiplierHashtagHNEnum::two,
                                        3 => MultiplierHashtagHNEnum::three,
                                        default => 1
                                    };
                                }else
                                {
                                    $multiplierHashtag = match ($countHashtag) {
                                        1 => MultiplierHashtagEnum::one,
                                        2 => MultiplierHashtagEnum::two,
                                        3 => MultiplierHashtagEnum::three,
                                        default => 1
                                    };
                                }

                                if ($this->roll_parlay_check[$key] == true) {
                                    $multiplierOneHashtag = $schedule->code == "HN" ? MultiplierHashtagHNEnum::one : MultiplierHashtagEnum::one;
                                    $parts = $this->generateSharpNumber($number);
                                    foreach ($parts as $part) {
                                        $betNumber2 = [
                                            'generated_number' => $part,
                                            'digit_length' => $this->digit[$key],
                                            'total_amount' => $this->roll_parlay_amount[$key] * $multiplierOneHashtag,
                                        ];

                                        $data = array_merge($betNumber1, $betNumber2);
                                        BetNumber::create($data);
                                    }
                                } else {
                                    $betNumber2 = [
                                        'generated_number' => $number,
                                        'digit_length' => $this->digit[$key],
                                        'total_amount' => $this->roll_parlay_amount[$key] * $multiplierHashtag,
                                    ];
                                    $data = array_merge($betNumber1, $betNumber2);
                                    BetNumber::create($data);
                                }
                            } else if (strpos($number, '*') !== false) {
                                foreach ($betTypes as $type => $info) {
                                    if ($info['amount'] > 0) {
                                        $betNumber1 = [
                                            'bet_id' => $respone->id,
                                            'original_number' => $number,
                                        ];

                                        $amounts = [
                                            'a_amount' => 0, 'b_amount' => 0, 'ab_amount' => 0,
                                            'roll_amount' => 0, 'roll7_amount' => 0, 'roll_parlay_amount' => 0,
                                        ];
                                        $checkeds = [
                                            'a_check' => 0, 'b_check' => 0, 'ab_check' => 0,
                                            'roll_check' => 0, 'roll7_check' => 0, 'roll_parlay_check' => 0,
                                        ];

                                        $amounts["{$type}_amount"] = $info['amount'];
                                        $checkeds["{$type}_check"] = $info['check'];

                                        $num = trim($number, '*');
                                        $numberLength = strlen($num) + 1;

                                        $total_amount = $this->calculateBetNumberTotalAmount($numberLength, $info['amount'], $schedule->code, $type);
                                        for ($i = 0; $i < 10; $i++) {
                                            $genNumber = str_starts_with($number, '*') ? $i . $num : $num . $i;
                                            $betNumber2 = [
                                                'generated_number' => $genNumber,
                                                'digit_length' => strlen($genNumber),
                                                'total_amount'=> $total_amount,
                                            ];

                                            $data = array_merge($betNumber1, $betNumber2, $amounts);
                                            BetNumber::create($data);
                                        }
                                    }
                                }
                            } else {
                                foreach ($betTypes as $type => $info) {
                                    if ($info['amount'] > 0 || $info['check'] > 0) {
                                        $betNumber1 = [
                                            'bet_id' => $respone->id,
                                            'original_number' => $number,
                                        ];

                                        $amounts = [
                                            'a_amount' => 0, 'b_amount' => 0, 'ab_amount' => 0,
                                            'roll_amount' => 0, 'roll7_amount' => 0, 'roll_parlay_amount' => 0,
                                        ];
                                        $checkeds = [
                                            'a_check' => 0, 'b_check' => 0, 'ab_check' => 0,
                                            'roll_check' => 0, 'roll7_check' => 0, 'roll_parlay_check' => 0,
                                        ];

                                        $amounts["{$type}_amount"] = $info['amount'];
                                        $checkeds["{$type}_check"] = $info['check'];

                                        // If check > 0, generate all permutations
                                        if ($info['check'] > 0) {
                                            $combinations = $this->generateUniquePermutations(str_split($number));
                                        } else {
                                            // Just use the original value
                                            $combinations = [$number];
                                        }
                                        $numberLength = strlen($number);
                                        $total_amount = $this->calculateBetNumberTotalAmount($numberLength, $info['amount'], $schedule->code, $type);

                                        foreach ($combinations as $combo) {
                                            $betNumber2 = [
                                                'generated_number' => $combo,
                                                'digit_length' => strlen($combo),
                                                'total_amount' => $total_amount,
                                            ];

                                            $data = array_merge($betNumber1, $betNumber2, $amounts, $checkeds);
                                            BetNumber::create($data);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            dump($e->getMessage());
        }
        if ($isCreateBetSuccess) {
            $this->handleReset();
            $this->dispatch('bet-saved', message: 'Bet saved successfully!');
            return redirect()->to('lotto_vn/bet_receipt/' . $betReceipt->receipt_no);
        }
    }
    private function validationBetLimitAmount($number, $key, $digit)
    {
        $betTypes = [
            'a' => [
                'amount' => $this->a_amount[$key] ?? 0,
                'check' => $this->a_check[$key],
            ],
            'b' => [
                'amount' => $this->b_amount[$key] ?? 0,
                'check' => $this->b_check[$key],
            ],
            'ab' => [
                'amount' => $this->ab_amount[$key] ?? 0,
                'check' => $this->ab_check[$key],
            ],
            'roll' => [
                'amount' => $this->roll_amount[$key] ?? 0,
                'check' => $this->roll_check[$key],
            ],
            'roll7' => [
                'amount' => $this->roll7_amount[$key] ?? 0,
                'check' => $this->roll7_check[$key],
            ],
            'roll_parlay' => [
                'amount' => $this->roll_parlay_amount[$key] ?? 0,
                'check' => $this->roll_parlay_check[$key],
            ],
        ];
        if (strpos($number, '#') !== false) {
            if($this->roll_parlay_amount[$key] > 0) {
                $digitKey = ($digit === 'RP3' && $this->roll_parlay_check[$key] != 1) ? 'RP3' : 'RP2';
                $checkBetLimit = UserBetLimit::where('user_id', $this->user->id)
                        ->where('digit_key', $digitKey)
                        ->first();
                    if ($checkBetLimit) {
                    if ($this->roll_parlay_amount[$key] < $checkBetLimit->min_bet) {
                        $message = "Your bet amount is below the minimum limit ({$checkBetLimit->min_bet})";
                        // You can now use $message variable or return it
                        return $message;
                    }
                    if ($this->roll_parlay_amount[$key] > $checkBetLimit->max_bet) {
                        $message = "Your bet amount exceeds the maximum limit ({$checkBetLimit->max_bet})";
                        // You can now use $message variable or return it
                        return $message;
                    }
                }
            }
        }else{
            foreach ($betTypes as $info) {
                if ($info['amount'] > 0) {
                        $checkBetLimit = UserBetLimit::where('user_id', $this->user->id)
                            ->where('digit_key', $digit)
                            ->first();
                        if ($checkBetLimit) {
                        if ($info['amount'] < $checkBetLimit->min_bet) {
                            $message = "Your bet amount is below the minimum limit ({$checkBetLimit->min_bet})";
                            // You can now use $message variable or return it
                            return $message;
                        }
                        if ($info['amount'] > $checkBetLimit->max_bet) {
                            $message = "Your bet amount exceeds the maximum limit ({$checkBetLimit->max_bet})";
                            // You can now use $message variable or return it
                            return $message;
                        }
                    }
                }
            }
        }
    }
    private function calculateAmountOutstanding($number, $key, $code, $rate)
    {

        $betTypes = [
            'a' => [
                'amount' => $this->a_amount[$key] ?? 0,
                'check' => $this->a_check[$key],
            ],
            'b' => [
                'amount' => $this->b_amount[$key] ?? 0,
                'check' => $this->b_check[$key],
            ],
            'ab' => [
                'amount' => $this->ab_amount[$key] ?? 0,
                'check' => $this->ab_check[$key],
            ],
            'roll' => [
                'amount' => $this->roll_amount[$key] ?? 0,
                'check' => $this->roll_check[$key],
            ],
            'roll7' => [
                'amount' => $this->roll7_amount[$key] ?? 0,
                'check' => $this->roll7_check[$key],
            ],
            'roll_parlay' => [
                'amount' => $this->roll_parlay_amount[$key] ?? 0,
                'check' => $this->roll_parlay_check[$key],
            ],
        ];

        $amount = 0;
        if (strpos($number, '#') !== false) {
            if($this->roll_parlay_amount[$key]> 0) {
                $multiplier = 1;
                $permuLength = 1;
                $countHashtag = substr_count($number, '#');
                if ($this->roll_parlay_check[$key] == true && $countHashtag ==2) {
                    $permuLength = 2;
                }
                if ($code == "HN") {
                    $multiplier = match ($countHashtag) {
                        1 => MultiplierHashtagHNEnum::one,
                        2 => MultiplierHashtagHNEnum::two,
                        3 => MultiplierHashtagHNEnum::three,
                        default => 1
                    };
                } else {
                    $multiplier = match ($countHashtag) {
                        1 => MultiplierHashtagEnum::one,
                        2 => MultiplierHashtagEnum::two,
                        3 => MultiplierHashtagEnum::three,
                        default => 1
                    };
                }
                $amount += $this->roll_parlay_amount[$key] * $multiplier * $permuLength;
            }

        } elseif (strpos($number, '*') !== false) {
            $length = strlen($number);
            foreach ($betTypes as $type => $info) {
                $multiplier = 1;
                $permuLength = 10;
                if ($info['amount'] > 0) {
                    if ($code == "HN") {
                        $multiplier = match ($type) {
                            'a' => $length == 2 ? MultiplierHNEnum::A : 0,
                            'ab' => $length == 2 ? MultiplierHNEnum::AB :
                                ($length == 3 ? MultiplierHNEnum::AB_3D : 0),
                            'roll' => $length == 2 ? MultiplierHNEnum::ROLL :
                                ($length == 3 ? MultiplierHNEnum::ROLL_3D :
                                    ($length == 4 ? MultiplierHNEnum::ROLL_4D : 0)),
                            default => 1,
                        };
                    } else {
                        $multiplier = match ($type) {
                            'ab' => $length == 2 ? MultiplierEnum::AB :
                                ($length == 3 ? MultiplierEnum::AB : 0),
                            'roll' => $length == 2 ? MultiplierEnum::ROLL :
                                ($length == 3 ? MultiplierEnum::ROLL_3D :
                                    ($length == 4 ? MultiplierEnum::ROLL_4D : 0)),
                            'roll7' => $length == 3 ? MultiplierEnum::ROLL7 : 0,
                            default => 1,
                        };
                    }
                    $amount += $info['amount'] * $multiplier * $permuLength;
                }
            }

        } else {
            $length = strlen($number);
            foreach ($betTypes as $type => $info) {
                $multiplier = 1;
                $permuLength = 1;
                if ($info['amount'] > 0) {
                    if($info['check']> 0){
                        $permuLength = $this->permutationsLength[$key];
                    }
                    if ($code == "HN") {
                        $multiplier = match ($type) {
                            'a' => $length == 2 ? MultiplierHNEnum::A : 0,
                            'ab' => $length == 2 ? MultiplierHNEnum::AB :
                                ($length == 3 ? MultiplierHNEnum::AB_3D : 0),
                            'roll' => $length == 2 ? MultiplierHNEnum::ROLL :
                                ($length == 3 ? MultiplierHNEnum::ROLL_3D :
                                    ($length == 4 ? MultiplierHNEnum::ROLL_4D : 0)),
                            default => 1,
                        };
                    } else {
                        $multiplier = match ($type) {
                            'ab' => $length == 2 ? MultiplierEnum::AB :
                                ($length == 3 ? MultiplierEnum::AB : 0),
                            'roll' => $length == 2 ? MultiplierEnum::ROLL :
                                ($length == 3 ? MultiplierEnum::ROLL_3D :
                                    ($length == 4 ? MultiplierEnum::ROLL_4D : 0)),
                            'roll7' => $length == 3 ? MultiplierEnum::ROLL7 : 0,
                            default => 1,
                        };
                    }
                    $amount += $info['amount'] * $multiplier * $permuLength;
                }
            }
        }
        return ($amount* $rate);

    }

    private function calculateBetNumberTotalAmount($numberLength, $amount, $code, $type)
    {
        $multiplier = 0;
        if ($code == "HN") {
            $multiplier = match ($type) {
                'a' => $numberLength == 2 ? MultiplierHNEnum::A : 0,
                'ab' => $numberLength == 2 ? MultiplierHNEnum::AB : ($numberLength == 3 ? MultiplierHNEnum::AB_3D : 0),
                'roll' => $numberLength == 2 ? MultiplierHNEnum::ROLL : ($numberLength == 3 ? MultiplierHNEnum::ROLL_3D : ($numberLength == 4 ? MultiplierHNEnum::ROLL_4D : 0)),
                default => 1,
            };
        } else {
            $multiplier = match ($type) {
                'ab' => $numberLength == 2 ? MultiplierEnum::AB : ($numberLength == 3 ? MultiplierEnum::AB : 0),
                'roll' => $numberLength == 2 ? MultiplierEnum::ROLL : ($numberLength == 3 ? MultiplierEnum::ROLL_3D : ($numberLength == 4 ? MultiplierEnum::ROLL_4D : 0)),
                'roll7' => $numberLength == 3 ? MultiplierEnum::ROLL7 : 0,
                default => 1,
            };
        }

        return $amount * $multiplier;
    }

    private function generateSharpNumber($number)
    {
        $parts = explode('#', $number);

        $result = [];

        for ($i = 0; $i < count($parts) - 1; $i++) {
            for ($j = $i + 1; $j < count($parts); $j++) {
                $result[] = $parts[$i] . '#' . $parts[$j];
            }
        }
        return array_unique($result);
    }

    function generateUniquePermutations(array $digits): array
    {
        $results = [];
        $recurse = function ($current, $remaining) use (&$results, &$recurse) {
            if (count($remaining) === 0) {
                $results[] = implode('', $current);
                return;
            }
            $used = [];
            foreach ($remaining as $i => $digit) {
                if (in_array($digit, $used)) continue; // skip same digit at same level
                $used[] = $digit;
                $next = $current;
                $next[] = $digit;
                $nextRemaining = $remaining;
                unset($nextRemaining[$i]);
                $nextRemaining = array_values($nextRemaining); // reindex
                $recurse($next, $nextRemaining);
            }
        };

        $recurse([], $digits);
        return array_unique($results);
    }

    public function handleReset()
    {
        $this->resetChanelValues();
        $field = [
            'number',
            'totalInvoice',
            'totalDue',
            'invoices',
            'province_check',
            'province_body_check',
            'a_amount',
            'b_amount',
            'ab_amount',
            'roll_amount',
            'roll7_amount',
            'roll_parlay_amount',
            'total_amount',
            'amountHN',
            'amountNotHN',
            'a_check',
            'b_check',
            'ab_check',
            'roll_check',
            'roll7_check',
            'roll_parlay_check'
        ];
        $this->reset($field);
        $this->initializeProperty();

    }


    public function handleCheckChanel($key, $name = "")
    {
    }

    public function handleInputAmount($key)
    {
        $this->store_roll7_amount[$key] = null;
    }

    private function handleCheckHN($key_num)
    {
        $isHN = false;
        if ($this->lengthNum[$key_num] == 3) {
            if ($this->roll7_amount[$key_num] !=null) {
                $this->store_roll7_amount[$key_num] = $this->roll7_amount[$key_num];
            }
            foreach ($this->schedules as $key => $item) {
                if ($this->province_body_check[$key][$key_num]) {
                    if ($item['code'] === 'HN') {
                        $isHN = true;
                        $this->enableChanelRoll7[$key_num] = false;
                        $this->roll7_amount[$key_num] = null;
                    }

                }
            }
            if (!$isHN) {
                $this->enableChanelRoll7[$key_num] = true;
                $this->roll7_amount[$key_num] = $this->store_roll7_amount[$key_num];
            }
        }

    }

    public function updated($propertyName)
    {
        $str = $propertyName;
        $parts = explode('.', $str);
        $index = end($parts);
        if ($parts[0] === 'province_check') {
            $this->province_body_check[$index] = array_fill(0, $this->totalRow, $this->province_check[$index]);
        }

        if (count($this->number) > 0 && count($this->province_body_check) > 0 || count($this->province_check) > 0) {
            $updatedInvoices = [];

            foreach ($this->number as $key => $num) {
                $lengthOfNum = strlen($num);
                if ($lengthOfNum > 0) {
                    $chanel = [];
                    $amount = [];
                    $countProvince = 0;

                    foreach ($this->schedules as $key_prov => $schedule) {
                        if ($this->province_body_check[$key_prov][$key] == true) {
                            $countProvince++;
                            $chanel[] = $schedule->code;
                        } else {
                            $this->total_amount[$key] = 0;
                            $this->totalInvoice = 0;
                            $this->totalDue = 0;
                            foreach ($this->total_amount as $key1 => $total) {
                                if ($total > 0) {
                                    $this->totalInvoice += $total;
                                    $this->totalDue += $total * intval($this->packageRate[$key1]) / 100;
                                }
                            }
                        }
                    }

                    $isAsterisk = preg_match('/^\*\d+$|\d+\*$/', $num);
                    $countHashtag = substr_count($num, '#');

                    if ($countProvince > 0) {
                        $this->handleCheckHN($key);
                        $this->totalAmountNormalNumber($key, $lengthOfNum, $isAsterisk, $countHashtag);
                    }
                    $this->addAmount($amount, $this->b_amount[$key] ?? 0, $this->b_check[$key] ?? false, "B", $key);
                    $this->addAmount($amount, $this->a_amount[$key] ?? 0, $this->a_check[$key] ?? false, "A", $key);
                    $this->addAmount($amount, $this->ab_amount[$key] ?? 0, $this->ab_check[$key] ?? false, "AB", $key);
                    $this->addAmount($amount, $this->roll_amount[$key] ?? 0, $this->roll_check[$key] ?? false, "R", $key);
                    $this->addAmount($amount, $this->roll7_amount[$key] ?? 0, $this->roll7_check[$key] ?? false, "R7", $key);
                    $this->addAmount($amount, $this->roll_parlay_amount[$key] ?? 0, $this->roll_parlay_check[$key] ?? false, "RP", $key);
                    if (count($chanel) > 0 && count($amount) > 0 && $this->total_amount[$key] > 0) {

                        $updatedInvoices[$key] = [
                            'number' => $num,
                            'chanel' => $chanel,
                            'amount' => $amount
                        ];
                    }
                }
            }
            $this->invoices = [];
            $this->invoices = $updatedInvoices;
        }
    }

    private function addAmount(&$amountArray, $value, $check, $label, $index)
    {
        if ($value > 0) {
            $amountArray[] = $value . ($check ? "({$label}x)" : "({$label})");
        }
    }

    // calculate total
    private function totalAmountNormalNumber($key, $lengthNumber, $isAsterisk, $countHashtag): void
    {
        $this->totalProvisional = 0;
        $this->totalProvisionalHN = 0;
        $this->roll7AmountProvisional =0;
        $this->isCheckHN[$key] = false;
        foreach ($this->schedules as $keys => $schedule) {
            if ($this->province_body_check[$keys][$key]) {
                if ($schedule->code == "HN") {
                    $this->isCheckHN[$key] = true;
                    if ($this->a_amount[$key] > 0) {
                        if ($this->a_check[$key]) {
                            $this->totalProvisionalHN += $this->a_amount[$key] * MultiplierHNEnum::A * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisionalHN += $this->a_amount[$key] * MultiplierHNEnum::A * 10;
                            } else {
                                $this->totalProvisionalHN += $this->a_amount[$key] * MultiplierHNEnum::A;
                            }
                        }
                    }

                    if ($this->b_amount[$key] > 0) {
                        if ($this->b_check[$key]) {
                            $this->totalProvisionalHN += $this->b_amount[$key] * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisionalHN += $this->b_amount[$key] * 10;
                            } else {
                                $this->totalProvisionalHN += $this->b_amount[$key];
                            }
                        }
                    }

                    if ($this->ab_amount[$key] > 0) {
                        $ab = match ($lengthNumber) {
                            2 => MultiplierHNEnum::AB,
                            3 => MultiplierHNEnum::AB_3D,
                            default => 1
                        };
                        if ($this->ab_check[$key]) {
                            $this->totalProvisionalHN += $this->ab_amount[$key] * $ab * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisionalHN += $this->ab_amount[$key] * $ab * 10;
                            } else {
                                $this->totalProvisionalHN += $this->ab_amount[$key] * $ab;
                            }
                        }
                    }

                    if ($this->roll_amount[$key] > 0) {
                        $roll = match ($lengthNumber) {
                            2 => MultiplierHNEnum::ROLL,
                            3 => MultiplierHNEnum::ROLL_3D,
                            4 => MultiplierHNEnum::ROLL_4D,
                            default => 1
                        };
                        if ($this->roll_check[$key]) {
                            $this->totalProvisionalHN += $this->roll_amount[$key] * $roll * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisionalHN += $this->roll_amount[$key] * $roll * 10;
                            } else {
                                $this->totalProvisionalHN += $this->roll_amount[$key] * $roll;
                            }
                        }
                    }


                    if ($this->roll_parlay_amount[$key] > 0) {
                        $value = match ($countHashtag) {
                            1 => MultiplierHashtagHNEnum::one,
                            2 => MultiplierHashtagHNEnum::two,
                            3 => MultiplierHashtagHNEnum::three,
                            default => 1
                        };
                        if ($this->roll_parlay_check[$key]) {
                            if ($countHashtag == 2) {
                                $this->totalProvisionalHN += $this->roll_parlay_amount[$key] * $value * $countHashtag;
                            } else {
                                $this->totalProvisionalHN += $this->roll_parlay_amount[$key] * $value;
                            }
                        } else {
                            $this->totalProvisionalHN += $this->roll_parlay_amount[$key] * $value;
                        }
                    }
                } else {
                    if ($this->a_amount[$key] > 0) {
                        if ($this->a_check[$key]) {
                            $this->totalProvisional += $this->a_amount[$key] * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisional += $this->a_amount[$key] * 10;
                            } else {
                                $this->totalProvisional += $this->a_amount[$key];
                            }
                        }
                    }

                    if ($this->b_amount[$key] > 0) {
                        if ($this->b_check[$key]) {
                            $this->totalProvisional += $this->b_amount[$key] * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisional += $this->b_amount[$key] * 10;
                            } else {
                                $this->totalProvisional += $this->b_amount[$key];
                            }
                        }
                    }

                    if ($this->ab_amount[$key] > 0) {
                        if ($this->ab_check[$key]) {
                            $this->totalProvisional += ($this->ab_amount[$key] * MultiplierEnum::AB) * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisional += $this->ab_amount[$key] * MultiplierEnum::AB * 10;
                            } else {
                                $this->totalProvisional += $this->ab_amount[$key] * MultiplierEnum::AB;
                            }
                        }
                    }
                    if ($this->roll_amount[$key] > 0) {
                        $roll = match ($lengthNumber) {
                            2 => MultiplierEnum::ROLL,
                            3 => MultiplierEnum::ROLL_3D,
                            4 => MultiplierEnum::ROLL_4D,
                            default => 1
                        };
                        if ($this->roll_check[$key]) {
                            $this->totalProvisional += $this->roll_amount[$key] * $roll * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisional += $this->roll_amount[$key] * $roll * 10;
                            } else {
                                $this->totalProvisional += $this->roll_amount[$key] * $roll;
                            }
                        }
                    }
                    if ($this->roll7_amount[$key] > 0) {
                        if ($this->roll7_check[$key]) {
                            $this->roll7AmountProvisional += $this->roll7_amount[$key] * MultiplierEnum::ROLL7 * $this->permutationsLength[$key];
                        }
                        else {
                            if ($isAsterisk) {
                                $this->roll7AmountProvisional += $this->roll7_amount[$key] * MultiplierEnum::ROLL7 * 10;
                            } else {
                                $this->roll7AmountProvisional += $this->roll7_amount[$key] * MultiplierEnum::ROLL7;
                            }
                        }
                    }

                    if ($this->roll_parlay_amount[$key] > 0) {
                        $value = match ($countHashtag) {
                            1 => MultiplierHashtagEnum::one,
                            2 => MultiplierHashtagEnum::two,
                            3 => MultiplierHashtagEnum::three,
                            default => 1
                        };

                        if ($this->roll_parlay_check[$key]) {
                            if ($countHashtag == 2) {
                                $this->totalProvisional += $this->roll_parlay_amount[$key] * $value * $countHashtag;
                            } else {
                                $this->totalProvisional += $this->roll_parlay_amount[$key] * $value;
                            }
                        } else {
                            $this->totalProvisional += $this->roll_parlay_amount[$key] * $value;
                        }
                    }
                }
            }
        }

        $this->amountHN[$key] = $this->totalProvisionalHN;
        $this->amountNotHN[$key] = $this->totalProvisional;
        $this->total_amount[$key] = $this->totalProvisional + $this->totalProvisionalHN;
        if(!$this->isCheckHN[$key] ){
            $this->total_amount[$key] += $this->roll7AmountProvisional;
        }
        // invoice
        $this->totalInvoice = 0;
        $this->totalDue = 0;
        foreach ($this->total_amount as $key => $total) {
            $this->totalInvoice += $total;
            $this->totalDue += $total * intval($this->packageRate[$key]) / 100;
        }

    }
}
