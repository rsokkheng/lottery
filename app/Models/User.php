<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\BetUserWallet;
use App\Models\BetLotteryPackage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'package_id',
        'manager_id',
        'name',
        'email',
        'password',
        'phonenumber',
        'provider_id',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function package()
    {
        return $this->belongsTo(BetLotteryPackage::class, 'package_id');
    }
    public function bets():HasMany
    {
        return $this->hasMany(Bet::class);
    }
    public function userWallet()
    {
        return $this->hasMany(BetUserWallet::class);
    }
    // Each user belongs to a manager
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Each manager has many users under them
    public function members()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

   
}
