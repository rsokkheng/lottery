<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryResult extends Model
{
    use HasFactory;
    protected $table      = 'bet_lottery_results';
    protected $primaryKey = 'result_id';
    protected $guarded    = [];

    public function betSchedule(){
        return $this->belongsTo(LotterySchedule::class, 'lottery_schedule_id', 'id');
    }

}
