<?php

namespace App\Models;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTrait;

class Card extends Model
{
    use UserTrait;

    protected $fillable = ['name', 'pay_day', 'closing_day', 'credit_limit', 'balance', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getCreditLimitAttribute($value)
    {
        return $value / 100;
    }

    public function setCreditLimitAttribute($value)
    {
        $this->attributes['credit_limit'] = $value * 100;
    }

    public function getBalanceAttribute($value)
    {
        return $value / 100;
    }

    public function setBalanceAttribute($value)
    {
        $this->attributes['balance'] = $value * 100;
    }

}
