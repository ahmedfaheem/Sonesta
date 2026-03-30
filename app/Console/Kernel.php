<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, string>
     */
    protected array $commands = [
        \App\Console\Commands\LoginReminder::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('login:reminder')->daily();
    }

    protected function commands(): void
    {
        require __DIR__.'/../../routes/console.php';
    }
}
