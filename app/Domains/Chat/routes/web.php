<?php
// app/Domains/Chat/routes/web.php

use Illuminate\Support\Facades\Route;
use App\Domains\Chat\Http\Controllers\ChatController;
use App\Domains\Chat\Http\Controllers\WhatsAppConnectionController;

/*
|--------------------------------------------------------------------------
| Rute Web Domain Chat
|--------------------------------------------------------------------------
*/

Route::get('/chat', [ChatController::class, 'index'])
    ->name('chat.index'); // -> /app/chat

// ====================================================================
// [ROUTE BARU]
// =V==================================================================
// Rute untuk UI 3-kolom ala WhatsApp
Route::get('/chat/whatsapp', [ChatController::class, 'showWhatsAppUI'])
    ->name('chat.whatsapp'); // -> /app/chat/whatsapp

// ====================================================================
// [ROUTE BARU UNTUK AJAX]
// ====================================================================
// Rute GET untuk mengambil data room (JSON)
Route::get('/chat/room/{roomId}/data', [ChatController::class, 'getRoomData'])
    ->where('roomId', '[0-9]+')
    ->name('chat.room.data'); // -> GET /app/chat/room/1/data

// Rute POST untuk mengirim pesan (JSON)
Route::post('/chat/room/{roomId}/send-ajax', [ChatController::class, 'storeAjaxMessage'])
    ->where('roomId', '[0-9]+')
    ->name('chat.room.send-ajax'); // -> POST /app/chat/room/1/send-ajax

Route::get('/chat/{roomId}', [ChatController::class, 'show'])
    ->where('roomId', '[0-9]+')
    ->name('chat.show'); // -> /app/chat/1

Route::post('/chat/{roomId}/send', [ChatController::class, 'storeMessage'])
    ->where('roomId', '[0-9]+')
    ->name('chat.store'); // -> POST /app/chat/1/send

/**
 * Route untuk Manajemen Koneksi WhatsApp oleh Admin.
 * * Kita gunakan middleware 'role:admin' untuk memastikan
 * hanya admin yang bisa mengakses pengaturan koneksi ini.
 *
 */
Route::middleware(['auth', 'role:admin'])->prefix('admin/whatsapp')->group(function () {
    
    // Halaman UI untuk menampilkan status dan QR Code
    Route::get('/connection', function () {
        // Arahkan ke view yang akan kita buat nanti
        return view('pages.chat.whatsapp'); //
    })->name('admin.whatsapp.connection');

    // API Internal untuk dipanggil oleh JavaScript di halaman admin
    Route::prefix('api')->name('admin.whatsapp.api.')->group(function () {
        Route::get('/status', [WhatsAppConnectionController::class, 'getStatus'])->name('status');
        Route::get('/qr', [WhatsAppConnectionController::class, 'getQrCode'])->name('qr');
        Route::post('/reconnect', [WhatsAppConnectionController::class, 'requestReconnect'])->name('reconnect');
    });
});