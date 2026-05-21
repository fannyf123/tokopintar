<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('tokopintar:recompute-insight')->dailyAt('02:00');
Schedule::command('tokopintar:check-expiry')->dailyAt('07:00');
Schedule::command('tokopintar:recompute-customer')->dailyAt('02:30');
Schedule::command('tokopintar:recompute-association')->dailyAt('03:00');
Schedule::command('tokopintar:detect-anomaly')->everySixHours();
