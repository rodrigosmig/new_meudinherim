<?php

namespace App\Models;

use App\Traits\UserTrait;
use App\Traits\HasCategory;
use App\Models\AccountEntry;
use Illuminate\Database\Eloquent\Model;

class AccountsScheduling extends Model
{
    use UserTrait, HasCategory;
    
    public $fillable =  [
        'due_date', 
        'paid_date', 
        'description', 
        'value', 
        'category_id', 
        'invoice_id', 
        'paid',
        'monthly',
        'user_id'
    ];

    public function accountEntry(){
        return $this->hasOne(AccountEntry::class, 'account_scheduling_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getValueAttribute($value)
    {
        return $value / 100;
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $value * 100;
    }

    /**
     * Checks if the account is paid
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->paid;
    }
}
