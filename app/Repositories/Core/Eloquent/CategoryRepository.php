<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\Category;
use App\Repositories\Core\BaseEloquentRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository extends BaseEloquentRepository implements CategoryRepositoryInterface
{
    protected $model = Category::class;

    /* public function getCategoriesByType($type) {

        return $this->model::where('type', $type)
            ->where('user_id', auth()->user()->id)
            ->get();
    }

    public function getIncomeCategoriesForForm()
    {
        return $this->model::where('user_id', auth()->user()->id)
                    ->where('type', $this->model::INCOME)
                    ->pluck('name', 'id'); 
    }

    public function getExpenseCategoriesForForm() {
        return $this->model::where('user_id', auth()->user()->id)
                    ->where('type', $this->model::EXPENSE)
                    ->pluck('name', 'id');
    }

    public function getAllCategoriesForForm() {
        $categories = [
            'income'    => $this->getIncomeCategoriesForForm(),
            'expense'   => $this->getExpenseCategoriesForForm()
        ];

        return $categories;
    } */
}