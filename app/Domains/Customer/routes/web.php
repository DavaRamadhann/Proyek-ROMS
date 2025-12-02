<?php

use App\Domains\Customer\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::prefix('cs/pelanggan')->name('customers.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::post('/', [CustomerController::class, 'store'])->name('store');
    Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
    Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
});
