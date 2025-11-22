<?php

use App\Domains\Automation\Http\Controllers\AutomationController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin/otomasi-pesan')->name('admin.otomasi-pesan')->group(function () {
    Route::get('/', [AutomationController::class, 'index']); // name will be admin.otomasi-pesan
    Route::get('/tambah', [AutomationController::class, 'create'])->name('.tambah'); // name: admin.otomasi-pesan.tambah
    Route::post('/', [AutomationController::class, 'store'])->name('.store');
});
