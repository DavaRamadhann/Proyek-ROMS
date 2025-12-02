<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Scheduler for reminder command
Schedule::command('reminders:send')
    ->dailyAt(config('reminder.schedule_time'))
    ->description('Kirim reminder pending setiap hari jam '.config('reminder.schedule_time'));
