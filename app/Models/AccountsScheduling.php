<?php

namespace App\Models;

use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;

class AccountsScheduling extends Model
{
    use UserTrait;
    
    public $fillable =  ['date', 'description', 'value', 'category_id', 'paid', 'user_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
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
     * Checks if the category is an expense type
     *
     * @return bool
     */
    public function isExpenseCategory()
    {
        return $this->category->type === $this->category::EXPENSE;
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
