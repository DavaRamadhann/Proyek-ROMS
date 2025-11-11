<?php
// app/Domains/Chat/Repositories/ChatRoomRepository.php

namespace App\Domains\Chat\Repositories;

use App\Domains\Chat\Interfaces\ChatRoomRepositoryInterface;
use App\Domains\Chat\Models\ChatRoom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB; // <-- Pastikan ini ada

class ChatRoomRepository implements ChatRoomRepositoryInterface
{
    public function findActiveRoomByCustomer(int $customerId): ?ChatRoom
    {
        return ChatRoom::where('customer_id', $customerId)
                       ->whereIn('status', ['new', 'open'])
                       ->first();
    }

    public function createRoom(array $data): ChatRoom
    {
        return ChatRoom::create($data);
    }

    public function getRoomsForCs(int $csId): Collection
    {
        // Kita eager load relasi 'customer' agar di view inbox
        // kita bisa langsung tampilkan nama pelanggannya.
        return ChatRoom::with('customer')
                        ->where('cs_user_id', $csId)
                        ->whereIn('status', ['new', 'open'])
                        ->latest('id')
                        ->get();
    }

    public function updateRoomStatus(int $roomId, string $status): bool
    {
        return ChatRoom::where('id', $roomId)
                       ->update(['status' => $status]);
    }

    public function assignCsToRoom(int $roomId, int $csId): bool
    {
        return ChatRoom::where('id', $roomId)
                       ->update([
                           'cs_user_id' => $csId,
                           'status' => 'open' // Otomatis 'open' saat di-assign
                       ]);
    }

    // Method tambahan yang kita asumsikan ada di ChatController
    public function findRoomById(int $roomId): ?ChatRoom
    {
        return ChatRoom::find($roomId);
    }

    // Method tambahan yang kita asumsikan ada di ChatService
    public function findActiveRoomById(int $roomId): ?ChatRoom
    {
         return ChatRoom::where('id', $roomId)
                       ->whereIn('status', ['new', 'open'])
                       ->first();
    }
}