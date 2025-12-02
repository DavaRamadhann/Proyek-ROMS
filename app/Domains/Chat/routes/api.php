<?php
// app/Domains/Chat/routes/api.php

use Illuminate\Support\Facades\Route;
use App\Domains\Chat\Http\Controllers\InboundWebhookController;

/*
|--------------------------------------------------------------------------
| Rute API Domain Chat
|--------------------------------------------------------------------------
|
| Endpoint ini state-less dan digunakan untuk komunikasi antar-service.
|
*/

Route::post('/v1/webhook/whatsapp-inbound', [InboundWebhookController::class, 'handle'])
    ->name('api.chat.webhook.inbound');
    
// (Tidak ada 'require' atau 'use Illuminate\Http\Request' di sini!)

use App\Domains\Chat\Http\Controllers\WhatsAppConnectionController;

Route::prefix('whatsapp')->group(function () {
    Route::get('/status', [WhatsAppConnectionController::class, 'getStatus'])->name('api.whatsapp.status');
    Route::get('/qr', [WhatsAppConnectionController::class, 'getQrCode'])->name('api.whatsapp.qr');
    Route::post('/start', [WhatsAppConnectionController::class, 'start'])->name('api.whatsapp.start');
    Route::post('/reconnect', [WhatsAppConnectionController::class, 'requestReconnect'])->name('api.whatsapp.reconnect');
    Route::post('/disconnect', [WhatsAppConnectionController::class, 'disconnect'])->name('api.whatsapp.disconnect');
});