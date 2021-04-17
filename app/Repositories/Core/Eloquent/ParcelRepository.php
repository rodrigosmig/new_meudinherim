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
                $query->where('parcels.invoice_id', '=', $invoice->id);
            })->get();
    }

    /**
     * Returns parcels for a given account scheduling
     *
     * @param Invoice $invoice
     * @return Illuminate\Database\Eloquent\Collection
     */ 
    public function getParcelsOfAccountsScheduling(int $categoryType, array $filter)
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
     * @param int $categoryType
     * @param array $filter
     * @return array
     */ 
    public function getParcelsByCategoryAndRangeDate($from, $to, $category_id): array
    {
        return $this->model::with('invoice.card')
            ->with('category')
            ->where('category_id', $category_id)
            ->where('date', '>=', $from)
            ->where('date', '<=', $to)
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}