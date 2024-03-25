<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Category;
use App\Models\InvoiceEntry;
use App\Models\Parcel;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;

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
            $invoice_entry_query->join(DB::raw("(SELECT DISTINCT taggable_id, taggable_type FROM meudinherim.taggables WHERE tag_id IN (" . implode(",", $filter["tags"]) . ")) t"), function($join) {
                $join->on('t.taggable_id', '=', 'invoice_entries.id');
                $join->where('t.taggable_type', '=', InvoiceEntry::class);
            });
        }

        $invoice_entry_query->where('categories.type', $categoryType)
            ->whereBetween('date', [$filter['from'], $filter['to']]);

        $parcel_query = $this->model::getQueryForParcelGroupedByCategory();

        if (isset($filter["tags"]) && !empty($filter["tags"])) {
            $parcel_query->join(DB::raw("(SELECT DISTINCT taggable_id, taggable_type FROM meudinherim.taggables WHERE tag_id IN (" . implode(",", $filter["tags"]) . ")) t"), function($join) {
                $join->on('t.taggable_id', '=', 'parcels.id');
                $join->where('t.taggable_type', '=', Parcel::class);
            });
        }
        
        $parcel_query->where('categories.type', $categoryType)
        ->whereBetween('date', [$filter['from'], $filter['to']])
        ->union($invoice_entry_query);
            
        return $parcel_query->get()->toArray();
    }
}
