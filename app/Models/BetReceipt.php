<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetReceipt extends Model
{
    use HasFactory;
    protected $table = 'bet_receipts';
    protected $guarded = '';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function bets(){
        return $this->hasMany(Bet::class, 'bet_receipt_id','id');
    }
}
