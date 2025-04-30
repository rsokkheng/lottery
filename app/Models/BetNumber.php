<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetNumber extends Model
{
    use HasFactory;
    protected $guarded = '';

    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }
    public function betNumberWin()
    {
        return $this->hasOne(BetWinningRecord::class, 'bet_number_id', 'id');
    }
    
}
