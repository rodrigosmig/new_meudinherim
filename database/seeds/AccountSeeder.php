<?php

use App\Models\User;
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
        $jon = auth()->user();

        $jon->accounts()->create([
            'name'      => __('global.money'),
            'type'      => Account::MONEY,
        ]);

        $jon->accounts()->create([
            'name'      => 'Nubank',
            'type'      => Account::CHECKING_ACCOUNT,
        ]);
    }
}
