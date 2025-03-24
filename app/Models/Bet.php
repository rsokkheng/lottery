<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bet extends Model
{
    use HasFactory;
    protected $guarded = '';

    public function betNumber():HasMany
    {
        return $this->hasMany(BetNumber::class);
    }

    public function betLotterySchedule():BelongsTo
    {
        return $this->belongsTo(BetLotterySchedule::class, 'bet_schedule_id');
    }
    
    public function bePackageConfig(): BelongsTo
    {
        return $this->belongsTo(BetLotteryPackageConfiguration::class, 'bet_package_config_id');
    }
    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
