<?php

namespace App\Domains\Chat\Events;

use App\Domains\Chat\Models\ChatRoom;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatListUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatRoom $room)
    {
        // Load relasi yang dibutuhkan untuk tampilan list
        $this->room = $room->load(['customer', 'latestMessage']);
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat-dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat-list-updated';
    }
}
