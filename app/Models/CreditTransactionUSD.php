<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditTransactionUSD extends Model
{
    protected $table = 'credit_transaction_usd';

    protected $fillable = [
        'user_id', 'type', 'amount',
        'balance_before', 'balance_after',
        'note', 'bet_date', 'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
