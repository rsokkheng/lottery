<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetWinningKH extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bet_winning_kh_vnd';

    public function betsKH(): BelongsTo
    {
        return $this->belongsTo(BetKH::class, 'bet_id', 'id');
    }

    public function betReceiptKH(): BelongsTo
    {
        return $this->belongsTo(BetReceiptKH::class, 'bet_receipt_id', 'id');
    }

    public function betWinningRecordKH()
    {
        return $this->hasMany(BetWinningRecordKH::class, 'bet_winning_id', 'id');
    }
}

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

    public function betWinningRecordKHUSD()
    {
        return $this->hasMany(BetWinningRecordKHUSD::class, 'bet_winning_id', 'id');
    }
}
