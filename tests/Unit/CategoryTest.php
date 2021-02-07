<?php

namespace Tests\Unit;

use Tests\TestCase;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CategoryTest extends TestCase
{
    public function testIsExpense()
    {
        $category = new Category();
        $category->type = Category::INCOME;

        $this->assertFalse($category->isExpense());

        $category->type = Category::EXPENSE;
        $this->assertTrue($category->isExpense());

        $category->type = 'Fake category';
        $this->assertFalse($category->isExpense());
    }
}
