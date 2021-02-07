<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Account;
use Faker\Generator as Faker;

$factory->define(Account::class, function (Faker $faker) {
    return [
        'name'      => $faker->name,
        'type'      => $faker->randomElement(Account::TYPES),
        'user_id'   => factory(User::class)
    ];
});
