<?php

namespace App\Providers;

use App\Services\CardService;
use App\Services\AccountService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            
            $accountService = app(AccountService::class);
            $accounts       = $accountService->getAccounts();            
            $account_items  = [];

            foreach ($accounts as $account) {
                $item = [
                    'text' => $account->name,
                    'url'  => route('accounts.entries', $account->id),
                    'label_color' => 'success',
                    'active' => ["accounts/{$account->id}/entries/*"]
                ];
                
                $account_items[] = $item;
            }

            if ($accounts->isNotEmpty()) {
                $event->menu->addIn('accounts', [
                    'key'     => 'extract',
                    'text'    => 'extract',
                    'icon'    => 'fas fa-money-check-alt',
                    'submenu' => $account_items,
                ],);
            }

            $cardService    = app(CardService::class);
            $cards          = $cardService->getCards();
            $card_items     = [];

            foreach ($cards as $card) {
                $item = [
                    'text' => $card->name,
                    'url'  => route('cards.invoices.index', $card->id),
                    'label_color' => 'success',
                    'active' => ["cards/{$card->id}/invoices/*"]
                ];
                
                $card_items[] = $item;
            }

            if ($cards->isNotEmpty()) {
                $event->menu->addIn('cards', [
                    'key'     => 'invoices',
                    'text'    => 'invoices',
                    'icon'    => 'fas fa-money-check-alt',
                    'submenu' => $card_items,
                ],);
            }
        });
    }
}
