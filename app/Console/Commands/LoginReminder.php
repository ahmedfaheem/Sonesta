<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\LoginReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LoginReminder extends Command
{
    protected $signature = 'login:reminder';

    protected $description = 'Send reminder emails to inactive client users.';

    public function handle(): int
    {
        $threshold = now()->subMonth()->getTimestamp();

        $activeClientIds = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $threshold)
            ->distinct()
            ->pluck('user_id')
            ->filter()
            ->values()
            ->all();

        $clients = User::query()
            ->role('client')
            ->whereHas('clientProfile', fn ($query) => $query->where('is_approved', true))
            ->when($activeClientIds, fn ($query, $ids) => $query->whereNotIn('id', $ids))
            ->get();

        if ($clients->isEmpty()) {
            $this->info('No inactive clients found.');
            return self::SUCCESS;
        }

        $clients->each(fn (User $client) => $client->notify(new LoginReminderNotification()));

        $this->info('Queued login reminders for '.$clients->count().' clients.');

        return self::SUCCESS;
    }
}
