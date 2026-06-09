<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscription:renew-daily')->dailyAt('00:05')->timezone('Asia/Kolkata');
Schedule::command('subscription:expire')->dailyAt('00:10')->timezone('Asia/Kolkata');
Schedule::command('predictions:generate-daily')->dailyAt('04:00')->timezone('Asia/Kolkata');
Schedule::command('dasha:send-alerts')->dailyAt('08:00')->timezone('Asia/Kolkata');
