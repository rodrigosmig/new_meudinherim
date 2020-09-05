<?php

namespace App\Observers;

use App\Models\InvoiceEntry;

class InvoiceEntryObserver
{
    /**
     * Handle the invoice entry "created" event.
     *
     * @param  \App\Models\InvoiceEntry  $invoiceEntry
     * @return void
     */
    public function creating(InvoiceEntry $invoiceEntry)
    {
        $invoiceEntry->user_id = auth()->user()->id;
    }

    /**
     * Handle the invoice entry "updated" event.
     *
     * @param  \App\Models\InvoiceEntry  $invoiceEntry
     * @return void
     */
    public function updating(InvoiceEntry $invoiceEntry)
    {
        $invoiceEntry->user_id = auth()->user()->id;
    }
}
