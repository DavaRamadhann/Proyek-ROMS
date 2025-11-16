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
        // [PERBAIKAN]
        // Tambahkan status default 'pending' jika pesan baru dibuat.
        // Ini akan di-update oleh ChatService menjadi 'sent' atau 'failed'.
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }
        
        return ChatMessage::create($data);
    }

    /**
     * [IMPLEMENTASI BARU]
     * Implementasi dari method di Interface.
     */
    public function updateMessageStatus(int $messageId, string $status): bool
    {
        // Temukan pesan berdasarkan ID dan update statusnya.
        // Menggunakan update massal lebih efisien.
        $updatedCount = ChatMessage::where('id', $messageId)
                                   ->update(['status' => $status]);
        
        return $updatedCount > 0;
    }
}