<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetNumber extends Model
{
    use HasFactory;
    protected $guarded = '';

    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }
    
}
