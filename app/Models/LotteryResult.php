<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryResult extends Model
{
    use HasFactory;
    protected $table = 'bet_lottery_results';
    protected $guarded = '';
}
