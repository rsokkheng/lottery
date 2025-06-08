<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BetWinningUSD extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bet_winning_usd';


    public function betsUSD(): BelongsTo
    {
        return $this->belongsTo(BetUSD::class, 'bet_id', 'id');
    }

    public function betReceiptUSD(){
        return $this->belongsTo(BetReceiptUSD::class, 'bet_receipt_id','id');
    }

    public function betWinningRecordUSD()
    {
        return $this->hasMany(BetWinningRecordUSD::class, 'bet_winning_id', 'id');
    }
}
