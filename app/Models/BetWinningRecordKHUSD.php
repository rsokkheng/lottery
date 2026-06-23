<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetWinningRecordKHUSD extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bet_winning_record_kh_usd';

    public function betLotteryResult(): BelongsTo
    {
        return $this->belongsTo(LotteryResult::class, 'result_id', 'result_id');
    }

    public function betWinningKHUSD(): BelongsTo
    {
        return $this->belongsTo(BetWinningKHUSD::class, 'bet_winning_id', 'id');
    }

    public function betWinningKH(): BelongsTo { return $this->betWinningKHUSD(); }
}
