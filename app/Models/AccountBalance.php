<?php

namespace App\Models;

use App\Models\Account;
use Illuminate\Database\Eloquent\Model;

class AccountBalance extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
