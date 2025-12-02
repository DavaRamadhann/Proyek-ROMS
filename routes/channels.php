<?php
// File: routes/channels.php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Di sinilah Anda dapat mendaftarkan semua channel event broadcasting
| yang didukung oleh aplikasi Anda.
|
*/

/**
 * Verifikasi apakah $user (CS yang login)
 * boleh mendengarkan channel 'chat-room.{roomId}'
 */
Broadcast::channel('chat-room.{roomId}', function ($user, $roomId) {
    
    // TODO: Implementasikan logika validasi yang lebih aman.
    // Saat ini, kita hanya cek apakah rolenya 'cs'.
    // Idealnya: Cek apakah $user->id (CS) ini memang di-assign
    // ke $roomId di tabel 'chat_rooms'.
    
    if ($user->role === 'cs') { // Asumsi Anda punya field 'role'
        return true; 
    }
    
    return false;
});

Broadcast::channel('chat-dashboard', function ($user) {
    return $user->role === 'cs' || $user->role === 'admin';
});