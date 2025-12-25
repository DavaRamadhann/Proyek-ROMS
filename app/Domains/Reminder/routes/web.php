<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Reminder\Http\Controllers\ReminderController;

Route::middleware(['role:admin'])->group(function () {
    Route::get('/reminders/sync', [ReminderController::class, 'syncAndFetch'])->name('reminders.sync');
    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::get('/reminders/create', [ReminderController::class, 'create'])->name('reminders.create');
    Route::post('/reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::get('/reminders/{reminder}/edit', [ReminderController::class, 'edit'])->name('reminders.edit');
    Route::put('/reminders/{reminder}', [ReminderController::class, 'update'])->name('reminders.update');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');
});
