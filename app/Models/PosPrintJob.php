<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosPrintJob extends Model
{
    protected $table = 'pos_print_jobs';
    protected $guarded = '';

    protected $casts = [
        'is_reprint' => 'boolean',
        'printed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function receiptUrl(): string
    {
        $prefix = $this->currency === 'USD' ? 'lotto_usd' : 'lotto_vn';

        return url($prefix . '/bet_receipt/' . rawurlencode($this->receipt_no)) . '?' . http_build_query([
            'station' => 1,
            'reprint' => $this->is_reprint ? 1 : 0,
        ]);
    }
}
