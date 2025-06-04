<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetReceiptUSD extends Model
{
    use HasFactory;
    protected $table = 'bet_receipt_usd';
    protected $guarded = '';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function betsUSD(): HasMany
    {
        return $this->hasMany(BetUSD::class, 'bet_receipt_id', 'id');
    }

    public function betWinningUSD(){
        return $this->hasMany(BetWinningUSD::class, 'bet_receipt_id', 'id');
    }
}
