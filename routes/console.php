<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule 5-Minute Automated Mobile Banking Sync for registered user phone numbers
Schedule::command('expense:sync-mobile-banking')->everyFiveMinutes();
