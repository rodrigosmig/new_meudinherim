<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountsSchedulingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'due_date'      => now()->format('Y-m-d'),
            'description'   => $this->faker->sentence,
            'value'         => 100,
            'category_id'   => Category::factory(),
            'user_id'       => User::factory()
        ];
    }
}
