<?php
// app/Domains/Chat/Repositories/ChatRoomRepository.php

namespace App\Domains\Chat\Repositories;

use App\Domains\Chat\Interfaces\ChatRoomRepositoryInterface;
use App\Domains\Chat\Models\ChatRoom;
// Hapus 'use App\Domains\Chat\Services\ChatService;'

class ChatRoomRepository implements ChatRoomRepositoryInterface
{
    // Hapus __construct() yang memanggil ChatService

    /**
     * Cari room, atau buat baru.
     * Logika assign CS dipindahkan ke ChatService.
     */
    public function findOrCreateRoomForCustomer(int $customerId): ChatRoom
    {
        return ChatRoom::firstOrCreate(
            ['customer_id' => $customerId],
            ['status' => 'new']
        );
        // Kita tidak assign CS di sini lagi
    }

    /**
     * Ambil semua room yang di-assign ke CS
     */
    public function getRoomsForCs(int $csUserId): \Illuminate\Database\Eloquent\Collection
    {
        return ChatRoom::with('customer')
                       ->where('cs_user_id', $csUserId)
                       ->orderBy('status', 'desc')
                       ->orderBy('id', 'desc')
                       ->get();
    }

    public function updateRoomStatus(int $roomId, string $status): bool
    {
        $room = $this->findRoomById($roomId);
        if ($room) {
            $room->status = $status;
            return $room->save();
        }
        return false;
    }

    public function assignCsToRoom(int $roomId, int $csId): bool
    {
        $room = $this->findRoomById($roomId);
        if ($room) {
            $room->cs_user_id = $csId;
            return $room->save();
        }
        return false;
    }

    public function findRoomById(int $roomId): ?ChatRoom
    {
        return ChatRoom::find($roomId);
    }

    public function findRoomByCustomerPhone(string $phone): ?ChatRoom
    {
        return ChatRoom::whereHas('customer', function ($query) use ($phone) {
            $query->where('phone', $phone);
        })->first();
    }

    // Implementasi method yang hilang (sesuai kontrak)
    public function createRoom(array $data): ChatRoom
    {
        return ChatRoom::create($data);
    }

    public function findActiveRoomByCustomer(int $customerId): ?ChatRoom
    {
        return ChatRoom::where('customer_id', $customerId)
                       ->whereIn('status', ['new', 'open'])
                       ->first();
    }
}