<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(AccountSeeder::class);
        $this->call(AccountEntrySeeder::class);
        $this->call(CardSeeder::class);
        $this->call(InvoiceEntrySeeder::class);
        $this->call(AccountSchedulingSeeder::class);
    }
}
