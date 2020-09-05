<?php

namespace App\Models;

use App\Models\User;
use App\Models\AccountEntry;
use App\Models\AccountBalance;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
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
}
