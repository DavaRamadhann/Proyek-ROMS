<?php

use App\Domains\Reminder\Http\Controllers\ReminderController;
use Illuminate\Support\Facades\Route;

Route::prefix('cs/reminder')->name('reminder.')->group(function () {
    Route::get('/', [ReminderController::class, 'index'])->name('index');
    Route::get('/create', [ReminderController::class, 'create'])->name('create');
    Route::post('/', [ReminderController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ReminderController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ReminderController::class, 'update'])->name('update');
    Route::delete('/{id}', [ReminderController::class, 'destroy'])->name('destroy');
});
