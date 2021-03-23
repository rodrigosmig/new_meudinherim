<?php

use App\Models\User;
use App\Services\CardService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

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
