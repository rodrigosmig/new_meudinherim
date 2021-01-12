<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Account;
use App\Models\AccountEntry;
use Faker\Generator as Faker;

$factory->define(AccountEntry::class, function (Faker $faker) {
    return [
        'date'          => $faker->name,
        'description'   => $faker->randomElement(Account::TYPES),
        'value'         => 10,
        'category_id'   => factory(User::class),
        'account_id'    => factory(Account::class)
    ];
});
