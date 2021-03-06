<?php

use App\Services\AccountService;
use Illuminate\Database\Seeder;

class AccountEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jon = auth()->user();

        $service = app(AccountService::class);

        $jon_account = $jon->accounts()->where('type', 'checking_account')->first();

        $money = $jon->accounts()->where('type', 'money')->first();

        $money_entry = $money->entries()->create([
            'date'          => now()->format('Y-m-d'),
            'description'   => 'Loan returned',
            'value'         => 550,
            'category_id'   => $jon->categories()->find(4)->id,
            'user_id'       => $jon->id,
        ]);

        $entry1 = $jon_account->entries()->create([
            'date'          => now()->modify("-4 days")->format('Y-m-d'),
            'description'   => 'Salary',
            'value'         => 5000,
            'category_id'   => $jon->categories()->find(1)->id,
            'user_id'       => $jon->id,
        ]);

        $entry2 = $jon_account->entries()->create([
            'date'          => now()->modify("-3 days")->format('Y-m-d'),
            'description'   => 'Laptop Sale',
            'value'         => 1000,
            'category_id'   => $jon->categories()->find(8)->id,
            'user_id'       => $jon->id,
        ]);
        
        $entry3 = $jon_account->entries()->create([
            'date'          => now()->modify("-2 days")->format('Y-m-d'),
            'description'   => 'Lunch',
            'value'         => 25.50,
            'category_id'   => $jon->categories()->find(17)->id,
            'user_id'       => $jon->id,
        ]);

        $entry4 = $jon_account->entries()->create([
            'date'          => now()->modify("-2 days")->format('Y-m-d'),
            'description'   => 'Dinner',
            'value'         => 25.50,
            'category_id'   => $jon->categories()->find(17)->id,
            'user_id'       => $jon->id,
        ]);

        $entry5 = $jon_account->entries()->create([
            'date'          => now()->modify("-1 days")->format('Y-m-d'),
            'description'   => 'Iphone',
            'value'         => 4500,
            'category_id'   => $jon->categories()->find(15)->id,
            'user_id'       => $jon->id,
        ]);

        $service->updateBalance($entry5->account, $entry1->date);
        $service->updateBalance($money_entry->account, $money_entry->date);
    }
}
