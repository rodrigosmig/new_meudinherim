<?php

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\CategoryService;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryServiceTest extends TestCase 
{
	protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->categoryService = new CategoryService($this->categoryRepository);
    }

	//https://stackoverflow.com/questions/29557388/laravel-4-repository-pattern-testing-with-phpunit-and-mockery
	//https://laracasts.com/discuss/channels/testing/testing-repository-pattern-with-mockery
	//https://stackoverflow.com/questions/34681430/laravel-5-2-unit-testing-repository-with-mocking-does-not-have-method
}