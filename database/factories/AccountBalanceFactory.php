<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountBalanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date'              => now()->format('Y-m-d'),
            'previous_balance'  => 100,
            'current_balance'   => 200,
            'account_id'        => Account::factory(),
            'user_id'           => User::factory()
        ];
    }
}
