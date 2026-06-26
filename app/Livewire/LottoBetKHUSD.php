<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\BetKHUSD;
use Livewire\Component;
use App\Models\BetNumberKHUSD;
use App\Models\UserBetLimit;
use App\Enums\MultiplierKHEnum;
use App\Enums\MultiplierHNKHEnum;
use App\Models\BetReceiptKHUSD;
use App\Models\AccountUSD;
use App\Models\CreditTransactionUSD;
use App\Models\BetLotterySchedule;
use Illuminate\Support\Facades\DB;
use App\Enums\MultiplierHashtagKHEnum;
use Illuminate\Support\Facades\Auth;
use App\Enums\MultiplierHashtagHNKHEnum;
use App\Models\BetLotteryPackageConfiguration;
use Illuminate\Support\Facades\Log;

class LottoBetKHUSD extends Component
{
    protected $betModel;
    protected $betLotteryScheduleModel;
    public $betPackageConfiguration;
    public $betReceipt;

    public $totalRow = 15;

    public $number = [];
    public $digit = [];
    public $a_amount = [];
    public $b_amount = [];
    public $c_amount = [];
    public $d_amount = [];
    public $abcd_amount = [];
    public $roll_amount = [];
    public $roll2_amount = [];
    public $roll_parlay_amount = [];
    public $total_amount = [];
    public $amountHN = [];
    public $amountNotHN = [];

    public $a_check = [];
    public $b_check = [];
    public $c_check = [];
    public $d_check = [];
    public $abcd_check = [];
    public $roll_check = [];
    public $roll2_check = [];
    public $roll_parlay_check = [];

    public $province_check = [];
    public $province_body_check = [];

    public $enableChanelA = [];
    public $enableChanelB = [];
    public $enableChanelC = [];
    public $enableChanelD = [];
    public $enableChanelABCD = [];
    public $enableChanelRoll = [];
    public $enableChanelRoll2 = [];
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

    public $betAccount;

    public $totalOutstanding = 0;

    public $packagePrice;
    public string $currency = 'usd';


    public function mount(
        BetKHUSD                       $betModel,
        BetLotterySchedule             $betLotteryScheduleModel,
        BetLotteryPackageConfiguration $betPackageConfiguration,
        BetReceiptKHUSD                $betReceipt,
    )
    {
        $this->betLotteryScheduleModel = $betLotteryScheduleModel;
        $this->betModel = $betModel;
        $this->betPackageConfiguration = $betPackageConfiguration;
        $this->betReceipt = $betReceipt;

        $this->currentDate = Carbon::now()->format('Y-m-d');
        $this->currentDay = Carbon::now()->format('l');
        $this->currentTime = Carbon::now()->format('H:i:s');

        $this->user = Auth::user();

        $this->schedules = $this->betLotteryScheduleModel
            ->where('draw_day', '=', $this->currentDay)
            ->where('time_close', '>=', $this->currentTime)
            ->orderBy('company_id', 'asc')
            ->orderBy('sequence', 'asc')
            ->get(['id', 'code', 'company_id']);
        $this->timeClose = $this->betLotteryScheduleModel
            ->where('draw_day', '=', $this->currentDay)
            ->where('time_close', '>=', $this->currentTime)
            ->orderBy('company_id', 'asc')
            ->orderBy('sequence', 'asc')
            ->get(['id', 'code', 'time_close']);

        // USD users share the same AccountUSD balance across Vietnam and Cambodia bets
        $this->betAccount = AccountUSD::where('user_id', $this->user->id)->value('credit_balance') ?? 0;

        $this->totalOutstanding = DB::table('bet_kh_usd')
            ->where('user_id', $this->user->id)
            ->whereDate('bet_date', $this->currentDate)
            ->sum('total_amount');

        $settledCompanyIds = DB::table('bet_lottery_results')
            ->join('bet_lottery_schedules', 'bet_lottery_schedules.id', '=', 'bet_lottery_results.lottery_schedule_id')
            ->whereDate('bet_lottery_results.draw_date', Carbon::today())
            ->pluck('bet_lottery_schedules.company_id')
            ->unique()
            ->toArray();

        $this->packagePrice = $this->betPackageConfiguration
            ->where('package_id', $this->user->package_id)
            ->whereIn('bet_type', ['2D', '3D', '4D'])
            ->pluck('price', 'bet_type');
        $this->initializeProperty();
    }


    public function render()
    {
        return view('livewire.lotto-bet-kh');
    }

    public function initializeProperty()
    {
        foreach ($this->schedules as $key => $schedule) {
            $this->province_check[$key] = false;
            $this->province_body_check[$key] = array_fill(0, $this->totalRow, false);
        }

        $this->a_amount = array_fill(0, $this->totalRow, null);
        $this->b_amount = array_fill(0, $this->totalRow, null);
        $this->c_amount = array_fill(0, $this->totalRow, null);
        $this->d_amount = array_fill(0, $this->totalRow, null);
        $this->abcd_amount = array_fill(0, $this->totalRow, null);
        $this->roll_amount = array_fill(0, $this->totalRow, null);
        $this->roll2_amount = array_fill(0, $this->totalRow, null);
        $this->roll_parlay_amount = array_fill(0, $this->totalRow, null);
        $this->a_check = array_fill(0, $this->totalRow, false);
        $this->b_check = array_fill(0, $this->totalRow, false);
        $this->c_check = array_fill(0, $this->totalRow, false);
        $this->d_check = array_fill(0, $this->totalRow, false);
        $this->abcd_check = array_fill(0, $this->totalRow, false);
        $this->roll_check = array_fill(0, $this->totalRow, false);
        $this->roll2_check = array_fill(0, $this->totalRow, false);
        $this->roll_parlay_check = array_fill(0, $this->totalRow, false);
        $this->number = array_fill(0, $this->totalRow, null);
        $this->digit = array_fill(0, $this->totalRow, "");
        $this->permutationsLength = array_fill(0, $this->totalRow, 0);
        $this->packageRate = array_fill(0, $this->totalRow, 0);
        $this->lengthNum = array_fill(0, $this->totalRow, 0);

        $this->total_amount = array_fill(0, $this->totalRow, 0);
        $this->amountHN = array_fill(0, $this->totalRow, 0);
        $this->amountNotHN = array_fill(0, $this->totalRow, 0);
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
                $this->setBetType($key, "2D", true, true, true, true, true, true, true, false);
                $this->roll_parlay_amount[$key] = null;
                $this->roll_parlay_check[$key] = false;
                break;
            case 3:
                $this->setBetType($key, "3D", true, true, true, true, true, true, true, false);
                $this->roll_parlay_amount[$key] = null;
                $this->roll_parlay_check[$key] = false;
                break;
            case 4:
                $this->setBetType($key, "4D", false, false, false, false, false, true, false, false);
                $this->a_amount[$key] = null;
                $this->b_amount[$key] = null;
                $this->c_amount[$key] = null;
                $this->d_amount[$key] = null;
                $this->abcd_amount[$key] = null;
                $this->roll2_amount[$key] = null;
                $this->roll_parlay_amount[$key] = null;
                $this->a_check[$key] = false;
                $this->b_check[$key] = false;
                $this->c_check[$key] = false;
                $this->d_check[$key] = false;
                $this->abcd_check[$key] = false;
                $this->roll2_check[$key] = false;
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


    private function setBetType($key, $digit, $enableA, $enableB, $enableC, $enableD, $enableABCD, $enableRoll, $enableRoll2, $chanelRollParlay)
    {
        $this->digit[$key] = $digit;
        $this->enableChanelA[$key] = $enableA;
        $this->enableChanelB[$key] = $enableB;
        $this->enableChanelC[$key] = $enableC;
        $this->enableChanelD[$key] = $enableD;
        $this->enableChanelABCD[$key] = $enableABCD;
        $this->enableChanelRoll[$key] = $enableRoll;
        $this->enableChanelRoll2[$key] = $enableRoll2;
        $this->enableChanelRollParlay[$key] = $chanelRollParlay;
        $this->enableCheckRollParlay[$key] = $chanelRollParlay;

        $packageConfig = $this->betPackageConfiguration->where(['package_id' => $this->user->package_id, 'bet_type' => $digit])->first();
        $this->packageRate[$key] = $packageConfig->rate;
    }

    private function setBetTypeForComplex($key)
    {
        $this->enableChanelA[$key] = false;
        $this->enableChanelB[$key] = false;
        $this->enableChanelC[$key] = false;
        $this->enableChanelD[$key] = false;
        $this->enableChanelABCD[$key] = false;
        $this->enableChanelRoll[$key] = false;
        $this->enableChanelRoll2[$key] = false;
    }

    private function resetBetType($key)
    {
        $this->digit[$key] = null;
        $this->enableChanelA[$key] = false;
        $this->enableChanelB[$key] = false;
        $this->enableChanelC[$key] = false;
        $this->enableChanelD[$key] = false;
        $this->enableChanelABCD[$key] = false;
        $this->enableChanelRoll[$key] = false;
        $this->enableChanelRoll2[$key] = false;
        $this->enableChanelRollParlay[$key] = false;
        $this->enableCheckRollParlay[$key] = false;
    }

    private function resetChanelValues()
    {
        $fieldReset = [
            'enableChanelA',
            'enableChanelB',
            'enableChanelC',
            'enableChanelD',
            'enableChanelABCD',
            'enableChanelRoll',
            'enableChanelRoll2',
            'enableChanelRollParlay',
            'enableCheckRollParlay',
        ];
        $this->reset($fieldReset);
    }

    public function handleSave()
    {
        $isCreateBetSuccess = false;
        // USD users share AccountUSD across Vietnam and Cambodia bets
        $AccountModel = AccountUSD::class;
        $TransactionModel = CreditTransactionUSD::class;
        $BetModel = BetKHUSD::class;
        $BetNumberModel = BetNumberKHUSD::class;
        $betTable = 'bet_kh_usd';
        $betNumberTable = 'bet_number_kh_usd';

        DB::beginTransaction();
        try {
            $userSuspended = $this->user->is_active;
            if ($userSuspended == 0) {
                $this->dispatch('bet-saved', message: 'Account has been suspended', type: 'error');
                return back();
            }
            $betReceipt = null;
            if ($this->totalInvoice > 0 && $this->totalDue > 0) {
                $account = $AccountModel::where('user_id', auth()->id())->first();
                if (!$account) {
                    $this->dispatch('bet-saved', message: 'គណនីមិនមានទឹកលុយ (USD) សូមបញ្ជូលទឹកលុយទៅគណនីលោកអ្នក!', type: 'error');
                    return back();
                }
                $newBalance = round((float) $account->credit_balance - $this->totalDue, 2);
                if ($newBalance < 0) {
                    $shortage = number_format(abs($newBalance), 2);
                    $this->dispatch('bet-saved', message: "Insufficient credit (USD)! Please add {$shortage} more to your account.", type: 'error');
                    return back();
                } else {
                    $balanceBefore = (float) $account->credit_balance;
                    $account->credit_balance -= $this->totalDue;
                    $account->save();
                    $TransactionModel::create([
                        'user_id'        => auth()->id(),
                        'type'           => 'bet_debit',
                        'amount'         => $this->totalDue,
                        'balance_before' => $balanceBefore,
                        'balance_after'  => $account->credit_balance,
                        'note'           => 'Bet placed',
                        'bet_date'       => $this->currentDate,
                        'created_by'     => auth()->id(),
                    ]);
                }
                $lastReceipt = $this->betReceipt->orderByDesc('id')->first();
                if ($lastReceipt && isset($lastReceipt->receipt_no)) {
                    $matches = [];
                    preg_match('/INV-(\d+)/', $lastReceipt->receipt_no, $matches);
                    $nextNumber = isset($matches[1]) ? ((int)$matches[1] + 1) : ($lastReceipt->id + 1);
                } else {
                    $nextNumber = $this->betReceipt->max('id') + 1;
                }
                $invoiceNumber = 'INV-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
                $betReceipt = $this->betReceipt->create([
                    'receipt_no'  => $invoiceNumber,
                    'user_id'     => $this->user->id ?? 0,
                    'date'        => now(),
                    'currency'    => 'USD',
                    'total_amount'=> $this->totalInvoice,
                    'commission'  => $this->totalInvoice - $this->totalDue,
                    'net_amount'  => $this->totalDue,
                    'compensate'  => 0
                ]);
            }

            foreach ($this->number as $key => $number) {
                if (!empty($number)) {
                    $has_spacial = $this->roll_parlay_check[$key] ? 1 : 0;
                    $betPackage = $this->betPackageConfiguration::where('package_id', '=', $this->user->package_id)
                        ->where(['bet_type' => $this->digit[$key], 'has_special' => $has_spacial])->first();
                    $rate = $betPackage?->rate / 100;
                    foreach ($this->schedules as $key_prov => $schedule) {
                        if ($this->province_body_check[$key_prov][$key] && $this->total_amount[$key] > 0) {
                            $betLimit = $this->validationBetLimitAmount($number, $key, $this->digit[$key], $schedule->id);
                            if ($betLimit) {
                                $this->dispatch('bet-saved', message: $betLimit, type: 'error');
                                return back();
                            }
                            $nowTime = Carbon::now();
                            $checkTimeClose = BetLotterySchedule::where('id', $schedule->id)
                                ->where('time_close', '<=', $nowTime->format('H:i:s'))
                                ->first();
                            if ($checkTimeClose) {
                                $this->dispatch('bet-saved', message: 'Close Time', type: 'warning');
                                return back();
                            }
                            $amountBet = $this->calculateAmountOutstanding($number, $key, $schedule['code'], 1);
                            $betAmountDupplicate = $BetModel::where('user_id', $this->user->id)
                                ->where('bet_schedule_id', $schedule->id)
                                ->where('number_format', $number)
                                ->where('digit_format', $this->digit[$key])
                                ->where('company_id', $schedule->company_id)
                                ->where('total_amount', $amountBet)
                                ->where('bet_package_config_id', $betPackage->id ?? 0)
                                ->where('bet_receipt_id', $betReceipt->id ?? null)
                                ->whereDate('bet_date', $this->currentDate)
                                ->first();
                            $lastAmount = $betAmountDupplicate ? $amountBet + 0.01 : $amountBet;
                            $betItem = [
                                'bet_receipt_id'       => $betReceipt->id,
                                'company_id'           => $schedule->company_id,
                                'user_id'              => $this->user->id ?? 0,
                                'bet_schedule_id'      => $schedule->id,
                                'bet_package_config_id'=> $betPackage->id ?? 0,
                                'number_format'        => $number,
                                'digit_format'         => $this->digit[$key],
                                'bet_date'             => $this->currentDate,
                                'total_amount'         => $lastAmount,
                            ];
                            $respone = $BetModel::create($betItem);
                            if ($respone) {
                                $isCreateBetSuccess = true;
                            }
                            $betNumber1 = [
                                'bet_id'             => $respone->id,
                                'original_number'    => $number,
                                'a_amount'           => (float)($this->a_amount[$key] ?? 0),
                                'b_amount'           => (float)($this->b_amount[$key] ?? 0),
                                'c_amount'           => (float)($this->c_amount[$key] ?? 0),
                                'd_amount'           => (float)($this->d_amount[$key] ?? 0),
                                'abcd_amount'        => (float)($this->abcd_amount[$key] ?? 0),
                                'roll_amount'        => (float)($this->roll_amount[$key] ?? 0),
                                'roll2_amount'       => (float)($this->roll2_amount[$key] ?? 0),
                                'roll_parlay_amount' => (float)($this->roll_parlay_amount[$key] ?? 0),
                                'a_check'            => $this->a_check[$key],
                                'b_check'            => $this->b_check[$key],
                                'c_check'            => $this->c_check[$key],
                                'd_check'            => $this->d_check[$key],
                                'abcd_check'         => $this->abcd_check[$key],
                                'roll_check'         => $this->roll_check[$key],
                                'roll2_check'        => $this->roll2_check[$key],
                                'roll_parlay_check'  => $this->roll_parlay_check[$key],
                            ];
                            $betTypes = [
                                'a'          => ['amount' => $this->a_amount[$key] ?? 0,          'check' => $this->a_check[$key]],
                                'b'          => ['amount' => $this->b_amount[$key] ?? 0,          'check' => $this->b_check[$key]],
                                'c'          => ['amount' => $this->c_amount[$key] ?? 0,          'check' => $this->c_check[$key]],
                                'd'          => ['amount' => $this->d_amount[$key] ?? 0,          'check' => $this->d_check[$key]],
                                'abcd'       => ['amount' => $this->abcd_amount[$key] ?? 0,       'check' => $this->abcd_check[$key]],
                                'roll'       => ['amount' => $this->roll_amount[$key] ?? 0,       'check' => $this->roll_check[$key]],
                                'roll2'      => ['amount' => $this->roll2_amount[$key] ?? 0,      'check' => $this->roll2_check[$key]],
                                'roll_parlay'=> ['amount' => $this->roll_parlay_amount[$key] ?? 0,'check' => $this->roll_parlay_check[$key]],
                            ];

                            if (strpos($number, '#') !== false) {
                                $multiplierHashtag = 0;
                                $countHashtag = substr_count($number, '#');
                                if ($schedule->code == "HN") {
                                    $multiplierHashtag = match ($countHashtag) {
                                        1 => MultiplierHashtagHNKHEnum::one,
                                        2 => MultiplierHashtagHNKHEnum::two,
                                        3 => MultiplierHashtagHNKHEnum::three,
                                        default => 1
                                    };
                                } else {
                                    $multiplierHashtag = match ($countHashtag) {
                                        1 => MultiplierHashtagKHEnum::one,
                                        2 => MultiplierHashtagKHEnum::two,
                                        3 => MultiplierHashtagKHEnum::three,
                                        default => 1
                                    };
                                }
                                if ($this->roll_parlay_check[$key] == true) {
                                    $multiplierOneHashtag = $schedule->code == "HN" ? MultiplierHashtagHNKHEnum::one : MultiplierHashtagKHEnum::one;
                                    $parts = $this->generateSharpNumber($number);
                                    foreach ($parts as $part) {
                                        $betNumber2 = [
                                            'generated_number' => $part,
                                            'digit_length'     => $this->digit[$key],
                                            'total_amount'     => $this->roll_parlay_amount[$key] * $multiplierOneHashtag,
                                        ];
                                        $BetNumberModel::create(array_merge($betNumber1, $betNumber2));
                                    }
                                } else {
                                    $betNumber2 = [
                                        'generated_number' => $number,
                                        'digit_length'     => $this->digit[$key],
                                        'total_amount'     => $this->roll_parlay_amount[$key] * $multiplierHashtag,
                                    ];
                                    $BetNumberModel::create(array_merge($betNumber1, $betNumber2));
                                }
                            } elseif (strpos($number, '*') !== false) {
                                $num = trim($number, '*');
                                $numberLength = \strlen($num) + 1;
                                $total_amount = 0;
                                foreach ($betTypes as $type => $info) {
                                    if ($info['amount'] > 0) {
                                        $total_amount += $this->calculateBetNumberTotalAmount($numberLength, $info['amount'], $schedule->code, $type);
                                    }
                                }
                                for ($i = 0; $i < 10; $i++) {
                                    $genNumber = str_starts_with($number, '*') ? $i . $num : $num . $i;
                                    $betNumber2 = [
                                        'generated_number' => $genNumber,
                                        'digit_length'     => \strlen($genNumber),
                                        'total_amount'     => $total_amount,
                                    ];
                                    $BetNumberModel::create(array_merge($betNumber1, $betNumber2));
                                }
                            } else {
                                $numberLength = \strlen($number);
                                $hasCheck = collect($betTypes)->contains(fn($info) => $info['check'] > 0);
                                $combinations = $hasCheck
                                    ? $this->generateUniquePermutations(str_split($number))
                                    : [$number];
                                $total_amount = 0;
                                foreach ($betTypes as $type => $info) {
                                    if ($info['amount'] > 0) {
                                        $total_amount += $this->calculateBetNumberTotalAmount($numberLength, $info['amount'], $schedule->code, $type);
                                    }
                                }
                                foreach ($combinations as $combo) {
                                    $betNumber2 = [
                                        'generated_number' => $combo,
                                        'digit_length'     => \strlen($combo),
                                        'total_amount'     => $total_amount,
                                    ];
                                    $BetNumberModel::create(array_merge($betNumber1, $betNumber2));
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();
            if ($isCreateBetSuccess) {
                $this->totalOutstanding += $this->totalDue;
                $this->handleReset();
                $this->dispatch('bet-saved', message: 'Bet saved successfully!');
                return redirect()->to('lotto_kh_usd/bet_receipt/' . $betReceipt->receipt_no);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('KH USD bet save failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'user_id' => auth()->id()]);
            $this->dispatch('bet-saved', message: 'Save failed: ' . $e->getMessage(), type: 'error');
        }
    }

    private function validationBetLimitAmount($number, $key, $digit, $scheduleId)
    {
        $vBetTable = 'bet_kh_usd';
        $vBetNumberTable = 'bet_number_kh_usd';

        $betTypes = [
            'a'          => ['amount' => $this->a_amount[$key] ?? 0,          'check' => $this->a_check[$key]],
            'b'          => ['amount' => $this->b_amount[$key] ?? 0,          'check' => $this->b_check[$key]],
            'c'          => ['amount' => $this->c_amount[$key] ?? 0,          'check' => $this->c_check[$key]],
            'd'          => ['amount' => $this->d_amount[$key] ?? 0,          'check' => $this->d_check[$key]],
            'abcd'       => ['amount' => $this->abcd_amount[$key] ?? 0,       'check' => $this->abcd_check[$key]],
            'roll'       => ['amount' => $this->roll_amount[$key] ?? 0,       'check' => $this->roll_check[$key]],
            'roll2'      => ['amount' => $this->roll2_amount[$key] ?? 0,      'check' => $this->roll2_check[$key]],
            'roll_parlay'=> ['amount' => $this->roll_parlay_amount[$key] ?? 0,'check' => $this->roll_parlay_check[$key]],
        ];
        if (strpos($number, '#') !== false) {
            if ($this->roll_parlay_amount[$key] > 0) {
                $digitKey = ($digit === 'RP3' && $this->roll_parlay_check[$key] != 1) ? 'RP3' : 'RP2';
                $checkBetLimit = UserBetLimit::where('user_id', $this->user->id)
                    ->where('digit_key', $digitKey)
                    ->first();
                $amountLimit = DB::table($vBetTable)
                    ->join($vBetNumberTable, "$vBetTable.id", '=', "$vBetNumberTable.bet_id")
                    ->where("$vBetNumberTable.generated_number", $number)
                    ->where("$vBetNumberTable.digit_length", $digit)
                    ->where("$vBetTable.bet_schedule_id", $scheduleId)
                    ->whereDate("$vBetTable.bet_date", $this->currentDate)
                    ->selectRaw("COALESCE(SUM($vBetNumberTable.roll_parlay_amount),0) as total")
                    ->value('total');
                if ($checkBetLimit) {
                    if ($this->roll_parlay_amount[$key] < $checkBetLimit->min_bet) {
                        return "Your bet amount is below the minimum limit ({$checkBetLimit->min_bet})";
                    }
                    if (($amountLimit + $this->roll_parlay_amount[$key]) > $checkBetLimit->max_bet) {
                        return "Your bet amount exceeds the maximum limit";
                    }
                }
            }
        } else {
            foreach ($betTypes as $info) {
                if ($info['amount'] > 0) {
                    $checkBetLimit = UserBetLimit::where('user_id', $this->user->id)
                        ->where('digit_key', $digit)
                        ->first();
                    $amountLimit = DB::table($vBetTable)
                        ->join($vBetNumberTable, "$vBetTable.id", '=', "$vBetNumberTable.bet_id")
                        ->where("$vBetNumberTable.generated_number", $number)
                        ->where("$vBetNumberTable.digit_length", intval($digit))
                        ->where("$vBetTable.bet_schedule_id", $scheduleId)
                        ->whereDate("$vBetTable.bet_date", $this->currentDate)
                        ->selectRaw("
                            COALESCE(SUM($vBetNumberTable.a_amount),0)
                            + COALESCE(SUM($vBetNumberTable.b_amount),0)
                            + COALESCE(SUM($vBetNumberTable.c_amount),0)
                            + COALESCE(SUM($vBetNumberTable.d_amount),0)
                            + COALESCE(SUM($vBetNumberTable.abcd_amount),0)
                            + COALESCE(SUM($vBetNumberTable.roll_amount),0)
                            + COALESCE(SUM($vBetNumberTable.roll2_amount),0)
                            + COALESCE(SUM($vBetNumberTable.roll_parlay_amount),0) as total
                        ")
                        ->value('total');
                    if ($checkBetLimit) {
                        if ($info['amount'] < $checkBetLimit->min_bet) {
                            return "Your bet amount is below the minimum limit ({$checkBetLimit->min_bet})";
                        }
                        if (($info['amount'] + $amountLimit) > $checkBetLimit->max_bet) {
                            return "Your bet amount exceeds the maximum limit";
                        }
                    }
                }
            }
        }
    }

    private function calculateAmountOutstanding($number, $key, $code, $rate)
    {
        $betTypes = [
            'a'          => ['amount' => $this->a_amount[$key] ?? 0,          'check' => $this->a_check[$key]],
            'b'          => ['amount' => $this->b_amount[$key] ?? 0,          'check' => $this->b_check[$key]],
            'c'          => ['amount' => $this->c_amount[$key] ?? 0,          'check' => $this->c_check[$key]],
            'd'          => ['amount' => $this->d_amount[$key] ?? 0,          'check' => $this->d_check[$key]],
            'abcd'       => ['amount' => $this->abcd_amount[$key] ?? 0,       'check' => $this->abcd_check[$key]],
            'roll'       => ['amount' => $this->roll_amount[$key] ?? 0,       'check' => $this->roll_check[$key]],
            'roll2'      => ['amount' => $this->roll2_amount[$key] ?? 0,      'check' => $this->roll2_check[$key]],
            'roll_parlay'=> ['amount' => $this->roll_parlay_amount[$key] ?? 0,'check' => $this->roll_parlay_check[$key]],
        ];

        $amount = 0;
        if (strpos($number, '#') !== false) {
            if ($this->roll_parlay_amount[$key] > 0) {
                $multiplier = 1;
                $permuLength = 1;
                $countHashtag = substr_count($number, '#');
                if ($this->roll_parlay_check[$key] == true && $countHashtag == 2) {
                    $permuLength = 2;
                }
                if ($code == "HN") {
                    $multiplier = match ($countHashtag) {
                        1 => MultiplierHashtagHNKHEnum::one,
                        2 => MultiplierHashtagHNKHEnum::two,
                        3 => MultiplierHashtagHNKHEnum::three,
                        default => 1
                    };
                } else {
                    $multiplier = match ($countHashtag) {
                        1 => MultiplierHashtagKHEnum::one,
                        2 => MultiplierHashtagKHEnum::two,
                        3 => MultiplierHashtagKHEnum::three,
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
                            'a'    => $length == 2 ? MultiplierHNKHEnum::A    : ($length == 3 ? MultiplierHNKHEnum::A_3D : 0),
                            'b'    => $length == 2 ? MultiplierHNKHEnum::B    : ($length == 3 ? MultiplierHNKHEnum::B_3D : 0),
                            'c'    => $length == 2 ? MultiplierHNKHEnum::C    : ($length == 3 ? MultiplierHNKHEnum::C_3D : 0),
                            'd'    => $length == 2 ? MultiplierHNKHEnum::D    : ($length == 3 ? MultiplierHNKHEnum::D_3D : 0),
                            'abcd' => $length == 2 ? MultiplierHNKHEnum::ABCD : ($length == 3 ? MultiplierHNKHEnum::ABCD_3D : 0),
                            'roll' => $length == 2 ? MultiplierHNKHEnum::ROLL : ($length == 3 ? MultiplierHNKHEnum::ROLL_3D : ($length == 4 ? MultiplierHNKHEnum::ROLL_4D : 0)),
                            'roll2' => match ($length) { 2 => MultiplierHNKHEnum::ROLL2, 3 => MultiplierHNKHEnum::ROLL2_3D, default => 0 },
                            default => 1,
                        };
                    } else {
                        $multiplier = match ($type) {
                            'a'    => $length == 2 ? MultiplierKHEnum::A    : ($length == 3 ? MultiplierKHEnum::A_3D : 0),
                            'b'    => $length == 2 ? MultiplierKHEnum::B    : ($length == 3 ? MultiplierKHEnum::B_3D : 0),
                            'c'    => $length == 2 ? MultiplierKHEnum::C    : ($length == 3 ? MultiplierKHEnum::C_3D : 0),
                            'd'    => $length == 2 ? MultiplierKHEnum::D    : ($length == 3 ? MultiplierKHEnum::D_3D : 0),
                            'abcd' => $length == 2 ? MultiplierKHEnum::ABCD : ($length == 3 ? MultiplierKHEnum::ABCD_3D : 0),
                            'roll' => $length == 2 ? MultiplierKHEnum::ROLL : ($length == 3 ? MultiplierKHEnum::ROLL_3D : ($length == 4 ? MultiplierKHEnum::ROLL_4D : 0)),
                            'roll2' => match ($length) { 2 => MultiplierKHEnum::ROLL2, 3 => MultiplierKHEnum::ROLL2_3D, default => 0 },
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
                    if ($info['check'] > 0) {
                        $permuLength = $this->permutationsLength[$key];
                    }
                    if ($code == "HN") {
                        $multiplier = match ($type) {
                            'a'    => $length == 2 ? MultiplierHNKHEnum::A    : ($length == 3 ? MultiplierHNKHEnum::A_3D : 0),
                            'b'    => $length == 2 ? MultiplierHNKHEnum::B    : ($length == 3 ? MultiplierHNKHEnum::B_3D : 0),
                            'c'    => $length == 2 ? MultiplierHNKHEnum::C    : ($length == 3 ? MultiplierHNKHEnum::C_3D : 0),
                            'd'    => $length == 2 ? MultiplierHNKHEnum::D    : ($length == 3 ? MultiplierHNKHEnum::D_3D : 0),
                            'abcd' => $length == 2 ? MultiplierHNKHEnum::ABCD : ($length == 3 ? MultiplierHNKHEnum::ABCD_3D : 0),
                            'roll' => $length == 2 ? MultiplierHNKHEnum::ROLL : ($length == 3 ? MultiplierHNKHEnum::ROLL_3D : ($length == 4 ? MultiplierHNKHEnum::ROLL_4D : 0)),
                            'roll2' => match ($length) { 2 => MultiplierHNKHEnum::ROLL2, 3 => MultiplierHNKHEnum::ROLL2_3D, default => 0 },
                            default => 1,
                        };
                    } else {
                        $multiplier = match ($type) {
                            'a'    => $length == 2 ? MultiplierKHEnum::A    : ($length == 3 ? MultiplierKHEnum::A_3D : 0),
                            'b'    => $length == 2 ? MultiplierKHEnum::B    : ($length == 3 ? MultiplierKHEnum::B_3D : 0),
                            'c'    => $length == 2 ? MultiplierKHEnum::C    : ($length == 3 ? MultiplierKHEnum::C_3D : 0),
                            'd'    => $length == 2 ? MultiplierKHEnum::D    : ($length == 3 ? MultiplierKHEnum::D_3D : 0),
                            'abcd' => $length == 2 ? MultiplierKHEnum::ABCD : ($length == 3 ? MultiplierKHEnum::ABCD_3D : 0),
                            'roll' => $length == 2 ? MultiplierKHEnum::ROLL : ($length == 3 ? MultiplierKHEnum::ROLL_3D : ($length == 4 ? MultiplierKHEnum::ROLL_4D : 0)),
                            'roll2' => match ($length) { 2 => MultiplierKHEnum::ROLL2, 3 => MultiplierKHEnum::ROLL2_3D, default => 0 },
                            default => 1,
                        };
                    }
                    $amount += $info['amount'] * $multiplier * $permuLength;
                }
            }
        }
        return ($amount * $rate);
    }

    private function calculateBetNumberTotalAmount($numberLength, $amount, $code, $type)
    {
        $multiplier = 0;
        if ($code == "HN") {
            $multiplier = match ($type) {
                'a'    => $numberLength == 2 ? MultiplierHNKHEnum::A    : ($numberLength == 3 ? MultiplierHNKHEnum::A_3D : 0),
                'b'    => $numberLength == 2 ? MultiplierHNKHEnum::B    : ($numberLength == 3 ? MultiplierHNKHEnum::B_3D : 0),
                'c'    => $numberLength == 2 ? MultiplierHNKHEnum::C    : ($numberLength == 3 ? MultiplierHNKHEnum::C_3D : 0),
                'd'    => $numberLength == 2 ? MultiplierHNKHEnum::D    : ($numberLength == 3 ? MultiplierHNKHEnum::D_3D : 0),
                'abcd' => $numberLength == 2 ? MultiplierHNKHEnum::ABCD : ($numberLength == 3 ? MultiplierHNKHEnum::ABCD_3D : 0),
                'roll' => $numberLength == 2 ? MultiplierHNKHEnum::ROLL : ($numberLength == 3 ? MultiplierHNKHEnum::ROLL_3D : ($numberLength == 4 ? MultiplierHNKHEnum::ROLL_4D : 0)),
                'roll2' => match ($numberLength) { 2 => MultiplierHNKHEnum::ROLL2, 3 => MultiplierHNKHEnum::ROLL2_3D, default => 0 },
                default => 1,
            };
        } else {
            $multiplier = match ($type) {
                'a'    => $numberLength == 2 ? MultiplierKHEnum::A    : ($numberLength == 3 ? MultiplierKHEnum::A_3D : 0),
                'b'    => $numberLength == 2 ? MultiplierKHEnum::B    : ($numberLength == 3 ? MultiplierKHEnum::B_3D : 0),
                'c'    => $numberLength == 2 ? MultiplierKHEnum::C    : ($numberLength == 3 ? MultiplierKHEnum::C_3D : 0),
                'd'    => $numberLength == 2 ? MultiplierKHEnum::D    : ($numberLength == 3 ? MultiplierKHEnum::D_3D : 0),
                'abcd' => $numberLength == 2 ? MultiplierKHEnum::ABCD : ($numberLength == 3 ? MultiplierKHEnum::ABCD_3D : 0),
                'roll' => $numberLength == 2 ? MultiplierKHEnum::ROLL : ($numberLength == 3 ? MultiplierKHEnum::ROLL_3D : ($numberLength == 4 ? MultiplierKHEnum::ROLL_4D : 0)),
                'roll2' => match ($numberLength) { 2 => MultiplierKHEnum::ROLL2, 3 => MultiplierKHEnum::ROLL2_3D, default => 0 },
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
                if (in_array($digit, $used)) continue;
                $used[] = $digit;
                $next = $current;
                $next[] = $digit;
                $nextRemaining = $remaining;
                unset($nextRemaining[$i]);
                $nextRemaining = array_values($nextRemaining);
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
            'number', 'totalInvoice', 'totalDue', 'invoices',
            'province_check', 'province_body_check',
            'a_amount', 'b_amount', 'c_amount', 'd_amount', 'abcd_amount',
            'roll_amount', 'roll2_amount', 'roll_parlay_amount',
            'total_amount', 'amountHN', 'amountNotHN',
            'a_check', 'b_check', 'c_check', 'd_check', 'abcd_check',
            'roll_check', 'roll2_check', 'roll_parlay_check',
        ];
        $this->reset($field);
        $this->initializeProperty();
    }

    public function handleCheckChanel($key, $name = "")
    {
        $num = $this->number[$key] ?? '';
        $lengthOfNum = strlen($num);
        if ($lengthOfNum === 0) return;

        $isAsterisk  = preg_match('/^\*\d+$|\d+\*$/', $num);
        $countHashtag = substr_count($num, '#');
        $countProvince = 0;
        foreach ($this->schedules as $kp => $schedule) {
            if ($this->province_body_check[$kp][$key] ?? false) $countProvince++;
        }
        if ($countProvince > 0) {
            $this->totalAmountNormalNumber($key, $lengthOfNum, $isAsterisk, $countHashtag);
        }
    }

    public function handleInputAmount($key)
    {
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
                        $this->totalAmountNormalNumber($key, $lengthOfNum, $isAsterisk, $countHashtag);
                    }
                    $this->addAmount($amount, $this->a_amount[$key] ?? 0,          $this->a_check[$key] ?? false,    "A",    $key);
                    $this->addAmount($amount, $this->b_amount[$key] ?? 0,          $this->b_check[$key] ?? false,    "B",    $key);
                    $this->addAmount($amount, $this->c_amount[$key] ?? 0,          $this->c_check[$key] ?? false,    "C",    $key);
                    $this->addAmount($amount, $this->d_amount[$key] ?? 0,          $this->d_check[$key] ?? false,    "D",    $key);
                    $this->addAmount($amount, $this->abcd_amount[$key] ?? 0,       $this->abcd_check[$key] ?? false, "ABCD", $key);
                    $this->addAmount($amount, $this->roll_amount[$key] ?? 0,       $this->roll_check[$key] ?? false, "R",    $key);
                    $this->addAmount($amount, $this->roll2_amount[$key] ?? 0,      $this->roll2_check[$key] ?? false,"R2",   $key);
                    $this->addAmount($amount, $this->roll_parlay_amount[$key] ?? 0,$this->roll_parlay_check[$key] ?? false, "RP", $key);
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

    private function totalAmountNormalNumber($key, $lengthNumber, $isAsterisk, $countHashtag): void
    {
        $this->totalProvisional = 0;
        $this->totalProvisionalHN = 0;
        foreach ($this->schedules as $keys => $schedule) {
            if ($this->province_body_check[$keys][$key]) {
                if ($schedule->code == "HN") {
                    if ($this->a_amount[$key] > 0) {
                        $aM = $lengthNumber == 2 ? MultiplierHNKHEnum::A : ($lengthNumber == 3 ? MultiplierHNKHEnum::A_3D : 0);
                        $this->totalProvisionalHN += $this->a_check[$key]
                            ? $this->a_amount[$key] * $aM * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->a_amount[$key] * $aM * 10 : $this->a_amount[$key] * $aM);
                    }
                    if ($this->b_amount[$key] > 0) {
                        $bM = $lengthNumber == 2 ? MultiplierHNKHEnum::B : ($lengthNumber == 3 ? MultiplierHNKHEnum::B_3D : 0);
                        $this->totalProvisionalHN += $this->b_check[$key]
                            ? $this->b_amount[$key] * $bM * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->b_amount[$key] * $bM * 10 : $this->b_amount[$key] * $bM);
                    }
                    if ($this->c_amount[$key] > 0) {
                        $cM = $lengthNumber == 2 ? MultiplierHNKHEnum::C : ($lengthNumber == 3 ? MultiplierHNKHEnum::C_3D : 0);
                        $this->totalProvisionalHN += $this->c_check[$key]
                            ? $this->c_amount[$key] * $cM * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->c_amount[$key] * $cM * 10 : $this->c_amount[$key] * $cM);
                    }
                    if ($this->d_amount[$key] > 0) {
                        $dM = $lengthNumber == 2 ? MultiplierHNKHEnum::D : ($lengthNumber == 3 ? MultiplierHNKHEnum::D_3D : 0);
                        $this->totalProvisionalHN += $this->d_check[$key]
                            ? $this->d_amount[$key] * $dM * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->d_amount[$key] * $dM * 10 : $this->d_amount[$key] * $dM);
                    }
                    if ($this->abcd_amount[$key] > 0) {
                        $ab = match ($lengthNumber) { 2 => MultiplierHNKHEnum::ABCD, 3 => MultiplierHNKHEnum::ABCD_3D, default => 1 };
                        $this->totalProvisionalHN += $this->abcd_check[$key]
                            ? $this->abcd_amount[$key] * $ab * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->abcd_amount[$key] * $ab * 10 : $this->abcd_amount[$key] * $ab);
                    }
                    if ($this->roll_amount[$key] > 0) {
                        $roll = match ($lengthNumber) { 2 => MultiplierHNKHEnum::ROLL, 3 => MultiplierHNKHEnum::ROLL_3D, 4 => MultiplierHNKHEnum::ROLL_4D, default => 1 };
                        $this->totalProvisionalHN += $this->roll_check[$key]
                            ? $this->roll_amount[$key] * $roll * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->roll_amount[$key] * $roll * 10 : $this->roll_amount[$key] * $roll);
                    }
                    if ($this->roll2_amount[$key] > 0) {
                        $r2M = match ($lengthNumber) { 2 => MultiplierHNKHEnum::ROLL2, 3 => MultiplierHNKHEnum::ROLL2_3D, default => 0 };
                        $this->totalProvisionalHN += $this->roll2_check[$key]
                            ? $this->roll2_amount[$key] * $r2M * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->roll2_amount[$key] * $r2M * 10 : $this->roll2_amount[$key] * $r2M);
                    }
                    if ($this->roll_parlay_amount[$key] > 0) {
                        $value = match ($countHashtag) { 1 => MultiplierHashtagHNKHEnum::one, 2 => MultiplierHashtagHNKHEnum::two, 3 => MultiplierHashtagHNKHEnum::three, default => 1 };
                        $this->totalProvisionalHN += $this->roll_parlay_check[$key]
                            ? ($countHashtag == 2 ? $this->roll_parlay_amount[$key] * $value * $countHashtag : $this->roll_parlay_amount[$key] * $value)
                            : $this->roll_parlay_amount[$key] * $value;
                    }
                } else {
                    if ($this->a_amount[$key] > 0) {
                        $aM = $lengthNumber == 2 ? MultiplierKHEnum::A : ($lengthNumber == 3 ? MultiplierKHEnum::A_3D : 0);
                        $this->totalProvisional += $this->a_check[$key]
                            ? $this->a_amount[$key] * $aM * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->a_amount[$key] * $aM * 10 : $this->a_amount[$key] * $aM);
                    }
                    if ($this->b_amount[$key] > 0) {
                        $bM = $lengthNumber == 2 ? MultiplierKHEnum::B : ($lengthNumber == 3 ? MultiplierKHEnum::B_3D : 0);
                        $this->totalProvisional += $this->b_check[$key]
                            ? $this->b_amount[$key] * $bM * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->b_amount[$key] * $bM * 10 : $this->b_amount[$key] * $bM);
                    }
                    if ($this->c_amount[$key] > 0) {
                        $cM = $lengthNumber == 2 ? MultiplierKHEnum::C : ($lengthNumber == 3 ? MultiplierKHEnum::C_3D : 0);
                        $this->totalProvisional += $this->c_check[$key]
                            ? $this->c_amount[$key] * $cM * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->c_amount[$key] * $cM * 10 : $this->c_amount[$key] * $cM);
                    }
                    if ($this->d_amount[$key] > 0) {
                        $dM = $lengthNumber == 2 ? MultiplierKHEnum::D : ($lengthNumber == 3 ? MultiplierKHEnum::D_3D : 0);
                        $this->totalProvisional += $this->d_check[$key]
                            ? $this->d_amount[$key] * $dM * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->d_amount[$key] * $dM * 10 : $this->d_amount[$key] * $dM);
                    }
                    if ($this->abcd_amount[$key] > 0) {
                        $this->totalProvisional += $this->abcd_check[$key]
                            ? $this->abcd_amount[$key] * MultiplierKHEnum::ABCD * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->abcd_amount[$key] * MultiplierKHEnum::ABCD * 10 : $this->abcd_amount[$key] * MultiplierKHEnum::ABCD);
                    }
                    if ($this->roll_amount[$key] > 0) {
                        $roll = match ($lengthNumber) { 2 => MultiplierKHEnum::ROLL, 3 => MultiplierKHEnum::ROLL_3D, 4 => MultiplierKHEnum::ROLL_4D, default => 1 };
                        $this->totalProvisional += $this->roll_check[$key]
                            ? $this->roll_amount[$key] * $roll * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->roll_amount[$key] * $roll * 10 : $this->roll_amount[$key] * $roll);
                    }
                    if ($this->roll2_amount[$key] > 0) {
                        $r2M = match ($lengthNumber) { 2 => MultiplierKHEnum::ROLL2, 3 => MultiplierKHEnum::ROLL2_3D, default => 0 };
                        $this->totalProvisional += $this->roll2_check[$key]
                            ? $this->roll2_amount[$key] * $r2M * $this->permutationsLength[$key]
                            : ($isAsterisk ? $this->roll2_amount[$key] * $r2M * 10 : $this->roll2_amount[$key] * $r2M);
                    }
                    if ($this->roll_parlay_amount[$key] > 0) {
                        $value = match ($countHashtag) { 1 => MultiplierHashtagKHEnum::one, 2 => MultiplierHashtagKHEnum::two, 3 => MultiplierHashtagKHEnum::three, default => 1 };
                        $this->totalProvisional += $this->roll_parlay_check[$key]
                            ? ($countHashtag == 2 ? $this->roll_parlay_amount[$key] * $value * $countHashtag : $this->roll_parlay_amount[$key] * $value)
                            : $this->roll_parlay_amount[$key] * $value;
                    }
                }
            }
        }

        $this->amountHN[$key] = $this->totalProvisionalHN;
        $this->amountNotHN[$key] = $this->totalProvisional;
        $this->total_amount[$key] = $this->totalProvisional + $this->totalProvisionalHN;
        $this->totalInvoice = 0;
        $this->totalDue = 0;
        foreach ($this->total_amount as $key => $total) {
            $this->totalInvoice += $total;
            $this->totalDue += $total * intval($this->packageRate[$key]) / 100;
        }
    }
}
