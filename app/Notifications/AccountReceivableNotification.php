<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountReceivableNotification extends Notification
{
    use Queueable;

    private $receivables;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($receivables)
    {
        $this->receivables = $receivables;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->greeting(__('messages.mail.receivable_due_in') . now()->format('d/m/Y'))
            ->subject(now()->format('d/m/Y'));

        foreach ($this->receivables as $receivable) {
            $mail->line(__('global.account') . ": " . $receivable->description . " - " . __('global.value') . ": " . toBrMoney($receivable->value));
        }

        $mail->action(__("global.view_accounts"), route('receivables.index'));

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
