<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
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
        return (new MailMessage)
            ->subject('Your account has been approved')
            ->greeting("Hello {$notifiable->name},")
            ->line('Great news — your client account has been approved by our team.')
            ->line('You can now log in and start booking rooms immediately.')
            ->action('Login to Sonesta', url('/login'))
            ->line('Thank you for choosing Sonesta.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your account has been approved.',
        ];
    }
}
