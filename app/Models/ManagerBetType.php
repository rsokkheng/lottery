<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerBetType extends Model
{
    protected $fillable = ['user_id', 'bet_system', 'currency'];

    const OPTIONS = [
        ['bet_system' => 'vietnam', 'currency' => 'VND', 'label' => 'Bet Vietnam · Vietnamese Dong'],
        ['bet_system' => 'vietnam', 'currency' => 'USD', 'label' => 'Bet Vietnam · USD Dollar'],
        ['bet_system' => 'khmer',   'currency' => 'VND', 'label' => 'Bet Khmer · Vietnamese Dong'],
        ['bet_system' => 'khmer',   'currency' => 'USD', 'label' => 'Bet Khmer · USD Dollar'],
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
