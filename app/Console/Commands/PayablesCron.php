<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use App\Services\ProfileService;
use App\Notifications\AccountPayableNotification;
use App\Repositories\Core\Eloquent\ParcelRepository;
use App\Notifications\AccountPayableDatabaseNotification;
use App\Repositories\Interfaces\ParcelRepositoryInterface;
use App\Repositories\Interfaces\AccountsSchedulingRepositoryInterface;

class PayablesCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:payable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies users if there is an account payable on the current date';

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
        $userService        = app(ProfileService::class);
        $payableRepository  = app(AccountsSchedulingRepositoryInterface::class);
        $parcelRepository   = app(ParcelRepositoryInterface::class);
        
        $users = $userService->getUsersForNotification();

        foreach ($users as $user) {
            $payables   = $payableRepository->getAccountsByUserForCron($user, Category::EXPENSE);
            $parcels    = $parcelRepository->getParcelsOfAccountsSchedulingForCron($user, Category::EXPENSE);

            $all_accounts = $payables->concat($parcels);

            if ($all_accounts->count() > 0) {
                $user->notify(new AccountPayableNotification($all_accounts));

                foreach ($all_accounts as $payable) {
                    $user->notify(new AccountPayableDatabaseNotification($payable));
                }
            }
        }
        
    }
}
