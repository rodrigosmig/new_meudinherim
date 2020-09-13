<?php

use App\Models\User;
use App\Models\AccountEntry;
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

        $jon_account = $jon->accounts()->where('type', 'checking_account')->first();

        $jon_account->entries()->create([
            'date'          => now()->modify("-1 days")->format('Y-m-d'),
            'description'   => 'Salary',
            'value'         => 1000,
            'category_id'   => $jon->categories()->where('type', 1)->first()->id,
            'user_id'       => $jon->id,
        ]);
        
        $jon_account->entries()->create([
            'date'          => now()->format('Y-m-d'),
            'description'   => 'Lunch',
            'value'         => 25.50,
            'category_id'   => $jon->categories()->where('type', 2)->first()->id,
            'user_id'       => $jon->id,
        ]);
    }
}
