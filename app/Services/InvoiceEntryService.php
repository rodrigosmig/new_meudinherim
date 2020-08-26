<?php

namespace App\Services;

use App\Models\InvoiceEntry;
use App\Exceptions\InsufficientLimitException;

class InvoiceEntryService
{
    protected $entry;

    public function __construct(InvoiceEntry $entry)
    {
        $this->entry = $entry;
    }

    public function update($id, array $data)
    {
        $data['monthly'] = isset($data['monthly']) && $data['monthly'] === 'on' ? true : false;

        $entry = $this->findById($id);

        if ($data['value'] > $entry->invoice->card->balance) {
            throw new InsufficientLimitException(__('messages.entries.insufficient_limit'));
        }

        $result = $entry->update($data);

        if (! $result) {
            return false;
        }

        return $entry;
    }

    public function delete($id)
    {
        $entry = $this->findById($id);

        return $entry->delete();
    }

    public function findById($id)
    {
        return $this->entry->find($id);
    }

    
}
