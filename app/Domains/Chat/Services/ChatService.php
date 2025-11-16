<?php
// app/Domains/Chat/Services/ChatService.php

namespace App\Domains\Chat\Services;

use App\Domains\Chat\Interfaces\ChatRoomRepositoryInterface;
use App\Domains\Chat\Interfaces\ChatMessageRepositoryInterface;
use App\Domains\Customer\Interfaces\CustomerRepositoryInterface;
use App\Models\User;
use App\Domains\Chat\Events\NewChatMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache; // <-- TAMBAHKAN INI

class ChatService
{
    protected $chatRoomRepo;
    protected $chatMessageRepo;
    protected $customerRepo;

    // Kunci cache untuk melacak CS terakhir
    const CACHE_KEY_LAST_CS = 'last_assigned_cs_id';

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
     * (Tidak ada perubahan dari Bagian 2)
     */
    public function handleInboundMessage(array $data)
    {
        $senderPhone = $data['from'];
        $messageBody = $data['message_body'];
        $senderName = $data['sender_name'] ?? $senderPhone;

        $customer = $this->customerRepo->findByPhone($senderPhone);

        if (!$customer) {
            $customer = $this->customerRepo->createCustomer([
                'phone' => $senderPhone,
                'name' => $senderName
            ]);
        } 
        else if ($customer->name !== $senderName && $senderName !== $senderPhone) {
            $this->customerRepo->updateCustomer($customer->id, ['name' => $senderName]);
            $customer->refresh();
        }

        $room = $this->chatRoomRepo->findOrCreateRoomForCustomer($customer->id);

        // 5. [TER-UPDATE] Logika assign CS sekarang memanggil method baru
        if ($room->wasRecentlyCreated && !$room->cs_user_id) {
            $csId = $this->assignCsToNewRoom(); // <-- Memanggil method round-robin
            if ($csId) {
                $this->chatRoomRepo->assignCsToRoom($room->id, $csId);
            }
        }

        $newMessage = $this->chatMessageRepo->createMessage([
            'chat_room_id' => $room->id,
            'sender_id' => null,
            'sender_type' => 'customer',
            'message_content' => $messageBody
        ]);

        if ($room->status == 'closed') {
            $this->chatRoomRepo->updateRoomStatus($room->id, 'open');
        }
        
        broadcast(new NewChatMessage($newMessage));

        return $newMessage;
    }

    /**
     * Mengirim pesan keluar (balasan dari CS).
     * (Tidak ada perubahan dari Bagian 2)
     */
    public function sendOutboundMessage(User $csUser, int $roomId, string $messageBody)
    {
        $room = $this->chatRoomRepo->findRoomById($roomId);
        if (!$room || !$room->customer) {
            throw new \Exception("Chat room atau Customer tidak ditemukan.");
        }

        $newMessage = $this->chatMessageRepo->createMessage([
            'chat_room_id' => $roomId,
            'sender_id' => $csUser->id,
            'sender_type' => 'user',
            'message_content' => $messageBody
        ]);

        if ($room->status == 'new') {
            $this->chatRoomRepo->updateRoomStatus($roomId, 'open');
        }

        try {
            $waServiceUrl = rtrim(config('services.whatsapp.url'), '/');
            $waServiceApiKey = config('services.whatsapp.api_key');
            $clientId = 'official_business';
            $customerPhone = $room->customer->phone;

            $response = Http::withHeaders([
                'x-api-key' => $waServiceApiKey,
                'Accept' => 'application/json',
            ])->post("{$waServiceUrl}/messages", [
                'clientId' => $clientId,
                'to' => $customerPhone,
                'text' => $messageBody,
            ]);

            if ($response->failed()) {
                Log::error("Gagal mengirim WA (HTTP {$response->status()}): {$response->body()}");
                $this->chatMessageRepo->updateMessageStatus($newMessage->id, 'failed');
            } else {
                $this->chatMessageRepo->updateMessageStatus($newMessage->id, 'sent');
            }

        } catch (\Exception $e) {
            Log::error('Error saat mengirim outbound WA: ' . $e->getMessage());
            $this->chatMessageRepo->updateMessageStatus($newMessage->id, 'failed');
            throw $e;
        }

        return $newMessage;
    }

    /**
     * [PERBAIKAN 3: Implementasi Logika Round-Robin]
     * Logika untuk men-dispatch/assign CS ke sebuah room secara bergiliran.
     */
    public function assignCsToNewRoom()
    {
        // 1. Ambil semua ID CS yang aktif.
        // Kita cache daftar ini selama 10 menit agar tidak query terus-menerus
        $csUserIds = Cache::remember('active_cs_user_ids', 600, function () {
            return User::where('role', 'cs') //
                // TODO: Tambahkan filter status 'aktif' jika ada
                ->orderBy('id', 'asc')
                ->pluck('id')
                ->all();
        });

        if (empty($csUserIds)) {
            Log::warning('Tidak ada CS yang terdaftar untuk menerima chat baru.');
            return null;
        }

        // 2. Ambil ID CS terakhir yang ditugaskan dari cache
        $lastAssignedId = Cache::get(self::CACHE_KEY_LAST_CS);

        // 3. Cari index dari CS terakhir
        $lastIndex = -1;
        if ($lastAssignedId) {
            $lastIndex = array_search($lastAssignedId, $csUserIds);
            if ($lastIndex === false) {
                // CS terakhir mungkin sudah tidak aktif, reset
                $lastIndex = -1;
            }
        }

        // 4. Tentukan index CS berikutnya (round-robin)
        $nextIndex = ($lastIndex + 1) % count($csUserIds);
        
        // 5. Ambil ID CS berikutnya
        $nextCsUserId = $csUserIds[$nextIndex];

        // 6. Simpan ID CS ini ke cache untuk tugas berikutnya
        Cache::put(self::CACHE_KEY_LAST_CS, $nextCsUserId, 600); // Simpan 10 menit

        // 7. Kembalikan ID CS yang terpilih
        return $nextCsUserId;
    }
}