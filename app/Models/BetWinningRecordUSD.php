<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetWinningRecordUSD extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bet_winning_record_usd';
    public function betLotteryResult(): BelongsTo
    {
        return $this->belongsTo(LotteryResult::class,'result_id','result_id');
    }

    public function betWinningUSD()
    {
        return $this->belongsTo(BetWinningUSD::class, 'bet_winning_id', 'id');
    }
}
