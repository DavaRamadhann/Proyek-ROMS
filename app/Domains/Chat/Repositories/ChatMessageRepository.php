<?php
// app/Domains/Chat/Repositories/ChatMessageRepository.php

namespace App\Domains\Chat\Repositories;

use App\Domains\Chat\Interfaces\ChatMessageRepositoryInterface;
use App\Domains\Chat\Models\ChatMessage;
use Illuminate\Support\Collection;

class ChatMessageRepository implements ChatMessageRepositoryInterface
{
    public function getMessagesForRoom(int $roomId): Collection
    {
        return ChatMessage::where('chat_room_id', $roomId)
                            ->orderBy('created_at', 'asc') // Urutkan dari terlama
                            ->get();
    }

    public function createMessage(array $data): ChatMessage
    {
        return ChatMessage::create($data);
    }
}