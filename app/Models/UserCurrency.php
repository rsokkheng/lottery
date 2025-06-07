<?php

namespace App\Models;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserCurrency extends Model
{
    protected $fillable = ['user_id', 'currency'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

