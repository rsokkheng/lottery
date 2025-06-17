<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBetLimit extends Model
{
    use HasFactory;
    protected $table = 'user_bet_limits';
    protected $fillable = [
        'user_id',
        'digit_key',
        'min_bet',
        'max_bet',
       
    ];

    // Optional: relationship to User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

