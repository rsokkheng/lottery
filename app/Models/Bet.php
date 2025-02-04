<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    use HasFactory;
    protected $guarded = '';

    public function betNumber()
    {
        return $this->hasMany(BetNumber::class);
    }

    public function betLotterySchedule()
    {
        return $this->belongsTo(BetLotterySchedule::class, 'bet_schedule_id');
    }
    
    public function betPackageConfiguration()
    {
        return $this->belongsTo(BetPackageConfiguration::class, 'bet_package_config_id');
    }
}
