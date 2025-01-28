<?php
namespace App\Livewire;

use App\Models\BetLotterySchedule;
use Carbon\Carbon;
use Livewire\Component;

class LottoBet extends Component
{
    public $number = [];
    public $digit = [];
    public $chanelA = [];
    public $chanelB = [];
    public $chanelAB = [];
    public $chanelRoll = [];
    public $chanelRoll7 = [];
    public $chanelRollParlay = [];

    // check chanel
    public $checkA = [];
    public $checkB = [];
    public $checkAB = [];
    public $checkRoll = [];
    public $checkRoll7 = [];
    public $checkRollParlay = [];

    // check location
    public $location =[];

    public $totalAmount = 0;
    public $enableChanelA = [];
    public $enableChanelB = [];
    public $enableChanelAB = [];
    public $enableChanelRoll = [];
    public $enableChanelRoll7 = [];
    public $enableChanelRollParlay = [];

    public $province=[];
    public $currentDay;
    public $currentTime;
    public $betLotteryScheduleModel;


    public function mount()
    {
        // Initialization logic if needed
        $this->betLotteryScheduleModel = new BetLotterySchedule();
        $this->currentDay = Carbon::now()->format('l');
        $this->currentTime = Carbon::now()->format('H:i:s');
    }

    public function render()
    {

       $this->province= $this->betLotteryScheduleModel
           ->where('draw_day','=',$this->currentDay)
           ->where('draw_time','>=',$this->currentTime)
           ->get(['id','code']);

        return view('livewire.lotto-bet');
    }

    public function handleInputNumber()
    {
        dump($this->location);
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
                $this->setBetType($key, "2D", true, true, true, true, false);
                break;
            case 3:
                $this->setBetType($key, "3D", false, false, true, true, true);
                break;
            case 4:
                $this->setBetType($key, "4D", false, false, false, true, false);
                break;
            default:
                $this->resetBetType($key);
        }
    }

    private function handleComplexBet($normalizedNumber, $key)
    {
        // Split the normalized number based on the '#' character
        $parts = explode('#', $normalizedNumber);
        $length = count($parts);

        // Set bet type based on number of parts
        $this->digit[$key] = "RP" . $length;

        if ($length >= 2 && $length <= 4) {
            $this->setBetTypeForComplex($key);
        } else {
            $this->resetChanelValues(); // Reset if the number of parts is not valid
        }
    }

    private function setBetType($key, $digit, $enableA, $enableB, $enableAB, $enableRoll, $enableRoll7)
    {
        // Assign bet type and enable/disable channel values for a specific key
        $this->digit[$key] = $digit;
        $this->enableChanelA[$key] = $enableA;
        $this->enableChanelB[$key] = $enableB;
        $this->enableChanelAB[$key] = $enableAB;
        $this->enableChanelRoll[$key] = $enableRoll;
        $this->enableChanelRoll7[$key] = $enableRoll7;
    }

    private function setBetTypeForComplex($key)
    {
        // Enable channels for complex bets for the given index/key
        $this->enableChanelA[$key] = true;
        $this->enableChanelB[$key] = true;
        $this->enableChanelAB[$key] = true;
        $this->enableChanelRoll[$key] = true;
        $this->enableChanelRoll7[$key] = false;
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
        $this->enableChanelRoll7[$key] = false;
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
}
