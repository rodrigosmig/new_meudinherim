<?php

namespace App\Providers;

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
            
            $service    = app(AccountService::class);
            $accounts   = $service->getAccounts();
            $items      = [];

            foreach ($accounts as $account) {
                $item = [
                    'text' => $account->name,
                    'url'  => route('accounts.entries', $account->id),
                    'label_color' => 'success',
                    'active' => ["accounts/{$account->id}/entries/*"]
                ];
                
                $items[] = $item;
            }

            $event->menu->addIn('accounts', [
                'key'     => 'extract',
                'text'    => 'extract',
                'icon'    => 'fas fa-money-check-alt',
                'submenu' => $items,
            ],);

        });
    }
}
