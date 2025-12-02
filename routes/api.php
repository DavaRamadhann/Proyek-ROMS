<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ini adalah file API utama yang akan memuat rute API dari Domain.
|
*/

// Rute auth sanctum bawaan (jika ada)
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// ===================================================================
// INI BAGIAN PALING PENTING
// Memuat file rute API dari Domain Chat yang sudah kita buat
// ===================================================================
require __DIR__.'/../app/Domains/Chat/routes/api.php';

// ===================================================================
// ORDER API ROUTES (Epik 1.4)
// ===================================================================
Route::prefix('v1')->group(function () {
    // TODO: Tambahkan middleware auth (misal: 'auth:sanctum' atau custom key)
    // Untuk tahap awal development, kita buka dulu atau pakai simple key check di controller jika perlu
    Route::post('/orders', [\App\Domains\Order\Http\Controllers\Api\OrderApiController::class, 'store']);
});