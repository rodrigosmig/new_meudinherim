<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Card;
use App\Models\User;
use Faker\Generator as Faker;

$factory->define(Card::class, function (Faker $faker) {
    return [
        'name'          => $faker->name,
        'pay_day'       => '10',
        'closing_day'   => '3',
        'credit_limit'  => 5000,
        'balance'       => 5000,
        'user_id'       => factory(User::class)
    ];
});
