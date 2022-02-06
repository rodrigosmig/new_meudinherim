<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'due_date'      => now()->modify('+8 days')->format('Y-m-d'),
            'closing_date'  => now()->modify('+2 days')->format('Y-m-d'),
            'amount'        => 2000,
            'card_id'       => Card::factory(),
            'user_id'       => User::factory()
        ];
    }
}
