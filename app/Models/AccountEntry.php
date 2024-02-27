<?php

namespace App\Models;

use App\Models\Parcel;
use App\Models\Account;
use App\Traits\UserTrait;
use App\Traits\HasCategory;
use App\Models\AccountsScheduling;
use App\Traits\HasTag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountEntry extends Model
{
    use UserTrait, HasCategory, HasFactory, HasTag;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'account_entries';

    public $fillable =  ['date', 'description', 'value', 'category_id', 'account_id', 'parcel_id', 'account_scheduling_id', 'user_id'];


    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function accountScheduling()
    {
        return $this->belongsTo(AccountsScheduling::class);
    }

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
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
     * Checks if the entry was made by an accounts payable
     *
     * @return bool
     */
    public function isPayable()
    {
        return $this->accountScheduling && $this->isExpenseCategory();
    }

    /**
     * Checks if the entry was made by an accounts payable
     *
     * @return bool
     */
    public function isPayableParcel()
    {
        return $this->parcel && $this->isExpenseCategory();
    }

     /**
     * Checks if the entry was made by an accounts receivable
     *
     * @return bool
     */
    public function isReceivable()
    {
        return $this->accountScheduling && !$this->isExpenseCategory();
    }

    /**
     * Checks if the entry was made by an accounts receivable
     *
     * @return bool
     */
    public function isReceivableParcel()
    {
        return $this->parcel && !$this->isExpenseCategory();
    }
}
