<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('login:reminder', function () {
    return app(\App\Console\Commands\LoginReminder::class)->handle();
})->purpose('Send a login reminder email to inactive clients');
