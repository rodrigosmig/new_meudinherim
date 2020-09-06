<?php

namespace App\Models;

use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use UserTrait;

    const INCOME    = 1;
    const EXPENSE   = 2;
    
    protected $fillable = ['type','name', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoiceEntry()
    {
        return $this->hasMany(InvoiceEntry::class);
    }
}
