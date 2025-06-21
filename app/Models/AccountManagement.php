<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountManagement extends Model
{
    use HasFactory;

    protected $table = 'account_management';

    protected $fillable = [
        'user_id',
        'name_user',
        'available_credit',
        'bet_credit',
        'cash_balance',
        'currency',
        'created_by',
        'updated_by'
    ];

    // 🔁 Relationship: belongs to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
