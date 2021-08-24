<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryService
{
    protected $repository;

    public function __construct(CategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(Category $category, array $data)
    {
        return $this->repository->update($category, $data);
    }


    public function delete(Category $category)
    {
        return $this->repository->delete($category);
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    /**
     * Get categories of a given type
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesByType($type) 
    {
        return $this->repository->getCategoriesByType($type);
    }

    /**
     * Get income categories for form
     *
     * @return array
     */
    public function getIncomeCategoriesForForm()
    {
        return $this->repository->getIncomeCategoriesForForm();
    }

    /**
     * Get expense categories for form
     *
     * @return array
     */
    public function getExpenseCategoriesForForm() 
    {
        return $this->repository->getExpenseCategoriesForForm();
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
        $income = Category::getDefaultIncomeCategories();

        $expense = Category::getDefaultExpenseCategories();

        foreach ($income as $name) {
            $data = [
                'name' => $name,
                'type' => Category::INCOME
            ];

            $this->repository->create($data);
        }

        foreach ($expense as $name) {
            $data = [
                'name' => $name,
                'type' => Category::EXPENSE
            ];

            $this->repository->create($data);
        }
    }    

    /**
     * Creates default categories for API
     *
     * @return void
     */
    public function createDefaultCategoriesForApi() 
    {
        $income = Category::getDefaultIncomeCategories();

        $expense = Category::getDefaultExpenseCategories();

        foreach ($income as $name) {
            $data = [
                'name' => $name,
                'type' => Category::INCOME
            ];
            $this->repository->createWithoutEvents($data);
        }

        foreach ($expense as $name) {
            $data = [
                'name' => $name,
                'type' => Category::EXPENSE
            ];
            $this->repository->createWithoutEvents($data);
        }
    }

    public function getAllCategoriesByType(): array
    {
        $categories = [
            'income'    => $this->getCategoriesByType(Category::INCOME),
            'expense'   => $this->getCategoriesByType(Category::EXPENSE),
        ];

        return $categories;
    }

    public function getAllCategories()
    {
        return $this->repository->getAllCategories();
    }

    public function getTotalByCategoryType($categoryType, array $filter)
    {
        $entries = $this->repository->getTotalByCategoryType($categoryType, $filter);

        $result = [];

        foreach ($entries as $key => $value) {
            if (!isset($result[$value['category']])) {
                $result[$value['category']]['total'] = 0;
                $result[$value['category']]['quantity'] = 0;
            }

            $result[$value['category']]["id"] = $value['id'];
            $result[$value['category']]["total"] += $value['total'];
            $result[$value['category']]["quantity"] += $value['quantity'];
        }

        uasort($result, function($a, $b) {
            if ($a['total'] == $b['total']) {
                return 0;
            }

            return $a['total'] < $b['total'] ? 1 : -1;
        });

        return $result;
    }
}
