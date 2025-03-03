<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotterySchedule extends Model
{
    use HasFactory;
    protected $table = 'bet_lottery_schedules';
    protected $guarded = [];

    public function betResults()
    {
        return $this->hasMany(LotteryResult::class, 'lottery_schedule_id', 'id');
    }
}
