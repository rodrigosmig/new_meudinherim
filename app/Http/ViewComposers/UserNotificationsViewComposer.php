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
        $view->with([
            'count' => count($this->notifications),
            'unread_notifications' => $this->notifications,
        ]);
    }
}