<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date'          => now()->format('Y-m-d'),
            'description'   => $this->faker->randomElement(Account::TYPES),
            'value'         => 10,
            'category_id'   => Category::factory(),
            'account_id'    => Account::factory(),
            'user_id'       => User::factory()
        ];
    }
}
