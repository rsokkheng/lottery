<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetWinningRecord extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bet_winning_records';
    public function betLotteryResult(): BelongsTo
    {
        return $this->belongsTo(LotteryResult::class,'result_id','result_id');
    }

    public function bets(): BelongsTo
    {
        return $this->belongsTo(Bet::class, 'bet_id', 'id');
    }

    public function betReceipt(){
        return $this->belongsTo(BetReceipt::class, 'receipt_id','id');
    }
}
