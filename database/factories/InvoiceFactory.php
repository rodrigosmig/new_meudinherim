<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Card;
use App\Models\User;
use App\Models\Invoice;
use Faker\Generator as Faker;

$factory->define(Invoice::class, function (Faker $faker) {
    return [
        'due_date'      => now()->modify('+8 days')->format('Y-m-d'),
        'closing_date'  => now()->modify('+2 days')->format('Y-m-d'),
        'amount'        => 2000,
        'card_id'       => factory(Card::class),
        'user_id'       => factory(User::class)
    ];
});
