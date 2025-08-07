<?php

namespace App\Domain\Payment\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletFundedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly Model $walletTransaction)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $wallet = $this->walletTransaction->wallet;

        return (new MailMessage)
            ->subject('Your '.$wallet->currency.' wallet was successfully funded')
            ->greeting('Hello '.$wallet->user->firstname)
            ->line('We\'re happy to let you know your '.$wallet->currency.' wallet has been funded with '.$this->walletTransaction->amount.' '.$wallet->currency.'.')
            ->line('Thank you for shopping with us!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
