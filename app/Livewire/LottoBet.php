<?php

namespace App\Livewire;

use Livewire\Component;

class LottoBet extends Component
{
    public $number = null;
    public $digit = null;
    public $enableChanelA = false;
    public $enableChanelB = false;
    public $enableChanelAB = false;
    public $enableChanelRoll = false;
    public $enableChanelRoll7 = false;
    public $enableChanelRollParlay = false;

    public function render()
    {
        return view('livewire.lotto-bet');
    }
   
    public function handleInputNumber()
{
    $normalizedNumber = str_replace(' ', '', (string)$this->number);

    // Validate input based on specific rules
    if ($this->isInvalidInput($normalizedNumber)) {
        $this->digit = null;
        $this->enableChanelA = false;
        $this->enableChanelB = false;
        $this->enableChanelAB = false;
        $this->enableChanelRoll = false;
        $this->enableChanelRoll7 = false;
        return; // Stop processing invalid input
    }

    if (strpos($normalizedNumber, '#') !== false) {
        // Handle complex bets with '#' separator
        $parts = explode('#', $normalizedNumber);
        $length = count($parts);

        $this->digit = "RP" . $length;

        if ($length >= 2 && $length <= 4) {
            $this->enableChanelA = true;
            $this->enableChanelB = true;
            $this->enableChanelAB = true;
            $this->enableChanelRoll = true;
            $this->enableChanelRoll7 = false;
        } else {
            $this->digit = null;
            $this->enableChanelA = false;
            $this->enableChanelB = false;
            $this->enableChanelAB = false;
            $this->enableChanelRoll = false;
            $this->enableChanelRoll7 = false;
        }
    } else {
        // Handle simple bets without '#' separator
        $this->handleSimpleBet($normalizedNumber);
    }
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

public function handleSimpleBet($number)
{
    // Handle simple bets (2D, 3D, 4D)
    $length = strlen($number);

    if ($length == 2) {
        $this->digit = "2D";
        $this->enableChanelA = true;
        $this->enableChanelB = true;
        $this->enableChanelAB = true;
        $this->enableChanelRoll = true;
        $this->enableChanelRoll7 = false;
    } elseif ($length == 3) {
        $this->digit = "3D";
        $this->enableChanelA = false;
        $this->enableChanelB = false;
        $this->enableChanelAB = true;
        $this->enableChanelRoll = true;
        $this->enableChanelRoll7 = true;
    } elseif ($length == 4) {
        $this->digit = "4D";
        $this->enableChanelA = false;
        $this->enableChanelB = false;
        $this->enableChanelAB = false;
        $this->enableChanelRoll = true;
        $this->enableChanelRoll7 = false;
    } else {
        $this->digit = null;
        $this->enableChanelA = false;
        $this->enableChanelB = false;
        $this->enableChanelAB = false;
        $this->enableChanelRoll = false;
        $this->enableChanelRoll7 = false;
    }
}


}
