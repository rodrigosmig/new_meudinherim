<?php

namespace App\Models;

use App\Models\Invoice;
use App\Traits\UserTrait;
use App\Traits\HasCategory;
use App\Models\AccountEntry;
use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    use UserTrait, HasCategory;
    
    public $fillable =  [
        'date',
        'due_date',
        'paid_date',
        'description', 
        'value',
        'category_id',
        'parcel_number', 
        'parcel_total',
        'paid',
        'anticipated',
        'invoice_id',
        'user_id'
    ];

    protected $casts = [
        'paid'          => 'boolean',
        'anticipated'   => 'boolean',
    ];

    public function parcelable()
    {
        return $this->morphTo();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function accountEntry(){
        return $this->hasOne(AccountEntry::class);
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
        return $this->paid == true;
    }

    /**
     * Checks if is an account scheduling parcel
     *
     * @return bool
     */
    public function isParcel()
    {
        return isset($this->parcelable_type) && isset($this->parcelable_id) && $this->parcelable_type === AccountsScheduling::class;
    }
}
