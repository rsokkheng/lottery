<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountVND extends Model
{
    protected $table = 'account_vnd';

    protected $fillable = [
        'user_id',
        'credit_balance',
        'record_status_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'credit_balance' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(CreditTransactionVND::class, 'user_id', 'user_id');
    }
}
