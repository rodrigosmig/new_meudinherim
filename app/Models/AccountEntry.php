<?php

namespace App\Models;

use App\Models\Account;
use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;

class AccountEntry extends Model
{
    use UserTrait;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_entries';

    public $fillable =  ['date', 'description', 'value', 'category_id', 'account_id', 'user_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
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
     * Checks if the entry category is an expense type
     *
     * @return bool
     */
    public function isExpenseCategory()
    {
        return $this->category->type === $this->category::EXPENSE;
    }
}
