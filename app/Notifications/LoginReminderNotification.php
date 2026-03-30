<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected string $period = 'a month')
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We miss you at Sonesta')
            ->greeting("Hello {$notifiable->name},")
            ->line("We noticed you haven't logged in for {$this->period}.")
            ->line('There are great rooms waiting for you, and your next reservation is only a few clicks away.')
            ->action('Return to Sonesta', url('/'))
            ->line('We hope to see you back soon!');
    }
}
