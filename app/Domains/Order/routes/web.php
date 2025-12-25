<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Order\Http\Controllers\OrderController;

Route::middleware(['auth'])->group(function () {
    // Import Routes
    Route::get('orders/action/import', [OrderController::class, 'importForm'])->name('orders.import.form');
    Route::post('orders/action/import', [OrderController::class, 'import'])->name('orders.import');
    Route::get('orders/action/download-template', [OrderController::class, 'downloadTemplate'])->name('orders.download-template');
    
    Route::resource('orders', OrderController::class);
    Route::post('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
});
