<?php
// app/Domains/Chat/Services/ChatService.php

namespace App\Domains\Chat\Services;

use App\Domains\Chat\Interfaces\ChatRoomRepositoryInterface;
use App\Domains\Chat\Interfaces\ChatMessageRepositoryInterface;
use App\Domains\Customer\Interfaces\CustomerRepositoryInterface;
use App\Models\User;
use App\Domains\Chat\Events\NewChatMessage;

class ChatService
{
    protected $chatRoomRepo;
    protected $chatMessageRepo;
    protected $customerRepo;

    public function __construct(
        ChatRoomRepositoryInterface $chatRoomRepo,
        ChatMessageRepositoryInterface $chatMessageRepo,
        CustomerRepositoryInterface $customerRepo
    ) {
        $this->chatRoomRepo = $chatRoomRepo;
        $this->chatMessageRepo = $chatMessageRepo;
        $this->customerRepo = $customerRepo;
    }

    /**
     * "Otak" utama untuk menangani pesan masuk dari Webhook.
     */
    public function handleInboundMessage(string $senderPhone, string $senderName, string $messageBody)
    {
        // 1. Coba cari customer dulu (LANGSUNG PAKAI MODEL)
        $customer = \App\Domains\Customer\Models\Customer::where('phone', $senderPhone)->first();

        // 2. Jika tidak ada, buat baru (Ini pakai repo, karena method createCustomer-nya ADA)
        if (!$customer) {
            $customer = $this->customerRepo->createCustomer([
                'phone' => $senderPhone,
                'name' => $senderName
            ]);
        } 
        // 3. (Opsional) Jika ada tapi namanya beda, update
        else if ($customer->name !== $senderName) {
            $customer->name = $senderName;
            $customer->save();
        }

        // 4. Cari atau buat chat room
        $room = $this->chatRoomRepo->findOrCreateRoomForCustomer($customer->id);

        // 5. Logika assign CS
        if ($room->wasRecentlyCreated && !$room->cs_user_id) {
            $csId = $this->assignCsToNewRoom();
            if ($csId) {
                $this->chatRoomRepo->assignCsToRoom($room->id, $csId);
            }
        }

        // 6. [PERBAIKAN] Simpan pesan ke room (kirim sebagai array)
        $newMessage = $this->chatMessageRepo->createMessage([
            'chat_room_id' => $room->id,
            'sender_id' => null,
            'sender_type' => 'customer',
            'message_content' => $messageBody
        ]);

        // 7. Update status room (jika perlu)
        if ($room->status == 'closed') {
            $this->chatRoomRepo->updateRoomStatus($room->id, 'open');
        }
        
        // 8. Trigger event agar UI CS ter-update secara real-time
        broadcast(new NewChatMessage($newMessage));

        return $newMessage;
    }

    /**
     * Mengirim pesan keluar (balasan dari CS).
     */
    public function sendOutboundMessage(User $csUser, int $roomId, string $messageBody)
    {
        $room = $this->chatRoomRepo->findRoomById($roomId);
        if (!$room) {
            throw new \Exception("Chat room tidak ditemukan.");
        }

        // 2. [PERBAIKAN] Simpan pesan ke database (kirim sebagai array)
        $newMessage = $this->chatMessageRepo->createMessage([
            'chat_room_id' => $roomId,
            'sender_id' => $csUser->id,
            'sender_type' => 'user', // 'sender_type' untuk CS adalah 'user'
            'message_content' => $messageBody
        ]);

        // 3. Update status room menjadi 'open' (jika sebelumnya 'new')
        if ($room->status == 'new') {
            $this->chatRoomRepo->updateRoomStatus($roomId, 'open');
        }

        // 4. Kirim pesan ke WhatsApp Gateway
        // TODO: Implementasikan logic pengiriman ke API WhatsApp (Baileys dll)

        return $newMessage;
    }

    /**
     * Logika untuk men-dispatch/assign CS ke sebuah room.
     */
    public function assignCsToNewRoom()
    {
        $firstCs = User::where('role', 'cs')->first();
        return $firstCs ? $firstCs->id : null;
    }
}