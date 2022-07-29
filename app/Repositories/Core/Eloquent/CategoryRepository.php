<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Category;
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
        
        if (isset($filter['type']) && in_array($filter['type'], [Category::INCOME, Category::EXPENSE])) {
            $query->where('type', $filter['type']);
        }
        
        if(isset($filter['isForm']) && $filter['isForm']) {
            return $query->pluck('name', 'id');
        }

        return $query
            ->where('active', $active_categories)
            ->paginate($per_page);
    }

    public function createWithoutEvents($data)
    {
        return $this->model::createWithoutEvents($data);
    }

    public function getInvoiceEntriesByCategoryType($categoryType, array $filter): array
    {
        $invoice_entry_query = $this->model::getQueryForInvoiceEntryGroupedByCategory()
            ->where('categories.type', $categoryType)
            ->whereBetween('date', [$filter['from'], $filter['to']]);

        return $this->model::getQueryForParcelGroupedByCategory()
            ->where('categories.type', $categoryType)
            ->whereBetween('date', [$filter['from'], $filter['to']])
            ->union($invoice_entry_query)
            ->get()
            ->toArray();
    }
}
