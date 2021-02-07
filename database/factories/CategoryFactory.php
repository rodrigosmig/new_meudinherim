<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name'      => $faker->name,
        'type'      => $faker->randomElement([Category::EXPENSE, Category::INCOME]),
        'user_id'   => factory(User::class)
    ];
});
