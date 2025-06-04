<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetUSD extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bet_usd';
    public function betNumberUSD():HasMany
    {
        return $this->hasMany(BetNumberUSD::class, 'bet_id', 'id');
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
    public function beReceiptUSD(): BelongsTo
    {
        return $this->belongsTo(BetReceiptUSD::class, 'bet_receipt_id');
    }

    public function betWinningUSD(): HasMany
    {
        return $this->hasMany(BetWinningUSD::class, 'bet_id', 'id');
    }
}
