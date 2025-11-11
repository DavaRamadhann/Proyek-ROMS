<?php
// app/Domains/Chat/Interfaces/ChatRoomRepositoryInterface.php

namespace App\Domains\Chat\Interfaces;

use App\Domains\Chat\Models\ChatRoom;
use Illuminate\Support\Collection;

interface ChatRoomRepositoryInterface
{
    /**
     * Cari active room (status 'new' or 'open') berdasarkan customer ID.
     * Ini krusial untuk menentukan apakah kita perlu buat room baru atau tidak.
     */
    public function findActiveRoomByCustomer(int $customerId): ?ChatRoom;

    /**
     * Membuat chat room baru.
     */
    public function createRoom(array $data): ChatRoom;

    /**
     * Mengambil daftar room untuk inbox seorang CS.
     */
    public function getRoomsForCs(int $csId): Collection;

    /**
     * Mengubah status room (misal: 'new' -> 'open', 'open' -> 'closed').
     */
    public function updateRoomStatus(int $roomId, string $status): bool;

    /**
     * Meng-assign seorang CS ke sebuah chat room.
     * Ini adalah inti dari logika 'Distribusi Chat'.
     */
    public function assignCsToRoom(int $roomId, int $csId): bool;
}