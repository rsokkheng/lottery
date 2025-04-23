<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BetUserWallet extends Model
{
    use HasFactory;
    protected $table = 'bet_user_wallets';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
