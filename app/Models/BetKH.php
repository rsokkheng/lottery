<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BetKH extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'bet_kh';

    public function betNumberKH(): HasMany
    {
        return $this->hasMany(BetNumberKH::class, 'bet_id', 'id');
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

    public function beReceiptKH(): BelongsTo
    {
        return $this->belongsTo(BetReceiptKH::class, 'bet_receipt_id');
    }

    public function betWinningKH(): HasMany
    {
        return $this->hasMany(BetWinningKH::class, 'bet_id', 'id');
    }
}
