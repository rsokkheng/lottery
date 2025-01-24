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
        $length = strlen((string)$this->number);
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
