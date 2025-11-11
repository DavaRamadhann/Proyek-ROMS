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