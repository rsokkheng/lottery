<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryResult extends Model
{
    use HasFactory;
    protected $table = 'bet_lottery_results';
    protected $guarded = [];

    public function winningRecords(){
        return $this->hasMany(BetWinningRecord::class, 'result_id','result_id');
    }

    public function betSchedule(){
        return $this->belongsTo(LotterySchedule::class, 'lottery_schedule_id', 'id');
    }

}
