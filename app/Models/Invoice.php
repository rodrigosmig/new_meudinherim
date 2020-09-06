<?php

namespace App\Models;

use App\Models\Card;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use UserTrait;
    
    public $fillable =  ['due_date', 'closing_date', 'amount', 'paid', 'card_id', 'user_id'];

    protected $casts = [
        'paid' => 'boolean',
    ];

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

    public function getAmountAttribute($value)
    {
        return $value / 100;
    }

    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value * 100;
    }

    /**
     * Checks if the authenticated user is invoice owner
     *
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->user_id === auth()->user()->id;
    }
}
