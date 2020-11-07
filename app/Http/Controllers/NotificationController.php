<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AccountsSchedulingService;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function show($notification_id, $account_id)
    {
        $notification           = DatabaseNotification::find($notification_id);
        $accountScheduleService = app(AccountsSchedulingService::class);        

        if ($notification) {
            $notification->markAsRead();
            $account = $accountScheduleService->findById($account_id);
            
            if ($account->category->isExpense()) {
                return redirect()->route('payables.show', $account->id);
            }
            
            return redirect()->route('receivables.show', $account);
        }

        return redirect()->back();
    }

    public function all_read()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }
}
