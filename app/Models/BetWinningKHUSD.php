<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetWinningKHUSD extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bet_winning_kh_usd';

    public function betsKHUSD(): BelongsTo
    {
        return $this->belongsTo(BetKHUSD::class, 'bet_id', 'id');
    }

    public function betReceiptKHUSD(): BelongsTo
    {
        return $this->belongsTo(BetReceiptKHUSD::class, 'bet_receipt_id', 'id');
    }

    public function betWinningRecordKHUSD(): HasMany
    {
        return $this->hasMany(BetWinningRecordKHUSD::class, 'bet_winning_id', 'id');
    }

    public function betsKH(): BelongsTo { return $this->betsKHUSD(); }
    public function betReceiptKH(): BelongsTo { return $this->betReceiptKHUSD(); }
}
