<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function create(array $data);
    public function update($category, array $data);
    public function delete($category);
    public function findById($id);
    public function getCategories(array $filter, int $per_page);
    public function createWithoutEvents($data);
    public function getInvoiceEntriesByCategoryType($categoryType, array $filter): array;
}