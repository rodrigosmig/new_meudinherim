<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Services\InvoiceService;


class UserNotificationsViewComposer
{
    /**
     * The card repository implementation.
     *
     * @var array
     */
    protected $notifications;

    /**
     * Create a new cards composer.
     *
     * @param  InvoiceService  $service
     * @return void
     */
    public function __construct()
    {
        $this->notifications = auth()->user()->unreadNotifications;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $count = 0;
        
        foreach ($this->notifications as $notification) {
            foreach ($notification->data as $key => $data) {
                $count += count($data);
            }
            
        }

        $view->with([
            'count' => $count,
            'unread_notifications' => $this->notifications,
        ]);
    }
}