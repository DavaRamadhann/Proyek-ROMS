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
use Illuminate\Support\Facades\Cache;

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
     */
    public function handleInboundMessage(array $data)
    {
        $rawPhone = $data['from'];
        $messageBody = $data['message_body'];

        // [FILTER] Hanya terima pesan dari Private Chat (@c.us)
        // Abaikan Group (@g.us), Broadcast (@broadcast), dll.
        if (!str_ends_with($rawPhone, '@c.us')) {
            return null;
        }
        
        // 1. Bersihkan nomor telepon
        // Hapus suffix @c.us, @s.whatsapp.net, dll
        $cleanPhone = preg_replace('/@.+/', '', $rawPhone);
        // Hapus karakter non-digit
        $cleanPhone = preg_replace('/[^0-9]/', '', $cleanPhone);
        
        // Standarisasi ke format 62 (jika diawali 08, ubah ke 628)
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        // 2. Tentukan Nama Pengirim
        // Prioritas: pushName (nama profil WA) -> sender_name (dari kontak) -> Nomor HP
        $pushName = $data['pushName'] ?? null;
        $contactName = $data['sender_name'] ?? null;
        
        $senderName = $pushName ?: ($contactName ?: $cleanPhone);

        $customer = $this->customerRepo->findByPhone($cleanPhone);

        if (!$customer) {
            $customer = $this->customerRepo->create([
                'phone' => $cleanPhone,
                'name' => $senderName
            ]);
        } 
        else {
            // Update nama jika belum diset manual dan ada nama baru yang lebih valid
            // Kita hanya update jika nama saat ini adalah nomor telepon, atau jika ada pushName baru
            if (!$customer->is_manual_name && $customer->name === $customer->phone && $senderName !== $cleanPhone) {
                $this->customerRepo->update($customer->id, ['name' => $senderName]);
                $customer->refresh();
            }
        }

        $room = $this->chatRoomRepo->findOrCreateRoomForCustomer($customer->id);

        if ($room->wasRecentlyCreated && !$room->cs_user_id) {
            $csId = $this->assignCsToNewRoom();
            if ($csId) {
                $this->chatRoomRepo->assignCsToRoom($room->id, $csId);
            }
        }

        // Simpan pesan baru ke database
        $newMessage = $this->chatMessageRepo->createMessage([
            'chat_room_id' => $room->id,
            'sender_id' => $customer->id,
            'sender_type' => 'customer',
            'message_content' => $messageBody
        ]);

        // PENTING: Touch room agar updated_at terupdate untuk Long Polling
        $room->update(['updated_at' => now()]);

        if ($room->status == 'closed') {
            $this->chatRoomRepo->updateRoomStatus($room->id, 'open');
        }
        
        try {
            broadcast(new NewChatMessage($newMessage));
            broadcast(new \App\Domains\Chat\Events\ChatListUpdated($room)); // Update List
        } catch (\Exception $e) {
            Log::error('Broadcast error: ' . $e->getMessage());
        }

        return $newMessage;
    }

    /**
     * Mengirim pesan keluar (balasan dari CS).
     */
    public function sendOutboundMessage(User $csUser, int $roomId, string $messageBody, $attachment = null)
    {
        $room = $this->chatRoomRepo->findRoomById($roomId);
        if (!$room || !$room->customer) {
            throw new \Exception("Chat room atau Customer tidak ditemukan.");
        }

        // 1. Handle Attachment Upload
        $attachmentUrl = null;
        $attachmentType = null;

        if ($attachment) {
            $path = $attachment->store('chat_attachments', 'public');
            $attachmentUrl = asset('storage/' . $path);
            
            $mime = $attachment->getMimeType();
            if (str_starts_with($mime, 'image/')) {
                $attachmentType = 'image';
            } else {
                $attachmentType = 'document';
            }
        }

        // 2. Create Message in DB
        $newMessage = $this->chatMessageRepo->createMessage([
            'chat_room_id' => $roomId,
            'sender_id' => $csUser->id,
            'sender_type' => 'user',
            'message_content' => $messageBody,
            'attachment_url' => $attachmentUrl,
            'attachment_type' => $attachmentType,
        ]);

        if ($room->status == 'new') {
            $this->chatRoomRepo->updateRoomStatus($roomId, 'open');
        }

        // 3. Send to WhatsApp
        try {
            $waServiceUrl = rtrim(config('services.whatsapp.url'), '/');
            $waServiceApiKey = config('services.whatsapp.api_key');
            $clientId = 'official_business';
            $customerPhone = $room->customer->phone;

            // Jika ada attachment, kirim sebagai media (jika API support) atau kirim link
            // Untuk saat ini kita kirim link di dalam text jika ada attachment
            $finalText = $messageBody;
            if ($attachmentUrl) {
                $finalText .= "\n\n[Attachment]: " . $attachmentUrl;
            }

            $response = Http::withHeaders([
                'x-api-key' => $waServiceApiKey,
                'Accept' => 'application/json',
            ])->post("{$waServiceUrl}/messages", [
                'clientId' => $clientId,
                'to' => $customerPhone,
                'text' => $finalText,
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

        // PENTING: Touch room agar updated_at terupdate untuk Long Polling
        $room->update(['updated_at' => now()]);

        // Broadcast update list juga saat kirim pesan keluar
        try {
            broadcast(new \App\Domains\Chat\Events\ChatListUpdated($room));
        } catch (\Exception $e) {
            Log::error('Broadcast List Update error: ' . $e->getMessage());
        }

        return $newMessage;
    }

    /**
     * [PERBAIKAN 3: Implementasi Logika Round-Robin]
     * Logika untuk men-dispatch/assign CS ke sebuah room secara bergiliran.
     */
    public function assignCsToNewRoom()
    {
        // 1. Ambil semua ID CS yang aktif dan ONLINE
        // Kita cache daftar ini selama 10 menit agar tidak query terus-menerus
        // Note: Cache key dibedakan agar refresh saat ada perubahan status (ideally cache di-clear saat toggle)
        // Untuk simpelnya, kita kurangi durasi cache atau hapus cache saat toggle (di controller)
        // Di sini kita query langsung dulu untuk akurasi real-time
        $csUserIds = User::where('role', 'cs')
            ->where('is_online', true)
            ->orderBy('id', 'asc')
            ->pluck('id')
            ->all();

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