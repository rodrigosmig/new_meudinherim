<?php

use App\Services\CardService;
use Illuminate\Database\Seeder;
use App\Services\InvoiceEntryService;

class InvoiceEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $entryservice   = app(InvoiceEntryService::class);
        $cardService    = app(CardService::class);
        $jon            = auth()->user();
        $jon_card       = $jon->cards()->first();

        $data = [
            'date'          => now()->modify('-1 days')->format('Y-m-d'),
            'description'   => 'Lunch',
            'value'         => 22.50,
            'category_id'   => $jon->categories()->where('type', 2)->first()->id,
            'user_id'       => $jon->id,
        ];

        $entryservice->make($jon_card, $data);

        $data = [
            'date'          => now()->format('Y-m-d'),
            'description'   => 'Hotel',
            'value'         => 80,
            'category_id'   => $jon->categories()->where('type', 2)->first()->id,
            'user_id'       => $jon->id,
        ];

        $entryservice->make($jon_card, $data);

        $cardService->updateCardBalance($jon_card);
    }
}
