<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryService
{
    protected $repository;
    protected $tagService;

    public function __construct(CategoryRepositoryInterface $repository, TagService $tagService)
    {
        $this->repository = $repository;
        $this->tagService = $tagService;
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
    public function getCategoriesByType($type, $per_page = 100) 
    {
        return $this->repository->getCategories(['type' => $type], $per_page);
    }

    /**
     * Get income categories for form
     *
     * @return array
     */
    public function getIncomeCategoriesForForm()
    {
        $filter = [
            'type'      => Category::INCOME,
            'isForm'    => true
        ];

        return $this->repository->getCategories($filter, 1000);
    }

    /**
     * Get income categories for form
     *
     * @return array
     */
    public function getAllCategoriesForApiForm()
    {
        $allcategories = $this->getAllCategoriesForForm();

        $data = [
            'income' => [],
            'expense' => []
        ];

        foreach ($allcategories as $categoryType => $categories) {
            foreach ($categories as $key => $value) {
                $data[$categoryType][] = [
                    'id' => $key,
                    'label' => $value
                ];
            }
        }

        return $data;
    }

    /**
     * Get expense categories for form
     *
     * @return array
     */
    public function getExpenseCategoriesForForm() 
    {
        $filter = [
            'type'      => Category::EXPENSE,
            'isForm'    => true
        ];

        return $this->repository->getCategories($filter, 1000);
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

    public function getAllInactiveCategories(int $per_page)
    {
        return $this->repository->getCategories(['active' => false], $per_page);
    }

    public function getCategories(array $filter, int $per_page)
    {
        return $this->repository->getCategories($filter, $per_page);
    }

    public function getAllCategories(int $per_page)
    {
        return $this->repository->getCategories([], $per_page);
    }

    public function getTotalOfInvoiceEntriesByCategoryType($categoryType, array $filter)
    {
        $entries = $this->repository->getInvoiceEntriesByCategoryType($categoryType, $filter);

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

    public function getTotalOfInvoiceEntriesByCategoryTypeForApi($categoryType, array $filter)
    {
        if (isset($filter["tags"])) {
            $tags = $this->tagService->getTags($filter["tags"])->toArray();
            $filter["tags"] = array_map(function($tags) {
                return $tags["id"];
            }, $tags);
        }

        $entries = $this->getTotalOfInvoiceEntriesByCategoryType($categoryType, $filter);

        $result = [];

        foreach ($entries as $key => $value) {
            $result[] = [
                "category"  => $key,
                "id"        => $value['id'],
                "total"     => $value['total'],
                "quantity"  => $value['quantity']
            ];
        }

        return $result;
    }
}
