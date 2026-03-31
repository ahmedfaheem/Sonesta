<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\LoginReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class LoginReminder extends Command
{
    protected $signature = 'login:reminder';

    protected $description = 'Send a reminder email to users inactive for 30 days.';

    public function handle(): int
    {
        $threshold = now()->subDays(30);

        $users = User::query()
            ->where('updated_at', '<=', $threshold)
            ->get();

        if ($users->isEmpty()) {
            $this->info('No inactive users found.');
            return self::SUCCESS;
        }

        Notification::send($users, new LoginReminderNotification());

        $this->info('Sent login reminders to '.$users->count().' users.');

        return self::SUCCESS;
    }
}
