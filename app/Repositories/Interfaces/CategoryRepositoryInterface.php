<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function getCategoriesByType($type);
    public function getIncomeCategoriesForForm();
    public function getExpenseCategoriesForForm();
}