<?php

namespace App\Models;

use App\Models\User;
use App\Models\Invoice;
use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use UserTrait, SoftDeletes;

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

    protected static function boot() {
        parent::boot();
    
        static::deleting(function($card) {
            foreach ($card->invoices as $invoice) {
                $invoice->entries()->delete();
            }
            $card->invoices()->delete();
        });
    }
}
