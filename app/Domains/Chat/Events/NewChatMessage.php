<?php
// File: app/Domains/Chat/Events/NewChatMessage.php

namespace App\Domains\Chat\Events;

use App\Domains\Chat\Models\ChatMessage; // <-- Pastikan ini di-import
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // Pakai 'Now'
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Data pesan yang akan di-broadcast.
     * Kita buat 'public' agar otomatis terkirim sebagai payload.
     */
    public ChatMessage $message;

    /**
     * Buat instance event baru.
     */
    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Tentukan channel mana yang akan di-broadcast.
     */
    public function broadcastOn(): Channel|array
    {
        // Broadcast ke channel privat berdasarkan ID room
        return new PrivateChannel('chat-room.' . $this->message->chat_room_id);
    }

    /**
     * Nama event yang akan di-broadcast.
     */
    public function broadcastAs(): string
    {
        // Frontend (JS) akan mendengarkan event 'new-message'
        return 'new-message';
    }
}