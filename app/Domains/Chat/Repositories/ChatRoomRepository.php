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
     * Find room, or create new with round-robin CS assignment.
     */
    public function findOrCreateRoomForCustomer(int $customerId): ChatRoom
    {
        // Check if room already exists
        $existingRoom = ChatRoom::where('customer_id', $customerId)->first();
        if ($existingRoom) {
            return $existingRoom;
        }

        // Get next CS in round-robin
        $assignedCsId = $this->getNextCsIdRoundRobin();

        // Create new room with assigned CS
        return ChatRoom::create([
            'customer_id' => $customerId,
            'status' => 'new',
            'cs_user_id' => $assignedCsId
        ]);
    }

    public function findOrCreateRoomForContact(int $contactId): ChatRoom
    {
        // Check if room already exists
        $existingRoom = ChatRoom::where('chat_contact_id', $contactId)->first();
        if ($existingRoom) {
            return $existingRoom;
        }

        // Get next CS in round-robin
        $assignedCsId = $this->getNextCsIdRoundRobin();

        // Create new room with assigned CS
        return ChatRoom::create([
            'chat_contact_id' => $contactId,
            'status' => 'new',
            'cs_user_id' => $assignedCsId
        ]);
    }

    /**
     * Get next CS ID using round-robin algorithm
     */
    private function getNextCsIdRoundRobin(): ?int
    {
        // Get all active CS users who are ONLINE
        $csUsers = \App\Models\User::where('role', 'cs')
            ->where('email_verified_at', '!=', null)
            ->whereRaw('is_online = true')  // PostgreSQL boolean comparison
            ->orderBy('id')
            ->get();

        if ($csUsers->isEmpty()) {
            // Fallback: return null (no online CS available)
            return null;
        }

        // Get last assigned index from system settings
        $setting = \DB::table('system_settings')
            ->where('key', 'last_assigned_cs_index')
            ->first();

        $lastIndex = $setting ? (int)$setting->value : -1;

        // Calculate next index (round-robin)
        $nextIndex = ($lastIndex + 1) % $csUsers->count();

        // Update the setting
        \DB::table('system_settings')->updateOrInsert(
            ['key' => 'last_assigned_cs_index'],
            [
                'value' => (string)$nextIndex,
                'updated_at' => now()
            ]
        );

        // Return the CS user ID at next index
        return $csUsers[$nextIndex]->id;
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