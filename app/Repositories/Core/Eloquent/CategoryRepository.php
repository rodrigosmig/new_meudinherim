<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Category;
use App\Models\InvoiceEntry;
use App\Models\Parcel;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository extends BaseEloquentRepository implements CategoryRepositoryInterface
{
    protected $model = Category::class;

    /**
     * Get categories according to the given filter
     *
     * @param string $type
     * @param int $per_page
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCategories(array $filter, int $per_page) {
        $query = $this->model::orderBy('name');

        $active_categories = (isset($filter['active']) && !$filter['active']) ? false : true;

        $query->where('active', $active_categories);
        
        if (isset($filter['type']) && in_array($filter['type'], [Category::INCOME, Category::EXPENSE])) {
            $query->where('type', $filter['type']);
        }
        
        if(isset($filter['isForm']) && $filter['isForm']) {
            return $query->pluck('name', 'id');
        }

        return $query
            ->paginate($per_page);
    }

    public function createWithoutEvents($data)
    {
        return $this->model::createWithoutEvents($data);
    }

    public function getInvoiceEntriesByCategoryType($categoryType, array $filter): array
    {
        $invoice_entry_query = $this->model::getQueryForInvoiceEntryGroupedByCategory();            

        if (isset($filter["tags"]) && !empty($filter["tags"])) {
            $invoice_entry_query->join('taggables', function($join)
            {
                $join->on('taggables.taggable_id', '=', 'invoice_entries.id');
                $join->where('taggables.taggable_type','=', InvoiceEntry::class);
            })
            ->whereIn("taggables.tag_id", $filter["tags"]);
        }

        $invoice_entry_query->where('categories.type', $categoryType)
            ->whereBetween('date', [$filter['from'], $filter['to']]);

        $parcel_query = $this->model::getQueryForParcelGroupedByCategory();

        if (isset($filter["tags"]) && !empty($filter["tags"])) {
            $parcel_query->join('taggables', function($join)
            {
                $join->on('taggables.taggable_id', '=', 'parcels.id');
                $join->where('taggables.taggable_type','=', Parcel::class);
            });
        }
        
            $parcel_query->where('categories.type', $categoryType)
            ->whereBetween('date', [$filter['from'], $filter['to']])
            ->union($invoice_entry_query);
        
        if (isset($filter["tags"]) && !empty($filter["tags"])) {
            $parcel_query->whereIn("taggables.tag_id", $filter["tags"]);
        }
            
        return $parcel_query->get()->toArray();
    }
}
