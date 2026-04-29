<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscriptions:renew')->daily()->withoutOverlapping();
Schedule::command('delivery:check-missing')->daily()->withoutOverlapping();
Schedule::command('notifications:process-queued')->everyFiveMinutes()->withoutOverlapping();
Schedule::command('lifecycle:sync-time-based')->hourly()->withoutOverlapping();
