<?php
// app/Domains/Chat/Interfaces/ChatMessageRepositoryInterface.php

namespace App\Domains\Chat\Interfaces;

use App\Domains\Chat\Models\ChatMessage;
use Illuminate\Support\Collection;

interface ChatMessageRepositoryInterface
{
    /**
     * Mengambil semua pesan untuk satu chat room.
     */
    public function getMessagesForRoom(int $roomId): Collection;

    /**
     * Menyimpan pesan baru ke database.
     */
    public function createMessage(array $data): ChatMessage;
}