<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetReceipt extends Model
{
    use HasFactory;
    protected $table = 'bet_receipts';
    protected $guarded = '';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class);
    }
}
