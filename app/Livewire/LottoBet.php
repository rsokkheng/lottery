<?php

namespace App\Livewire;

use App\Enums\MultiplierEnum;
use App\Enums\MultiplierHNEnum;
use App\Models\Bet;
use App\Models\BetLotteryPackageConfiguration;
use App\Models\BetLotterySchedule;
use App\Models\BetNumber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class LottoBet extends Component
{
    protected $betModel;
    protected $betLotteryScheduleModel;
    public $betPackageConfiguration;

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
    public $roll_parlay_amount = [];
    public $total_amount = [];

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

    public function mount(
        Bet                            $betModel,
        BetLotterySchedule             $betLotteryScheduleModel,
        BetLotteryPackageConfiguration $betPackageConfiguration
    )
    {
        // Initialization logic if needed
        $this->betLotteryScheduleModel = $betLotteryScheduleModel;
        $this->betModel = $betModel;
        $this->betPackageConfiguration = $betPackageConfiguration;

        $this->currentDate = Carbon::now()->format('Y-m-d');
        $this->currentDay = Carbon::now()->format('l');
        $this->currentTime = Carbon::now()->format('H:i:s');
        $this->user = Auth::user();

        $this->schedules = $this->betLotteryScheduleModel
            ->where('draw_day', '=', $this->currentDay)
            ->where('time_close', '>=', $this->currentTime)
            ->orderBy('time_close', 'asc')
            ->get(['id', 'code']);

        $this->timeClose = $this->betLotteryScheduleModel
            ->where('draw_day', '=', $this->currentDay)
            ->where('time_close', '>=', $this->currentTime)
            ->orderBy('time_close', 'asc')
            ->get(['id', 'code', 'time_close']);


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
        // $this->number = array_fill(0, $this->totalRow, null);
        $this->digit = array_fill(0, $this->totalRow, "");
        $this->total_amount = array_fill(0, $this->totalRow, 0);
        $this->permutationsLength = array_fill(0, $this->totalRow, 0);
        $this->packageRate = array_fill(0, $this->totalRow, 0);
    }


    public function render()
    {
        return view('livewire.lotto-bet');
    }

    public function handleProvinceCheck($index)
    {

        if (isset($this->province_check[$index]) && $this->province_check[$index]) {
            $this->province_body_check[$index] = array_fill(0, $this->totalRow, true);
        } else {
            $this->province_body_check[$index] = array_fill(0, $this->totalRow, false);
        }
    }

    public function handleProvinceBodyCheck($key_sch, $key_num)
    {

        if ($this->province_body_check[$key_sch][$key_num]) {
            $this->province_body_check[$key_sch][$key_num] = true;
        } else {
            $this->province_body_check[$key_sch][$key_num] = false;
        }
    }

    public function handleInputNumber()
    {
        foreach ($this->number as $key => $value) {
            $this->number[$key] = str_replace(' ', '', (string)$value);
            $normalizedNumber = $this->number[$key];
            if ($this->isInvalidInput($normalizedNumber)) {
                // $this->resetChanelValues();
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

        if ($length === 4 && count(array_unique($parts)) === 1) {
            $this->digit[$key] = '-';
            $this->checkRollParlay[$key] = false;
            return;
        }

        $this->enableChanelRollParlay[$key] = true;
        if ($length >= 2 && $length <= 4) {
            $this->digit[$key] = "RP" . $length;
            $this->setBetTypeForComplex($key);
        } else {
            $this->digit[$key] = '-';
            $this->checkRollParlay[$key] = false;
            $this->resetChanelValues();
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
        $this->enableChanelRollParlay[$key] = true;
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
    }

    private function resetChanelValues()
    {
        $this->enableChanelA = [];
        $this->enableChanelB = [];
        $this->enableChanelAB = [];
        $this->enableChanelRoll = [];
        $this->enableChanelRoll7 = [];
        $this->enableChanelRollParlay = [];
    }

    public function handleSave()
    {

        foreach ($this->number as $key => $value) {
            if (!empty($value)) {
                $betPackageConId = $this->betPackageConfiguration::where('bet_type', '=', $this->digit[$key])->pluck('id')->first();
                foreach ($this->schedules as $key_prov => $schedule) {
                    if ($this->province_body_check[$key_prov][$key]) {
                        //insert bet 
                        $betItem = [
                            'user_id' => $this->user->id ?? 0,
                            'bet_schedule_id' => $schedule->id,
                            'bet_package_config_id' => $betPackageConId,
                            'number_format' => $value,
                            'digit_format' => $this->digit[$key],
                            'bet_date' => $this->currentDate,
                            'total_amount' => $this->total_amount,
                        ];

                        $respone = Bet::create($betItem);

                        //insert bet number
                        $betNumber1 = [
                            'bet_id' => $respone->id,
                            'original_number' => $value,
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

                        if (strpos($value, '#') !== false) {
                            $parts = explode('#', $value);
                            foreach ($parts as $part) {
                                $betNumber2 = [
                                    'generated_number' => $part,
                                    'digit_length' => strlen($part),
                                ];

                                $data = array_merge($betNumber1, $betNumber2);
                                BetNumber::create($data);
                            }
                        } else if (strpos($value, '*') !== false) {

                            $num = trim($value, '*');

                            for ($i = 0; $i < 10; $i++) {
                                $genNumber = str_starts_with($value, '*') ? $i . $num : $num . $i;
                                $betNumber2 = [
                                    'generated_number' => $genNumber,
                                    'digit_length' => strlen($genNumber),
                                ];

                                $data = array_merge($betNumber1, $betNumber2);
                                BetNumber::create($data);
                            }
                        } else {
                            $betNumber2 = [
                                'generated_number' => $value,
                                'digit_length' => strlen($value),
                            ];

                            $data = array_merge($betNumber1, $betNumber2);
                            BetNumber::create($data);
                        }
                    }
                }
            }
        }
    }

    public function handleCheckChanel($key, $name = "")
    {
    }

    public function handleInputAmount($key)
    {
    }

    public function updated($propertyName)
    {
        if (count($this->number) > 0 && count($this->province_body_check) > 0) {
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

                    if ($countProvince > 0) {
                        $this->totalAmountNormalNumber($key, $lengthOfNum, $countProvince, $isAsterisk);
                    }
                    $this->addAmount($amount, $this->b_amount[$key] ?? 0, $this->b_check[$key] ?? false, "B", $key);
                    $this->addAmount($amount, $this->a_amount[$key] ?? 0, $this->a_check[$key] ?? false, "A", $key);
                    $this->addAmount($amount, $this->ab_amount[$key] ?? 0, $this->ab_check[$key] ?? false, "AB", $key);
                    $this->addAmount($amount, $this->roll_amount[$key] ?? 0, $this->roll_check[$key] ?? false, "R", $key);
                    $this->addAmount($amount, $this->roll7_amount[$key] ?? 0, $this->roll7_check[$key] ?? false, "R7", $key);
                    $this->addAmount($amount, $this->roll_parlay_amount[$key] ?? 0, $this->roll_parlay_check[$key] ?? false, "RP", $key);

                    if (!empty($chanel) && !empty($amount)) {
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
    private function totalAmountNormalNumber($key, $lengthNumber, $countProvince, $isAsterisk): void
    {
        $this->totalProvisional = 0;
        foreach ($this->schedules as $keys => $schedule) {
            if ($this->province_body_check[$keys][$key]) {
                if ($schedule->code == "HN") {
                    if ($this->a_amount[$key] > 0) {
                        if ($this->a_check[$key]) {
                            $this->totalProvisional += $this->a_amount[$key] * MultiplierHNEnum::A * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisional += $this->a_amount[$key] * MultiplierHNEnum::A * 10;
                            } else {
                                $this->totalProvisional += $this->a_amount[$key] * MultiplierHNEnum::A;
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
                        $ab = match ($lengthNumber) {
                            2 => MultiplierHNEnum::AB,
                            3 => MultiplierHNEnum::AB_3D,
                            default => 1
                        };
                        if ($this->ab_check[$key]) {
                            $this->totalProvisional += $this->ab_amount[$key] * $ab * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisional += $this->ab_amount[$key] * $ab * 10;
                            } else {
                                $this->totalProvisional += $this->ab_amount[$key] * $ab;
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
                            $this->totalProvisional += $this->roll_amount[$key] * $roll * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisional += $this->roll_amount[$key] * $roll * 10;
                            } else {
                                $this->totalProvisional += $this->roll_amount[$key] * $roll;
                            }
                        }
                    }


                    if ($this->roll_parlay_amount[$key] > 0) {
                        if ($this->roll_parlay_check[$key]) {
                            $this->totalProvisional += $this->roll_parlay_amount[$key] * $this->permutationsLength[$key];
                        } else {
                                $this->totalProvisional += $this->roll_parlay_amount[$key];

                        }
                    }
                } else {
                    if ($this->a_amount[$key] > 0) {
                        if ($this->a_check[$key]) {
                            $this->totalProvisional += $this->a_amount[$key] * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisional += $this->a_amount[$key] * 10;
                            }else{
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
                            }
                            else{
                                $this->totalProvisional += $this->b_amount[$key];
                            }
                        }
                    }

                    if ($this->ab_amount[$key] > 0) {
                        if ($this->ab_check[$key]) {
                            $this->totalProvisional += ($this->ab_amount[$key] * MultiplierEnum::AB) * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisional += $this->ab_amount[$key] * MultiplierEnum::AB *10;
                            }
                            else{
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
                            }
                            else {
                                $this->totalProvisional += $this->roll_amount[$key] * $roll;
                            }
                        }
                    }

                    if ($this->roll7_amount[$key] > 0) {
                        if ($this->roll7_check[$key]) {
                            $this->totalProvisional += $this->roll7_amount[$key] * MultiplierEnum::ROLL7 * $this->permutationsLength[$key];
                        } else {
                            if ($isAsterisk) {
                                $this->totalProvisional += $this->roll7_amount[$key] * MultiplierEnum::ROLL7 * 10;
                            }
                            else{
                                $this->totalProvisional += $this->roll7_amount[$key] * MultiplierEnum::ROLL7;
                            }
                        }
                    }

                    if ($this->roll_parlay_amount[$key] > 0) {
                        if ($this->roll_parlay_check[$key]) {
                            $this->totalProvisional += $this->roll_parlay_amount[$key] * $this->permutationsLength[$key];
                        } else {
                            $this->totalProvisional += $this->roll_parlay_amount[$key];
                        }
                    }
                }
            }
        }

        $this->total_amount[$key] = $this->totalProvisional;
        // invoice
        $this->totalInvoice = 0;
        $this->totalDue = 0;
        foreach ($this->total_amount as $key => $total) {
            $this->totalInvoice += $total;
            $this->totalDue += $total * intval($this->packageRate[$key]) / 100;
        }

    }

    private function totalAmountNormalForHN($key): void
    {

        if ($this->a_amount[$key] > 0) {
            if ($this->a_check[$key]) {
                $this->totalProvisional += $this->a_amount[$key] * $this->permutationsLength[$key];
            } else {
                $this->totalProvisional += $this->a_amount[$key];
            }
        }

        if ($this->b_amount[$key] > 0) {
            if ($this->b_check[$key]) {
                $this->totalProvisional += $this->b_amount[$key] * $this->permutationsLength[$key];
            } else {
                $this->totalProvisional += $this->b_amount[$key];
            }
        }

        if ($this->ab_amount[$key] > 0) {
            if ($this->ab_check[$key]) {
                $this->totalProvisional += ($this->ab_amount[$key] * MultiplierEnum::AB) * $this->permutationsLength[$key];
            } else {
                $this->totalProvisional += $this->ab_amount[$key] * MultiplierEnum::AB;
            }
        }

        if ($this->roll_amount[$key] > 0) {
            if ($this->roll_check[$key]) {
                $this->totalProvisional += ($this->roll_amount[$key] * MultiplierEnum::ROLL) * $this->permutationsLength[$key];
            } else {
                $this->totalProvisional += $this->roll_amount[$key] * MultiplierEnum::ROLL;
            }
        }

        if ($this->roll7_amount[$key] > 0) {
            if ($this->roll7_check[$key]) {
                $this->totalProvisional += ($this->roll7_amount[$key] * MultiplierEnum::ROLL7) * $this->permutationsLength[$key];
            } else {
                $this->totalProvisional += $this->roll7_amount[$key] * MultiplierEnum::ROLL7;
            }
        }

        if ($this->roll_parlay_amount[$key] > 0) {
            if ($this->roll_parlay_check[$key]) {
                $this->totalProvisional += $this->roll_parlay_amount[$key] * $this->permutationsLength[$key];
            } else {
                $this->totalProvisional += $this->roll_parlay_amount[$key];
            }
        }

        $this->total_amount[$key] = $this->totalProvisional;
        // invoice
        $this->totalInvoice = 0;
        $this->totalDue = 0;
        foreach ($this->total_amount as $key => $total) {
            $this->totalInvoice += $total;
            $this->totalDue += $total * intval($this->packageRate[$key]) / 100;
        }

    }

    private function totalAmountAsteriskNumber($key, $lengthNumber, $countProvince): void
    {

    }

    private function totalAmountNumberSign(): void
    {

    }
}
