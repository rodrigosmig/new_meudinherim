<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Category;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository extends BaseEloquentRepository implements CategoryRepositoryInterface
{
    protected $model = Category::class;

    /**
     * Get categories of a given type
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesByType($type) {

        return $this->model::where('type', $type)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get categories all categories
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllCategories()
    {

        return $this->model::orderBy('name')->get();
    }

    /**
     * Get income categories for form
     *
     * @return array
     */
    public function getIncomeCategoriesForForm()
    {
        return $this->model::where('type', $this->model::INCOME)
            ->orderBy('name')
            ->pluck('name', 'id'); 
    }

    /**
     * Get expense categories for form
     *
     * @return array
     */
    public function getExpenseCategoriesForForm() {
        return $this->model::where('type', $this->model::EXPENSE)
            ->orderBy('name')
            ->pluck('name', 'id');
    }

    public function createWithoutEvents($data)
    {
        return $this->model::createWithoutEvents($data);
    }

    public function getTotalByCategoryType($categoryType, array $filter): array
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
