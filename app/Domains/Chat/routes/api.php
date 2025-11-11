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