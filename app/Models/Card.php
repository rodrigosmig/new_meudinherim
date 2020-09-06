<?php

namespace App\Models;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UserTrait;

class Card extends Model
{
    use UserTrait;

    protected $fillable = ['name', 'pay_day', 'closing_day', 'credit_limit', 'balance', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function getCreditLimitAttribute($value)
    {
        return $value / 100;
    }

    public function setCreditLimitAttribute($value)
    {
        $this->attributes['credit_limit'] = $value * 100;
    }

    public function getBalanceAttribute($value)
    {
        return $value / 100;
    }

    public function setBalanceAttribute($value)
    {
        $this->attributes['balance'] = $value * 100;
    }

    /**
     * Return a invoice for a given date.
     * If there is no invoice a new one is created
     *
     * @param string $date
     * @return Invoice
     */
    /* public function getInvoiceByDate($date): Invoice
    {
        $repository = new CardRepository();
        
        $invoice = $repository->getInvoiceByDate($this, $date);
      
        if (! $invoice) {
            $invoice = $this->createInvoice();
        }

        return $invoice;
    } */

    /**
     * Creates a invoice for the card
     *
     * @return Invoice
     */
    /* public function createInvoice(): ?Invoice
    {
        $repository = new CardRepository();
        
        $now = getdate();

        $due_date       = new DateTime($now['year'] . '-' . $now['mon'] . '-' . $this->pay_day);
        $closing_date   = new DateTime($now['year'] . '-' . $now['mon'] . '-' . $this->closing_day);

        if ($this->closing_day <= $now['mday']) {
            $closing_date->modify('+1 month');
        }
        
        if ($due_date <= $closing_date) {
            $due_date->modify('+1 month');
        }

        $data = [
            'amount'        => 0,
            'user_id'       => auth()->user()->id,
            'due_date'      => $due_date,
            'closing_date'  => $closing_date
        ];

        return $repository->createInvoice($this, $data);
    } */

    /**
     * Return invoices for a given status.
     *
     * @param bool $paid
     * @return Illuminate\Database\Eloquent\Collection
     */
  /*   public function getInvoicesByStatus($paid = false)
    {
        $repository = new InvoiceRepository();

        return $repository->getInvoicesByStatus($this->id, $paid);
    } */
}
