<?php

namespace App\Traits;

use App\Models\Category;

trait HasCategory
{
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Checks if the entry category is an expense type
     *
     * @return bool
     */
    public function isExpenseCategory()
    {
        return $this->category->type == $this->category::EXPENSE;
    }
}