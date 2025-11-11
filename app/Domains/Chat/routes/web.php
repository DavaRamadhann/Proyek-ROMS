<?php
// app/Domains/Chat/routes/web.php

use Illuminate\Support\Facades\Route;
use App\Domains\Chat\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Rute Web Domain Chat
|--------------------------------------------------------------------------
|
| Rute-rute ini stateful (pakai session, auth) dan diakses
| oleh CS melalui browser.
|
*/

// Rute ini akan memiliki prefix 'app' dan middleware 'auth' & 'check.role'
// yang kita definisikan di routes/web.php utama.

Route::get('/chat', [ChatController::class, 'index'])
    ->name('chat.index'); // -> /app/chat

Route::get('/chat/{roomId}', [ChatController::class, 'show'])
    ->where('roomId', '[0-9]+')
    ->name('chat.show'); // -> /app/chat/1

Route::post('/chat/{roomId}/send', [ChatController::class, 'storeMessage'])
    ->where('roomId', '[0-9]+')
    ->name('chat.store'); // -> POST /app/chat/1/send