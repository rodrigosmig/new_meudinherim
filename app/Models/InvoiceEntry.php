<?php

namespace App\Models;

use App\Traits\UserTrait;
use Illuminate\Database\Eloquent\Model;

class InvoiceEntry extends Model
{
    use UserTrait;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_entries';
    
    public $fillable =  ['date', 'description', 'value', 'monthly', 'category_id', 'invoice_id', 'user_id'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

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
     * Checks if the entry category is an expense type
     *
     * @return bool
     */
    public function isExpenseCategory()
    {
        return $this->category->type === $this->category::EXPENSE;
    }

    /**
     * Checks if the authenticated user is invoice entry owner
     *
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->user_id === auth()->user()->id;
    }
}
