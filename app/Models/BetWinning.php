<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetWinning extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bet_winning';


    public function bets(): BelongsTo
    {
        return $this->belongsTo(Bet::class, 'bet_id', 'id');
    }

    public function betReceipt(){
        return $this->belongsTo(BetReceipt::class, 'bet_receipt_id','id');
    }

    public function betWinningRecords()
    {
        return $this->hasMany(BetWinningRecord::class, 'bet_winning_id', 'id');
    }
}
