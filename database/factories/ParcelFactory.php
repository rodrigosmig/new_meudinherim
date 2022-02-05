<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Invoice;
use App\Models\Category;
use App\Models\InvoiceEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParcelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date'              => now()->format('Y-m-d'),
            'description'       => $this->faker->sentence,
            'value'             => 1000,
            'parcelable_type'   => InvoiceEntry::class,
            'parcelable_id'     => InvoiceEntry::factory(),
            'category_id'       => Category::factory(),
            'invoice_id'        => Invoice::factory(),
            'user_id'           => User::factory(),
            'anticipated'       => false
        ];
    }
}
