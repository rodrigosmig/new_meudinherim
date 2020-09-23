<?php

namespace App\Models;

use App\Models\Account;
use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;

class AccountBalance extends Model
{
    use UserTrait;

    protected $fillable = ['date', 'previous_balance', 'current_balance'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function getPreviousBalanceAttribute($value)
    {
        return $value / 100;
    }

    public function setPreviousBalanceAttribute($value)
    {
        $this->attributes['previous_balance'] = $value * 100;
    }

    public function getCurrentBalanceAttribute($value)
    {
        return $value / 100;
    }

    public function setCurrentBalanceAttribute($value)
    {
        $this->attributes['current_balance'] = $value * 100;
    }
}
