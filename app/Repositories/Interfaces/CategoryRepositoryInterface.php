<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function getCategoriesByType(string $type, int $per_page);
    public function getAllCategories(int $per_page);
    public function getIncomeCategoriesForForm();
    public function getExpenseCategoriesForForm();
    public function createWithoutEvents($data);
    public function getInvoiceEntriesByCategoryType($categoryType, array $filter): array;
}