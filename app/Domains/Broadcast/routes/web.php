<?php

use Illuminate\Support\Facades\Route;
use App\Domains\Broadcast\Http\Controllers\BroadcastController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/otomasi-pesan', [BroadcastController::class, 'index'])->name('broadcast.index');
    Route::get('/otomasi-pesan/create', [BroadcastController::class, 'create'])->name('broadcast.create');
    Route::post('/otomasi-pesan', [BroadcastController::class, 'store'])->name('broadcast.store');
    Route::get('/otomasi-pesan/{broadcast}', [BroadcastController::class, 'show'])->name('broadcast.show');
});
