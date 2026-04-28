<?php

use App\Services\SubscriptionService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('subscriptions:renew', function (SubscriptionService $subscriptionService) {
    $processed = $subscriptionService->renewDueSubscriptions();

    $this->info("Processed {$processed} subscription renewal(s).");
})->purpose('Process due subscription renewals');

Schedule::command('subscriptions:renew')
    ->dailyAt('01:00')
    ->withoutOverlapping();
