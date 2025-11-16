<?php
// app/Domains/Chat/Interfaces/ChatMessageRepositoryInterface.php

namespace App\Domains\Chat\Interfaces;

use App\Domains\Chat\Models\ChatMessage;
use Illuminate\Support\Collection;

interface ChatMessageRepositoryInterface
{
    public function getMessagesForRoom(int $roomId): Collection;

    public function createMessage(array $data): ChatMessage;

    /**
     * [TAMBAHAN]
     * Update status pesan (mis: 'pending', 'sent', 'failed', 'read').
     */
    public function updateMessageStatus(int $messageId, string $status): bool;
}