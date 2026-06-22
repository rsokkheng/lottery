<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetNumberKH extends Model
{
    use HasFactory;
    protected $table = 'bet_number_kh_vnd';
    protected $guarded = [];

    public function betKH()
    {
        return $this->belongsTo(BetKH::class, 'id', 'bet_id');
    }

    public function betNumberWinKH()
    {
        return $this->hasOne(BetWinningRecordKH::class, 'bet_number_id', 'id');
    }
}

class BetNumberKHUSD extends Model
{
    use HasFactory;
    protected $table = 'bet_number_kh_usd';
    protected $guarded = [];

    public function betKHUSD()
    {
        return $this->belongsTo(BetKHUSD::class, 'id', 'bet_id');
    }

    public function betNumberWinKHUSD()
    {
        return $this->hasOne(BetWinningRecordKHUSD::class, 'bet_number_id', 'id');
    }

    public function betNumberWinKH() { return $this->betNumberWinKHUSD(); }
}
