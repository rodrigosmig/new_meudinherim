<?php

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Account::create([
            'name'      => 'Dinheiro',
            'type'      => Account::MONEY,
            'user_id'   => 1,
        ]);

        Account::create([
            'name'      => 'Nubank',
            'type'      => Account::CHECKING_ACCOUNT,
            'user_id'   => 1,
        ]);

        Account::create([
            'name'      => 'Dinheiro',
            'type'      => Account::MONEY,
            'user_id'   => 2,
        ]);
    }
}
