<?php

namespace App\Livewire;

use App\Models\Bet;
use App\Models\BetLotterySchedule;
use App\Models\BetNumber;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class LottoBet extends Component
{
    protected $betModel;
    protected $betLotteryScheduleModel;

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
    public $total_amount = 0;

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

    public $province = [];
    public $currentDay;
    public $currentTime;


    public function mount(Bet $betModel, BetLotterySchedule $betLotteryScheduleModel)
    {
        // Initialization logic if needed
        $this->betLotteryScheduleModel = $betLotteryScheduleModel;
        $this->betModel = $betModel;
        $this->currentDay = Carbon::now()->format('l');
        $this->currentTime = Carbon::now()->format('H:i:s');

        $this->province = $this->betLotteryScheduleModel
            ->where('draw_day', '=', $this->currentDay)
            ->where('draw_time', '>=', $this->currentTime)
            ->get(['id', 'code']);


        foreach ($this->province as $key => $pro) {
            $this->province_body_check[$pro['id']] = array_fill(0, $this->totalRow, false);
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
    }


    public function render()
    {

        return view('livewire.lotto-bet');
    }

    public function handleProvinceCheck($index)
    {

        if (isset($this->province_check[$index]) && $this->province_check[$index]) {
            $this->province_body_check[$index] = array_fill(0, 15, true);
        } else {
            $this->province_body_check[$index] = array_fill(0, 15, false);
        }
    }

    public function handleInputNumber()
    {
        // Loop over each element in the $this->number array
        foreach ($this->number as $key => $value) {
            // Normalize each number by removing spaces
            $this->number[$key] = str_replace(' ', '', (string)$value);

            // Now, handle the normalized number for each element individually
            $normalizedNumber = $this->number[$key];

            // Validate the input number
            if ($this->isInvalidInput($normalizedNumber)) {
                $this->resetChanelValues(); // Reset all channel-related values on invalid input
                return; // Stop processing invalid input
            }

            if (strpos($normalizedNumber, '#') !== false) {
                // Handle complex bets with '#' separator
                $this->handleComplexBet($normalizedNumber, $key);
            } else {
                // Handle simple bets
                $this->handleSimpleBet($normalizedNumber, $key);
            }
        }
    }

    private function isInvalidInput($number)
    {
        // Check if the number is a single digit or five digits
        if (strlen($number) == 1 || (strlen($number) == 5 && ctype_digit($number))) {
            return true;
        }

        // Check for invalid complex bet patterns
        if (strpos($number, '#') !== false) {
            $parts = explode('#', $number);

            // Invalid if any part is empty or single-digit
            foreach ($parts as $part) {
                if ($part === '' || strlen($part) == 1) {
                    return true;
                }
            }

            // Invalid if the number of parts is greater than 4
            if (count($parts) > 4) {
                return true;
            }
        }

        return false;
    }

    private function handleSimpleBet($number, $key)
    {
        $length = strlen($number);

        // Handle bet based on length of the number (2D, 3D, or 4D)
        switch ($length) {
            case 2:
                $this->setBetType($key, "2D", true, true, true, true, false, false);
                break;
            case 3:
                $this->setBetType($key, "3D", false, false, true, true, true, false);
                break;
            case 4:
                $this->setBetType($key, "4D", false, false, false, true, false, false);
                break;
            default:
                $this->resetBetType($key);
        }
    }

    private function handleComplexBet($normalizedNumber, $key)
    {
        $parts = explode('#', $normalizedNumber);
        $length = count($parts); // Number of parts

        // Check if all four parts are identical
        if ($length === 4 && count(array_unique($parts)) === 1) {
            $this->digit[$key] = '-'; // Set to '-' if fully identical
            $this->checkRollParlay[$key] = false; // Uncheck the checkbox
            return;
        }

        // Ensure enableChanelRollParlay is set to true
        $this->enableChanelRollParlay[$key] = true;
        // Validate complex bet length (allow RP2, RP3, RP4)
        if ($length >= 2 && $length <= 4) {
            $this->digit[$key] = "RP" . $length;
            $this->setBetTypeForComplex($key);
        } else {
            $this->digit[$key] = '-'; // Invalid case
            $this->checkRollParlay[$key] = false; // Uncheck the checkbox
            $this->resetChanelValues();
        }
    }


    private function setBetType($key, $digit, $enableA, $enableB, $enableAB, $enableRoll, $enableRoll7, $chanelRollParlay)
    {
        // Assign bet type and enable/disable channel values for a specific key
        $this->digit[$key] = $digit;
        $this->enableChanelA[$key] = $enableA;
        $this->enableChanelB[$key] = $enableB;
        $this->enableChanelAB[$key] = $enableAB;
        $this->enableChanelRoll[$key] = $enableRoll;
        $this->enableChanelRoll7[$key] = $enableRoll7;
        $this->enableChanelRollParlay[$key] = $chanelRollParlay;
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
        // Reset all bet type values for a specific index/key
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
        // Reset all channel-related values to their default states
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
            $betItem = [
                'user_id' => '1',
                'bet_schedule_id' => 1,
                'bet_package_config_id' => 1,
                'number_format' => $value,
                'digit_format' => $this->digit[$key],
                'total_amount' => $this->total_amount,

            ];

            $respone = Bet::create($betItem);

            $betNumber1 = [
                'bet_id' => $respone->id,
                'original_number' => $value,
                'a_amount' => $this->a_amount[$key] ?? 0,
                'b_amount' => $this->b_amount[$key] ?? 0,
                'ab_amount' => $this->ab_amount[$key] ?? 0,
                'roll_amount' => $this->roll_amount[$key]   ?? 0,
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
