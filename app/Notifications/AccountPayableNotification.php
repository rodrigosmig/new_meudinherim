<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountPayableNotification extends Notification
{
    use Queueable;

    private $payables;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($payables)
    {
        $this->payables = $payables;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
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
            ->greeting(__('messages.mail.payable_due_in') . now()->format('d/m/Y'))
            ->subject(now()->format('d/m/Y'));

        foreach ($this->payables as $payable) {
            $mail->line(__('global.account') . ": " . $payable->description . " - " . __('global.value') . ": " . toBrMoney($payable->value));
        }

        $mail->action(__("global.view_accounts"), route('payables.index'));

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

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'payables' => $this->payables
        ];
    }
}
