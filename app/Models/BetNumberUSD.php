<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetNumberUSD extends Model
{
    use HasFactory;
    protected $table = 'bet_number_usd';
    protected $guarded = [];

    public function betUSD()
    {
        return $this->belongsTo(BetUSD::class, 'id','bet_id');
    }
    public function betNumberWinUSD()
    {
        return $this->hasOne(BetWinningRecordUSD::class, 'bet_number_id', 'id');
    }
}
