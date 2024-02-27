<?php

namespace App\Models;

use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use UserTrait, HasFactory;

    public $fillable =  ['name', 'user_id'];

    public function taggable()
    {
        return $this->morphTo();
    }

    public function accountEntries(): MorphToMany
    {
        return $this->morphedByMany(AccountEntry::class, 'taggable');
    }

    public function invoiceEntries(): MorphToMany
    {
        return $this->morphedByMany(InvoiceEntry::class, 'taggable');
    }
}
