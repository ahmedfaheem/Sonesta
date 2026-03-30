<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Sonesta account has been approved')
            ->greeting("Welcome {$notifiable->name},")
            ->line('Your client account has been approved successfully.')
            ->line('You can now log in and start booking rooms.')
            ->action('Go to Sonesta', url('/'))
            ->line('Thank you for choosing Sonesta.');
    }
}
