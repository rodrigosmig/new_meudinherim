<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\CategoryService;

class AllCategoriesViewComposer
{
    /**
     * The categories repository implementation.
     *
     * @var array
     */
    protected $categories;

    /**
     * Create a new categories composer.
     *
     * @param  CategoryService  $service
     * @return void
     */
    public function __construct(CategoryService $service)
    {
        $this->categories = $service->getAllCategoriesForForm();
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('form_all_categories', $this->categories);
    }
}