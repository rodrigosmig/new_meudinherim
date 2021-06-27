<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\Parcel;
use App\Models\Invoice;
use App\Models\Category;
use App\Models\InvoiceEntry;
use Faker\Generator as Faker;

$factory->define(Parcel::class, function (Faker $faker) {
    return [
        'date'              => now()->format('Y-m-d'),
        'description'       => $faker->sentence,
        'value'             => 1000,
        'parcelable_type'   => InvoiceEntry::class,
        'parcelable_id'     => factory(InvoiceEntry::class),
        'category_id'       => factory(Category::class),
        'invoice_id'        => factory(Invoice::class),
        'user_id'           => factory(User::class),
        'anticipated'       => false
    ];
});
