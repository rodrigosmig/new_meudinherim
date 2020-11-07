<?php

namespace App\Console\Commands;

use App\Models\Category;
use Illuminate\Console\Command;
use App\Services\ProfileService;
use App\Services\AccountsSchedulingService;
use App\Notifications\AccountPayableNotification;

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
    protected $description = 'Command description';

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
        $payableService = app(AccountsSchedulingService::class);
        $userService    = app(ProfileService::class);
        
        $users = $userService->getUsersForNotification();

        foreach ($users as $user) {
            $payables = $payableService->getAccountsByUserForCron($user, Category::EXPENSE);
            
            if ($payables->count() > 0) {
                $user->notify(new AccountPayableNotification($payables));
            }
        }
        
    }
}
