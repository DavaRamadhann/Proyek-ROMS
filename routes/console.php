<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler for reminder command
Schedule::command('reminders:send')
    ->everyMinute()
    ->description('Kirim reminder pending setiap menit');

// Scheduler for broadcast command
Schedule::command('broadcast:process-scheduled')->everyMinute();
