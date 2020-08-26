<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repositories\Core\Eloquent\CategoryRepository;

class ExpenseCategoriesViewComposer
{
    /**
     * The categories repository implementation.
     *
     * @var CategoryRepository
     */
    protected $categories;

    /**
     * Create a new categories composer.
     *
     * @param  CategoryRepository  $users
     * @return void
     */
    public function __construct(CategoryRepository $repository)
    {
        $this->categories = $repository->getExpenseCategoriesForForm();
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('form_expense_categories', $this->categories);
    }
}