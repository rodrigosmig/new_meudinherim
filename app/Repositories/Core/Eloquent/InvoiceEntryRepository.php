<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\InvoiceEntry;
use App\Models\Tag;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\InvoiceEntryRepositoryInterface;
use Illuminate\Support\Facades\DB;

class InvoiceEntryRepository extends BaseEloquentRepository implements InvoiceEntryRepositoryInterface
{
    protected $model = InvoiceEntry::class;

    /**
     * Returns an array with the total grouped by category for a given date
     *
     * @param string $date
     * @param int $category_type
     * @return array
     */ 
    public function getTotalByCategoryForChart(array $filter, $category_type): array
    {   
        return $this->model::selectRaw('categories.name, SUM(invoice_entries.value) as total')
            ->join('categories', 'categories.id', '=', 'invoice_entries.category_id')
            ->where('categories.type', $category_type)
            ->whereMonth('date', $filter['month'])
            ->whereYear('date', $filter['year'])
            ->groupBy('categories.name')
            ->get()
            ->toArray();
    }

    /**
     * Returns the total values of entries by category type for a given date
     *
     * @param int $categoryType
     * @param array $filter
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getTotalByCategoryTypeForRangeDate($categoryType, array $filter)
    {
        $mutator = 100;
        return $this->model::selectRaw("categories.name as category, categories.id, SUM(invoice_entries.value) / {$mutator} as total, count(*) as quantity")
            ->join('categories', 'categories.id', '=', 'invoice_entries.category_id')
            ->where('categories.type', $categoryType)
            ->where('date', '>=', $filter['from'])
            ->where('date', '<=', $filter['to'])
            ->where('has_parcels', false)
            ->orderByDesc('total')
            ->groupBy('categories.name', 'categories.id')
            ->get();
    }

    /**
     * Returns the entries for the given category id and range date
     *
     * @param int $categoryType
     * @param array $filter
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getEntriesByCategoryAndRangeDate($from, $to, $category_id, array $tags = [])
    {
        $query = $this->model::with('invoice.card')
            ->with('category');

        if (!empty($tags)) {            
            $query->join(DB::raw("(SELECT DISTINCT taggable_id, taggable_type FROM meudinherim.taggables WHERE tag_id IN (" . implode(",", $tags) . ")) t"), function($join) {
                $join->on('t.taggable_id', '=', 'invoice_entries.id');
                $join->where('t.taggable_type', '=', InvoiceEntry::class);
            });
        }

        return $query->where('category_id', $category_id)
            ->whereBetween('date', [$from, $to])
            ->where('has_parcels', false)
            ->orderBy('date')
            ->get();
    }

    /**
     * Returns the total values of entries by category type for a given date
     *
     * @param int $categoryType
     * @param string $date
     * @return float
     */ 
    public function getTotalMonthlyByCategory($categoryType, $filter): float
    {
        $total = $this->model::join('categories', 'categories.id', '=', 'invoice_entries.category_id')
            ->where('categories.type', $categoryType)
            ->whereMonth('date', $filter['month'])
            ->whereYear('date', $filter['year'])
            ->sum('value');
               
        return $total / 100;
    }

    /**
     * Returns entries for a given invoice
     *
     * @param Invoice $invoice
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getEntries($invoice)
    {
        return $invoice->entries()
                ->whereDoesntHave('parcels')
                ->get();
    }

    /**
     * Creates an invoice entry parcel
     */ 
    public function createInvoiceEntryParcel($entry, array $data)
    {
        return $entry->parcels()->create([
            'date'          => $data['date'],
            'description'   => $data['description'],
            'value'         => $data['parcel_value'],
            'parcel_number' => $data['parcel_number'],
            'parcel_total'  => $data['total_parcels'],
            'invoice_id'    => $data['invoice_id'],
            'category_id'   => $data['category_id'],
        ]);
    }
}