<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Parcel;
use App\Models\InvoiceEntry;
use App\Models\AccountsScheduling;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\ParcelRepositoryInterface;

class ParcelRepository extends BaseEloquentRepository implements ParcelRepositoryInterface
{
    protected $model = Parcel::class;

    /**
     * Returns parcels for a given invoice
     *
     * @param Invoice $invoice
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getParcelsOfInvoice($invoice)
    {
        return $this->model::whereHasMorph(
            'parcelable', 
            InvoiceEntry::class,
            function (Builder $query) use ($invoice) {
                $query->where('parcels.invoice_id', '=', $invoice->id)
                    ->where('parcels.anticipated', false);
            })->get();
    }

    /**
     * Returns parcels for a given category type
     *
     * @param int $categoryType
     * @param array $filter
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getParcelsOfAccountsScheduling($categoryType, $filter)
    {
        return $this->model::whereHasMorph(
            'parcelable', 
            AccountsScheduling::class,
            function (Builder $query) use ($categoryType, $filter) {
                $query->join('categories', 'categories.id', '=', 'category_id')
                    ->where('categories.type', $categoryType)
                    ->whereBetween('parcels.due_date', [$filter['from'], $filter['to']]);

                    if (isset($filter['status']) && $filter['status']) {
                        $status = $filter['status'] == 'open' ? false : true;
                        $query->where('parcels.paid', $status);
                    }
                
            })->get();
    }

    /**
     * Returns parcels for a given user and a given category type
     *
     * @param User $user
     * @param int $categoryType
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getParcelsOfAccountsSchedulingForCron($user, $categoryType)
    {
        return $this->model::withoutGlobalScopes()
            ->whereHasMorph(
                'parcelable', 
                AccountsScheduling::class,
                function (Builder $query) use ($user, $categoryType) {
                    $query->join('categories', 'categories.id', '=', 'category_id')
                        ->withoutGlobalScopes()
                        ->where('categories.type', $categoryType)
                        ->where('parcels.due_date',  now()->format('Y-m-d'))
                        ->where('parcels.paid', false)
                        ->where('parcels.user_id', $user->id);
                })  
                ->get();
    }

    /**
     * Returns parcels for a given account scheduling
     *
     * @param int $account_scheduling_id
     * @param int $parcel_id
     * @return Parcel
     */ 
    public function findParcelsOfAccountsScheduling($account_scheduling_id, $parcel_id)
    {
        return $this->model::whereHasMorph(
            'parcelable', 
            AccountsScheduling::class,
            function (Builder $query) use ($account_scheduling_id, $parcel_id) {
                $query->where('parcels.id', $parcel_id)
                    ->where('parcelable_id', $account_scheduling_id);
            })->first();
    }

    /**
     * Returns the parcels for the given category id and range date
     *
     * @param string $from
     * @param string $to
     * @param int $category_id
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getParcelsByCategoryAndRangeDate($from, $to, $category_id)
    {
        return $this->model::with('invoice.card')
            ->with('category')
            ->where('category_id', $category_id)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->orderBy('date')
            ->get();
    }

    /**
     * Returns the total amount of parcels for the given category id and range date
     *
     * @param int $categoryType
     * @param array $filter
     * @return array
     */ 
    public function getTotalByCategoryTypeForRangeDate($category_type, array $filter)
    {
        $mutator = 100;
        return $this->model::selectRaw("categories.name as category, categories.id, SUM(parcels.value) / {$mutator} as total, count(*) as quantity")
            ->join('categories', 'categories.id', '=', 'parcels.category_id')
            ->where('categories.type', $category_type)
            ->whereBetween('date', [$filter['from'], $filter['to']])
            ->orderByDesc('total')
            ->groupBy('categories.name', 'categories.id')
            ->get();
    }

    /**
     * Returns parcel for a given invoice entry
     *
     * @param int $invoice_entry_id
     * @param int $parcel_id
     * @return Parcel
     */ 
    public function findParcelOfInvoiceEntry($invoice_entry_id, $parcel_id)
    {
        return $this->model::whereHasMorph(
            'parcelable', 
            InvoiceEntry::class,
            function (Builder $query) use ($invoice_entry_id, $parcel_id) {
                $query->where('parcels.id', $parcel_id)
                    ->where('parcelable_id', $invoice_entry_id);
            })->first();
    }

    /**
     * Returns the nexts parcels for a given parcel number
     *
     * @param InvoiceEntry $invoice_entry
     * @param int $parcel_number
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getOpenParcels($invoice_entry, $parcel_number)
    {
        return $invoice_entry->parcels()
                    ->select('parcels.*')
                    ->join('invoices', 'invoices.id', '=', 'parcels.invoice_id')
                    ->where('invoices.paid', false)
                    ->where('anticipated', false)
                    ->where('parcel_number', '>', $parcel_number)
                    ->get();
    }
}
