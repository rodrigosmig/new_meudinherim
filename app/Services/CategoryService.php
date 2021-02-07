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
            ->orderBy('name')
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
        $income = $this->category->getDefaultIncomeCategories();

        $expense = $this->category->getDefaultExpenseCategories();

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

    /**
     * Creates default categories for API
     *
     * @return void
     */
    public function createDefaultCategoriesForApi() 
    {
        $income = $this->category->getDefaultIncomeCategories();

        $expense = $this->category->getDefaultExpenseCategories();

        foreach ($income as $name) {
            $data = [
                'name' => $name,
                'type' => $this->category::INCOME
            ];
            $this->category->createWithoutEvents($data);
        }

        foreach ($expense as $name) {
            $data = [
                'name' => $name,
                'type' => $this->category::EXPENSE
            ];
            $this->category->createWithoutEvents($data);
        }
    }

    public function getAllCategories(): array
    {
        $categories = [
            'income'    => $this->getCategoriesByType($this->category::INCOME),
            'expense'   => $this->getCategoriesByType($this->category::EXPENSE),
        ];

        return $categories;
    }
}
