<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function getCategoriesByType($type);
    public function getAllCategories();
    public function getIncomeCategoriesForForm();
    public function getExpenseCategoriesForForm();
    public function createWithoutEvents($data);
    public function getTotalByCategoryType($categoryType, array $filter): array;
}