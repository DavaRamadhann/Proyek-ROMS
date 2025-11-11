<?php
// app/Domains/Chat/routes/web.php

use Illuminate\Support\Facades\Route;
use App\Domains\Chat\Http\Controllers\ChatController;

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