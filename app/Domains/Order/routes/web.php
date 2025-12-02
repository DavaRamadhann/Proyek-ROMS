<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Order\Http\Controllers\OrderController;

Route::middleware(['auth'])->group(function () {
    Route::resource('orders', OrderController::class);
Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
});
