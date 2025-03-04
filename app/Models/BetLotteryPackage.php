<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BetLotteryPackage extends Model
{
    use HasFactory;
    protected $table = 'bet_lottery_packages';

    public function packageConfiges()
    {
        return $this->hasMany(BetLotteryPackageConfiguration::class, 'package_id', 'id');
    }
    
}
