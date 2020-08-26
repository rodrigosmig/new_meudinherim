<?php

use App\Models\AccountEntry;
use Illuminate\Database\Seeder;

class AccountEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AccountEntry::create([
            'date'          => now()->modify("-3 days")->format('Y-m-d'),
            'description'   => 'Vendas',
            'value'         => 1500,
            'category_id'   => 2,
            'account_id'    => 2,
            'user_id'       => 1,
        ]);
        
        AccountEntry::create([
            'date'          => now()->modify("-2 days")->format('Y-m-d'),
            'description'   => 'Almoço',
            'value'         => 20.50,
            'category_id'   => 4,
            'account_id'    => 2,
            'user_id'       => 1,
        ]);

        AccountEntry::create([
            'date'          => now()->modify("-1 days")->format('Y-m-d'),
            'description'   => 'Jantar',
            'value'         => 45.25,
            'category_id'   => 4,
            'account_id'    => 2,
            'user_id'       => 1,
        ]);

        AccountEntry::create([
            'date'          => now()->format('Y-m-d'),
            'description'   => 'Combustível',
            'value'         => 50,
            'category_id'   => 4,
            'account_id'    => 2,
            'user_id'       => 1,
        ]);

        AccountEntry::create([
            'date'          => now()->modify("-1 days")->format('Y-m-d'),
            'description'   => 'Vendas',
            'value'         => 50,
            'category_id'   => 2,
            'account_id'    => 1,
            'user_id'       => 1,
        ]);
    }
}
