<?php

use App\Models\User;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $service = app(CategoryService::class);
        $service->createDefaultCategories();
        
        /* $jon->categories()->create([
            'name'      => __('global.incomes'),
            'type'      => Category::INCOME,
        ]);

        $jon->categories()->create([
            'name'      => __('global.expenses'),
            'type'      => Category::EXPENSE,
        ]); */
    }
}
