<?php

namespace Database\Seeders;

use App\Services\CardService;
use Illuminate\Database\Seeder;

class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $service = app(CardService::class);
        $jon = auth()->user();

        $data = [
            'name'          => 'Mastercard',
            'pay_day'       => 10,
            'closing_day'   => 2,
            'credit_limit'  => 5000,
            'user_id'       => $jon->id
        ];

        $service->create($data);
    }
}
