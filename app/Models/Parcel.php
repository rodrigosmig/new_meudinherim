<?php

namespace App\Models;

use App\Traits\UserTrait;
use App\Traits\HasCategory;
use Illuminate\Database\Eloquent\Model;

class Parcel extends Model
{
    use UserTrait, HasCategory;
    
    public $fillable =  [
        'date',
        'paid_date',
        'description', 
        'value',
        'category_id',
        'parcel_number', 
        'parcel_total',
        'paid',
        'invoice_id',
        'user_id'];

    public function parcelable()
    {
        return $this->morphTo();
    }

    public function getValueAttribute($value)
    {
        return $value / 100;
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = $value * 100;
    }
}
