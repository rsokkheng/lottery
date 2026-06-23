<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetKHUSD extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bet_kh_usd';

    public function betNumberKHUSD(): HasMany
    {
        return $this->hasMany(BetNumberKHUSD::class, 'bet_id', 'id');
    }

    public function betLotterySchedule(): BelongsTo
    {
        return $this->belongsTo(BetLotterySchedule::class, 'bet_schedule_id');
    }

    public function bePackageConfig(): BelongsTo
    {
        return $this->belongsTo(BetLotteryPackageConfiguration::class, 'bet_package_config_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function beReceiptKHUSD(): BelongsTo
    {
        return $this->belongsTo(BetReceiptKHUSD::class, 'bet_receipt_id');
    }

    public function betWinningKHUSD(): HasMany
    {
        return $this->hasMany(BetWinningKHUSD::class, 'bet_id', 'id');
    }

    public function beReceiptKH(): BelongsTo { return $this->beReceiptKHUSD(); }
    public function betNumberKH(): HasMany { return $this->betNumberKHUSD(); }
}
