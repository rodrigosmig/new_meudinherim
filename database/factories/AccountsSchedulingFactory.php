<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Category;
use Faker\Generator as Faker;
use App\Models\AccountsScheduling;

$factory->define(AccountsScheduling::class, function (Faker $faker) {
    return [
        'due_date'      => now()->format('Y-m-d'),
        'description'   => $faker->sentence,
        'value'         => 100,
        'category_id'   => factory(Category::class),
        'user_id'       => factory(User::class)
    ];
});
