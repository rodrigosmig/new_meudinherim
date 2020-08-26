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
        $data['user_id'] = auth()->user()->id;
        //dd($data);
        return $this->category->create($data);
    }

    public function update($id, array $data)
    {
        $category = $this->findById($id);

        return $category->update($data);
    }


    public function delete($id)
    {
        $category = $this->findById($id);

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
            ->where('user_id', auth()->user()->id)
            ->get();
    }

    /**
     * Get income categories for form
     *
     * @return array
     */
    public function getIncomeCategoriesForForm()
    {
        return $this->category::where('user_id', auth()->user()->id)
                    ->where('type', $this->category::INCOME)
                    ->pluck('name', 'id'); 
    }

    /**
     * Get expense categories for form
     *
     * @return array
     */
    public function getExpenseCategoriesForForm() 
    {
        return $this->category::where('user_id', auth()->user()->id)
                    ->where('type', $this->category::EXPENSE)
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
}
