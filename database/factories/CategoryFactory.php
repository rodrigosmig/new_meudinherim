<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'      => $this->faker->name,
            'type'      => $this->faker->randomElement([Category::EXPENSE, Category::INCOME]),
            'user_id'   => (User::factory())
        ];
    }
}

