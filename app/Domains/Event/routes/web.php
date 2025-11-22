<?php

use App\Domains\Event\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/daftar-acara')->name('admin.daftar-acara')->group(function () {
    Route::get('/', [EventController::class, 'index']); // name: admin.daftar-acara
    Route::get('/tambah', [EventController::class, 'create'])->name('.tambah'); // name: admin.daftar-acara.tambah
    Route::post('/', [EventController::class, 'store'])->name('.store');
});
