<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BalanceReportOutstanding extends Model
{
    use HasFactory;
    protected $table = 'balance_report_outstandings';
    protected $fillable = [
        'user_id',
        'company_id',
        'amount',
        'date',
    ];

    // Optional: relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

