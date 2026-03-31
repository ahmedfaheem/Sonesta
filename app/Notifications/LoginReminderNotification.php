<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginReminderNotification extends Notification implements ShouldQueue
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
            ->subject('We miss you at Sonesta')
            ->greeting("Hello {$notifiable->name},")
            ->line('It looks like you have not signed in for over 30 days.')
            ->line('We would love to welcome you back to review rooms and manage reservations.')
            ->action('Login now', url('/login'))
            ->line('If you have any questions, feel free to contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Reminder to log in after 30 days of inactivity.',
        ];
    }
}
