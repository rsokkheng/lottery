<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BalanceReport extends Model
{
    use HasFactory;
    protected $table = 'balance_reports';
    protected $fillable = [
        'user_id',
        'text',
        'name_user',
        'net_lose',
        'net_win',
        'deposit',
        'withdraw',
        'adjustment',
        'balance',
        'report_date',
        'created_by',
        'updated_by'

    ];

    // Optional: relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

