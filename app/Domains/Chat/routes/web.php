<?php

use App\Domains\Chat\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Rute Domain Chat
// Prefix 'app' dan middleware 'role:admin,cs' sudah diterapkan di routes/web.php utama saat file ini di-require.
// Namun, untuk keamanan ganda atau jika file ini dipakai mandiri, kita bisa mendefinisikan grup lagi atau langsung rutenya.
// Karena di routes/web.php sudah ada grup, kita cukup mendefinisikan rutenya saja.
// TAPI: Di file domain lain (Customer, Product, dll), kita mendefinisikan Route::prefix(...)->group(...).
// Agar konsisten dan aman, kita definisikan rute secara eksplisit.

Route::group([], function () {

    // 1. Dashboard Statistik CS
    Route::get('/cs/dashboard', [ChatController::class, 'index'])
        ->name('chat.dashboard');

    // 2. Pintu Masuk: Halaman Koneksi & QR
    Route::get('/chat/connect', [ChatController::class, 'showConnectionPage'])
        ->name('chat.connect');

    // 3. Halaman Utama: UI Chat (Aplikasi Chat 3 Kolom)
    Route::get('/chat/ui', [ChatController::class, 'showChatUI'])
        ->name('chat.ui');

    // 3b. Halaman percakapan untuk room tertentu
    Route::get('/chat/room/{roomId}', [ChatController::class, 'show'])
        ->name('chat.show');
    
    // 3c. Route untuk form submission (non-AJAX)
    Route::post('/chat/room/{roomId}/store', [ChatController::class, 'storeMessage'])
        ->name('chat.store');

    // 4. AJAX Endpoints (Data & Kirim Pesan)
    Route::get('/chat/rooms', [ChatController::class, 'getRooms'])
        ->name('chat.rooms');

    Route::get('/chat/room/{roomId}/data', [ChatController::class, 'getRoomData'])
        ->name('chat.room.data');
    
    Route::post('/chat/room/{roomId}/send-ajax', [ChatController::class, 'storeAjaxMessage'])
        ->name('chat.room.send-ajax');

});