<?php

namespace App\Models;

use App\Models\Invoice;
use App\Traits\HasParcel;
use App\Traits\UserTrait;
use App\Traits\HasCategory;
use App\Traits\HasTag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceEntry extends Model
{
    use UserTrait, HasCategory, HasParcel, HasTag, SoftDeletes, HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_entries';
    
    public $fillable =  ['date', 'description', 'value', 'monthly', 'has_parcels', 'anticipated', 'category_id', 'invoice_id', 'user_id'];

    protected $casts = [
        'has_parcels' => 'boolean',
        'anticipated' => 'boolean',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
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
