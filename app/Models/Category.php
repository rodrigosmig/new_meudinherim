<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
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
