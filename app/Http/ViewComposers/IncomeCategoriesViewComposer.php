<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\CategoryService;
use App\Repositories\Core\Eloquent\CategoryRepository;

class IncomeCategoriesViewComposer
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
        $this->categories = $service->getIncomeCategoriesForForm();
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('form_income_categories', $this->categories);
    }
}