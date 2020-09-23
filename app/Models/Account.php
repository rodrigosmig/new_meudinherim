<?php

namespace App\Models;

use App\Models\User;
use App\Traits\UserTrait;
use App\Models\AccountEntry;
use App\Models\AccountBalance;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use UserTrait;
    
    const MONEY             = 'money';
    const SAVINGS           = 'savings';
    const CHECKING_ACCOUNT  = 'checking_account';
    const INVESTMENT        = 'investment';

    const TYPES = [
        self::MONEY,
        self::SAVINGS,
        self::CHECKING_ACCOUNT,
        self::INVESTMENT,
    ];

    const ARRAY_TYPES = [
        self::MONEY             => self::MONEY,
        self::SAVINGS           => self::SAVINGS,
        self::CHECKING_ACCOUNT  => self::CHECKING_ACCOUNT,
        self::INVESTMENT        => self::INVESTMENT,
    ];

    protected $appends = ['balance'];

    protected $fillable = ['type','name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entries()
    {
        return $this->hasMany(AccountEntry::class);
    }

    public function balances()
    {
        return $this->hasMany(AccountBalance::class);
    }

    public function getBalanceAttribute()
    {
        $today = now()->format('Y-m-d');
        $balance = $this->balances()->where('date', '<=', $today)->latest('date')->first();

        return $balance->current_balance;
    }
}
