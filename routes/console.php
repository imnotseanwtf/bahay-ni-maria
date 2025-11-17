<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule batched alert processing every 30 seconds
Schedule::command('alerts:process-batch')
    ->everyThirtySeconds()
    ->withoutOverlapping()
    ->runInBackground();
