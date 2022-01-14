<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use App\Services\ProfileService;
use App\Notifications\AccountReceivableNotification;
use App\Repositories\Interfaces\ParcelRepositoryInterface;
use App\Notifications\AccountReceivableDatabaseNotification;
use App\Repositories\Interfaces\AccountsSchedulingRepositoryInterface;

class ReceivablesCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:receivable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies users if there is an account receivable on the current date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userService            = app(ProfileService::class);
        $receivableRepository   = app(AccountsSchedulingRepositoryInterface::class);
        $parcelRepository       = app(ParcelRepositoryInterface::class);
        
        $users = $userService->getUsersForNotification();

        foreach ($users as $user) {
            $receivables = $receivableRepository->getAccountsByUserForCron($user, Category::INCOME);
            $parcels    = $parcelRepository->getParcelsOfAccountsSchedulingForCron($user, Category::INCOME);

            $all_accounts = $receivables->concat($parcels);
            
            if ($all_accounts->count() > 0) {
                $user->notify(new AccountReceivableNotification($all_accounts));

                foreach ($all_accounts as $receivable) {
                    $user->notify(new AccountReceivableDatabaseNotification($receivable));
                }
            }
        }
    }
}
