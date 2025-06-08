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
        'name_user',
        'beginning',
        'net_lose',
        'net_win',
        'deposit',
        'withdraw',
        'adjustment',
        'balance',
        'outstanding',
        'report_date',
    ];

    // Optional: relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

