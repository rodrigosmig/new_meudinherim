<?php

use App\Models\Category;
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
        Category::create([
            'name'      => 'Salário',
            'type'      => Category::INCOME,
            'user_id'   => 1,
        ]);

        Category::create([
            'name'      => 'Vendas',
            'type'      => Category::INCOME,
            'user_id'   => 1,
        ]);

        Category::create([
            'name'      => 'Aluguel',
            'type'      => Category::EXPENSE,
            'user_id'   => 1,
        ]);

        Category::create([
            'name'      => 'Despesas',
            'type'      => Category::EXPENSE,
            'user_id'   => 1,
        ]);

        Category::create([
            'name'      => 'Vendas',
            'type'      => Category::INCOME,
            'user_id'   => 2,
        ]);

        Category::create([
            'name'      => 'Despesas',
            'type'      => Category::EXPENSE,
            'user_id'   => 2,
        ]);
    }
}
