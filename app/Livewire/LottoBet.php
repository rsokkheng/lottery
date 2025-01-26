<?php

namespace App\Livewire;

use Livewire\Component;

class LottoBet extends Component
{
    public $number = [];
    public $digit = [];
    public $chanelA= [];
    public $chanelB= [];
    public $chanelAB= [];
    public $chanelRoll= [];
    public $chanelRoll7= [];
    public $chanelRollParlay= [];

//    check chanel
    public $checkA = [];
    public $checkB = [];
    public $checkAB =[];
    public $checkRoll = [];
    public $checkRoll7 = [];
    public $checkRollParlay = [];
//    check location
    public $checkHN = [];
    public $checkTP = [];
    public $checkLA = [];
    public $checkBP = [];
    public $checkHG = [];
    public $checkDNA = [];
    public $checkQNG = [];
    public $checkDNO = [];
    public $totalAmounnt = 0;
    public $enableChanelA = [];
    public $enableChanelB = [];
    public $enableChanelAB = [];
    public $enableChanelRoll = [];
    public $enableChanelRoll7 = [];
    public $enableChanelRollParlay = [];

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.lotto-bet');
    }

    public function handleInputNumber()
    {

        foreach ($this->number as $key => $value) {
            $this->number[$key] = str_replace(' ', '', (string)$value);

            $this->handleSimpleBet($this->number[$key], $key);
        }
        
//    $normalizedNumber = str_replace(' ', '', (string)$this->number);
//        // Validate input based on specific rules
//        if ($this->isInvalidInput($normalizedNumber)) {
//            $this->digit = null;
//            $this->enableChanelA = false;
//            $this->enableChanelB = false;
//            $this->enableChanelAB = false;
//            $this->enableChanelRoll = false;
//            $this->enableChanelRoll7 = false;
//            return; // Stop processing invalid input
//        }
//
//        if (strpos($normalizedNumber, '#') !== false) {
//            // Handle complex bets with '#' separator
//            $parts = explode('#', $normalizedNumber);
//            $length = count($parts);
//
//            $this->digit = "RP" . $length;
//
//            if ($length >= 2 && $length <= 4) {
//                $this->enableChanelA = true;
//                $this->enableChanelB = true;
//                $this->enableChanelAB = true;
//                $this->enableChanelRoll = true;
//                $this->enableChanelRoll7 = false;
//            } else {
//                $this->digit = null;
//                $this->enableChanelA = false;
//                $this->enableChanelB = false;
//                $this->enableChanelAB = false;
//                $this->enableChanelRoll = false;
//                $this->enableChanelRoll7 = false;
//            }
//        } else {
//            // Handle simple bets without '#' separator
//            $this->handleSimpleBet($normalizedNumber);
//        }
    }

    private function isInvalidInput($number)
    {
        // Check if the number is a single digit
        if (strlen($number) == 1 && is_numeric($number)) {
            return true;
        }

        // Check if the number is a five-digit number
        if (strlen($number) == 5 && ctype_digit($number)) {
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

    private function handleSimpleBet($number, $index)
    {
        // Handle simple bets (2D, 3D, 4D)
        $length = strlen($number);

        if ($length == 2) {
            $this->digit[$index] = "2D";
            $this->enableChanelA[$index] = true;
            $this->enableChanelB[$index] = true;
            $this->enableChanelAB[$index] = true;
            $this->enableChanelRoll[$index] = true;
            $this->enableChanelRoll7[$index] = false;
        } elseif ($length == 3) {
            $this->digit[$index] = "3D";
            $this->enableChanelA[$index] = false;
            $this->enableChanelB[$index] = false;
            $this->enableChanelAB[$index] = true;
            $this->enableChanelRoll[$index] = true;
            $this->enableChanelRoll7[$index] = true;
        } elseif ($length == 4) {
            $this->digit[$index] = "4D";
            $this->enableChanelA[$index] = false;
            $this->enableChanelB[$index] = false;
            $this->enableChanelAB[$index] = false;
            $this->enableChanelRoll[$index] = true;
            $this->enableChanelRoll7[$index] = false;
        } else {
            $this->digit[$index] = null;
            $this->enableChanelA[$index] = false;
            $this->enableChanelB[$index] = false;
            $this->enableChanelAB[$index] = false;
            $this->enableChanelRoll[$index] = false;
            $this->enableChanelRoll7[$index] = false;
        }
    }


}
