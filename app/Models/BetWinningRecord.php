<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetWinningRecord extends Model
{
    use HasFactory;
    protected $table = 'bet_winning_records';

    public function betLotteryResult(): BelongsTo
    {
        return $this->belongsTo(LotteryResult::class,'result_id','result_id');
    }
}
