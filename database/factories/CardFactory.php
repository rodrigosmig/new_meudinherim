<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'          => $this->faker->name,
            'pay_day'       => '10',
            'closing_day'   => '3',
            'credit_limit'  => 5000,
            'balance'       => 5000,
            'user_id'       => User::factory()
        ];
    }
}
