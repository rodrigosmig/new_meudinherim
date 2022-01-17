<?php

namespace App\Services;

use DateTime;
use App\Models\Card;
use App\Models\Invoice;
use App\Http\Resources\InvoiceResource;
use App\Repositories\Core\Eloquent\InvoiceRepository;

class InvoiceService
{
    protected $invoice;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function store(array $data)
    {
        return $this->invoiceRepository->create($data);
    }

    public function update($id, array $data)
    {
        $invoice = $this->findById($id);

        if (! $invoice) {
            return false;
        }

        return $this->invoiceRepository->update($data);
    }

    public function delete($id)
    {
        $invoice = $this->findById($id);

        if (! $invoice) {
            return false;
        }

        return $this->invoiceRepository->delete();
    }

    public function findById($id)
    {
        return $this->invoiceRepository->find($id);
    }

    /**
     * Returns invoices by status
     *
     * @param bool $paid
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllInvoicesByStatus($paid = false)
    {
        return $this->invoiceRepository->getAllInvoicesByStatus($paid);
    }

    /**
     * Returns an array with the total invoices of the lasts six months
     * The array key represents the month number
     *
     * @return array
     */ 
    public function getTotalInvoicesForSixMonthsForChart($date): array
    {
        $result     = [];
        $cards_name = Card::pluck('name');

        foreach ($cards_name as $card) {
            $new_date   = (new DateTime($date))->modify('-5 months');

            for ($i=0; $i < 6; $i++) { 
                $month  = $new_date->format('m');
                $year   = $new_date->format('Y');

                $total = $this->invoiceRepository->getInvoiceAmountForWebChart($card, $month, $year);

                $result[$card][] = $total / 100;
                                
                $new_date = $new_date->modify("+1 month");
            }

        }

        return $result;
    }

    /**
     * Returns the most recent open invoice
     *
     * @return array
     */ 
    public function getOpenInvoicesForMenu(): array
    {
        $cards  = auth()->user()->cards;
        $result = [];
        $total  = 0;

        foreach ($cards as $card) {            
            $invoice = $this->invoiceRepository->getTheFirstOpenInvoice($card);
            
            if ($invoice) {
                $result[$card->name] = $invoice;
                $total += $invoice->amount;
            }
        }

        $result['total'] = $total;
        
        return $result;
    }

    /**
     * Returns the most recent open invoice
     *
     * @return array
     */ 
    public function getOpenInvoicesForApi(): array
    {
        $cards  = auth()->user()->cards;
        $result = [];
        $total  = 0;

        $result['invoices'] = [];

        foreach ($cards as $card) {            
            $invoice = $this->invoiceRepository->getTheFirstOpenInvoice($card);

            if ($invoice){
                $result['invoices'][] = new InvoiceResource($invoice);
                $total += $invoice->amount;
            }
        }

        $result['total'] = $total;
        
        return $result;
    }

    /**
     * Returns the total of all open invoices for a given range date
     *
     * @return array
     */ 
    public function getTotalOfOpenInvoices($range_date): float
    {
        $total = 0;

        $cards = auth()->user()->cards;

        foreach ($cards as $card) {
            $total += ($this->invoiceRepository->getTotalOfOpenInvoice($card, $range_date) / 100);
        }

        return $total;
    }

    /**
     * Returns an array with the total invoices of the lasts six months
     * The array key represents the month number
     *
     * @return array
     */ 
    public function getInvoiceAmountForSixMonthsForChart($date): array
    {
        $result = [];
        $cards  = auth()->user()->cards;

        foreach ($cards as $card) {
            $new_date = (new DateTime($date))->modify('-5 months');

            $total = [];

            for ($i=0; $i < 6; $i++) { 
                $month  = $new_date->format('m');
                $year   = $new_date->format('Y');

                $amount = $this->invoiceRepository->getInvoicesAmountForChart(
                    $card->id,
                    $month,
                    $year
                );

                $total[] = $amount / 100;
                                
                $new_date = $new_date->modify("+1 month");
            }
            $result[] = [
                "name" => $card->name,
                "data" => $total
            ];
        }

        return $result;
    }
}
