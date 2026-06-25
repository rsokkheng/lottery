<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerBetType extends Model
{
    protected $fillable = ['user_id', 'bet_system', 'currency'];

    const OPTIONS = [
        ['currency' => 'VND', 'label' => 'VND — Bet Vietnam & Khmer'],
        ['currency' => 'USD', 'label' => 'USD — Bet Vietnam & Khmer'],
    ];

    const CURRENCY_LABEL = [
        'VND' => 'Vietnamese Dong',
        'USD' => 'USD Dollar',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function label(): string
    {
        $system      = ucfirst($this->bet_system);
        $currencyLabel = self::CURRENCY_LABEL[$this->currency] ?? $this->currency;
        return "Bet {$system} · {$currencyLabel}";
    }
}
