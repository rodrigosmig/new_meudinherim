<?php

use Illuminate\Database\Seeder;
use App\Services\AccountsSchedulingService;

class AccountSchedulingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $service        = app(AccountsSchedulingService::class);
        $jon            = auth()->user();
        $jon_card       = $jon->cards()->first();

        $data = [
            'due_date'      => now()->modify('+5 days')->format('Y-m-d'),
            'description'   => 'Gas',
            'value'         => 50,
            'category_id'   => $jon->categories()->find(10)->id,
            'user_id'       => $jon->id,
            'monthly'       => true
        ];

        $service->store($data);

        $data = [
            'due_date'      => now()->modify('8 days')->format('Y-m-d'),
            'description'   => 'Rent',
            'value'         => 230,
            'category_id'   => $jon->categories()->find(10)->id,
            'user_id'       => $jon->id,
            'monthly'       => true
        ];

        $service->store($data);

        $data = [
            'due_date'      => now()->modify('10 days')->format('Y-m-d'),
            'description'   => 'Health plan',
            'value'         => 300,
            'category_id'   => $jon->categories()->find(10)->id,
            'user_id'       => $jon->id,
            'monthly'       => true
        ];

        $service->store($data);

        $data = [
            'due_date'      => now()->modify('10 days')->format('Y-m-d'),
            'description'   => 'Supermarket',
            'value'         => 150,
            'category_id'   => $jon->categories()->find(10)->id,
            'user_id'       => $jon->id,
            'monthly'       => true
        ];

        $service->store($data);

        $data = [
            'due_date'      => now()->modify('10 days')->format('Y-m-d'),
            'description'   => 'PlayStation Sale',
            'value'         => 400,
            'category_id'   => $jon->categories()->find(8)->id,
            'user_id'       => $jon->id,
            'monthly'       => true
        ];

        $service->store($data);

        $data = [
            'due_date'      => now()->modify('10 days')->format('Y-m-d'),
            'description'   => 'Freelance',
            'value'         => 2500,
            'category_id'   => $jon->categories()->find(1)->id,
            'user_id'       => $jon->id,
            'monthly'       => true
        ];

        $service->store($data);
    }
}
