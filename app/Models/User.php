<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\BetUserWallet;
use App\Models\AccountUSD;
use App\Models\AccountVND;
use App\Models\BetLotteryPackage;
use Laravel\Sanctum\HasApiTokens;
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
        'master_id',
        'bet_system',
        'currency',
        'name',
        'email',
        'password',
        'phonenumber',
        'provider_id',
        'avatar',
        'record_status_id',
        'is_active',
        'created_by',
        'updated_by',
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
    // Immediate parent (manager or master who created this user)
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Direct children created by this user
    public function members()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    // Master Agent this user belongs to (for senior/member tracing back to master)
    public function master()
    {
        return $this->belongsTo(User::class, 'master_id');
    }

    // All users directly under this master
    public function masterChildren()
    {
        return $this->hasMany(User::class, 'master_id');
    }
    public function accountVND()
    {
        return $this->hasOne(AccountVND::class, 'user_id');
    }

    public function accountUSD()
    {
        return $this->hasOne(AccountUSD::class, 'user_id');
    }
public function accountKH()
    {
        return $this->hasOne(AccountKH::class);
    }

    public function creditTransactionsKH()
    {
        return $this->hasMany(CreditTransactionKH::class);
    }

}
