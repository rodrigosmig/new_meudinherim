<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function store(array $data)
    {
        return $this->category->create($data);
    }

    public function update($id, array $data)
    {
        $category = $this->findById($id);

        if (! $category) {
            return false;
        }

        return $category->update($data);
    }


    public function delete($id)
    {
        $category = $this->findById($id);

        if (! $category) {
            return false;
        }

        return $category->delete();
    }

    public function findById($id)
    {
        return $this->category->find($id);
    }

    /**
     * Get categories of a given type
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesByType($type) 
    {
        return $this->category::where('type', $type)
            ->get();
    }

    /**
     * Get income categories for form
     *
     * @return array
     */
    public function getIncomeCategoriesForForm()
    {
        return $this->category::where('type', $this->category::INCOME)
                    ->orderBy('name')
                    ->pluck('name', 'id'); 
    }

    /**
     * Get expense categories for form
     *
     * @return array
     */
    public function getExpenseCategoriesForForm() 
    {
        return $this->category::where('type', $this->category::EXPENSE)
                    ->orderBy('name')
                    ->pluck('name', 'id');
    }

    /**
     * Get all categories for form
     *
     * @return array
     */
    public function getAllCategoriesForForm() 
    {
        $categories = [
            'income'    => $this->getIncomeCategoriesForForm(),
            'expense'   => $this->getExpenseCategoriesForForm()
        ];

        return $categories;
    }

    /**
     * Creates default categories
     *
     * @return void
     */
    public function createDefaultCategories() 
    {
        $income = [
            __('global.default_categories.salary'),
            __('global.default_categories.revenue'),
            __('global.default_categories.withdraw'),
            __('global.default_categories.loans'),
            __('global.default_categories.investments'),
            __('global.default_categories.credit_on_card'),
            __('global.default_categories.bank_transfer'),
            __('global.default_categories.sales'),
            __('global.default_categories.others'),
        ];

        $expense = [
            __('global.default_categories.house'),
            __('global.default_categories.subscriptions'),
            __('global.default_categories.personal_expenses'),
            __('global.default_categories.education'),
            __('global.default_categories.loans'),
            __('global.default_categories.electronics'),
            __('global.default_categories.recreation'),
            __('global.default_categories.food'),
            __('global.default_categories.health'),
            __('global.default_categories.payments'),
            __('global.default_categories.supermarket'),
            __('global.default_categories.investments'),
            __('global.default_categories.bank_transfer'),
            __('global.default_categories.transport'),
            __('global.default_categories.withdraw'),
            __('global.default_categories.clothes'),
            __('global.default_categories.travels'),
            __('global.default_categories.others'),
        ];

        foreach ($income as $name) {
            $data = [
                'name' => $name,
                'type' => $this->category::INCOME
            ];
            $this->store($data);
        }

        foreach ($expense as $name) {
            $data = [
                'name' => $name,
                'type' => $this->category::EXPENSE
            ];
            $this->store($data);
        }
    }
}
