<?php

namespace App\Models;

use App\Models\Card;
use App\Models\User;
use App\Traits\UserTrait;
use DateTime;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use UserTrait;
    
    public $fillable =  ['due_date', 'closing_date', 'amount', 'paid', 'card_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function entries()
    {
        return $this->hasMany(InvoiceEntry::class);
    }

    public function payable(){
        return $this->hasOne(AccountsScheduling::class);
    }

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    /**
     * Checks if the invoice is closed
     *
     * @return bool
     */
    public function isClosed(): bool
    {
        $closing_date = new DateTime($this->closing_date);

        return $closing_date < now();
    }

    /**
     * Checks if the invoice is paid
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }
}
