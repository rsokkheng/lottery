<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetReceiptKHUSD extends Model
{
    use HasFactory;
    protected $table = 'bet_receipt_kh_usd';
    protected $guarded = '';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function betsKHUSD(): HasMany
    {
        return $this->hasMany(BetKHUSD::class, 'bet_receipt_id', 'id');
    }

    public function betWinningKHUSD(): HasMany
    {
        return $this->hasMany(BetWinningKHUSD::class, 'bet_receipt_id', 'id');
    }

    public function betsKH(): HasMany { return $this->betsKHUSD(); }
    public function betWinningKH(): HasMany { return $this->betWinningKHUSD(); }
}
