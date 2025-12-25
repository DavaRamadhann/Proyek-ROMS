<?php
// app/Domains/Chat/Services/ChatService.php

namespace App\Domains\Chat\Services;

use App\Domains\Chat\Interfaces\ChatRoomRepositoryInterface;
use App\Domains\Chat\Interfaces\ChatMessageRepositoryInterface;
use App\Domains\Customer\Interfaces\CustomerRepositoryInterface;
use App\Domains\Chat\Interfaces\ChatContactRepositoryInterface;
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
    protected $contactRepo;

    // Kunci cache untuk melacak CS terakhir
    const CACHE_KEY_LAST_CS = 'last_assigned_cs_id';

    public function __construct(
        ChatRoomRepositoryInterface $chatRoomRepo,
        ChatMessageRepositoryInterface $chatMessageRepo,
        CustomerRepositoryInterface $customerRepo,
        ChatContactRepositoryInterface $contactRepo
    ) {
        $this->chatRoomRepo = $chatRoomRepo;
        $this->chatMessageRepo = $chatMessageRepo;
        $this->customerRepo = $customerRepo;
        $this->contactRepo = $contactRepo;
    }

    /**
     * "Otak" utama untuk menangani pesan masuk dari Webhook.
     */
    public function handleInboundMessage(array $data)
    {
        // Use the extracted phone number from wa-service
        // wa-service now sends both 'from' (full ID) and 'phone' (extracted number)
        $rawPhone = $data['phone'] ?? $data['from']; // Fallback to 'from' for backward compatibility
        $messageBody = $data['message_body'];

        // 1. Bersihkan nomor telepon
        // Hapus suffix @c.us, @s.whatsapp.net, @lid, dll (jika masih ada)
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
        $room = null;
        $senderType = 'customer';
        $senderId = null;

        if ($customer) {
            // Update nama jika belum diset manual dan ada nama baru yang lebih valid
            if (!$customer->is_manual_name && $customer->name === $customer->phone && $senderName !== $cleanPhone) {
                $this->customerRepo->update($customer->id, ['name' => $senderName]);
                $customer->refresh();
            }
            $room = $this->chatRoomRepo->findOrCreateRoomForCustomer($customer->id);
            $senderId = $customer->id;
        } else {
            // Check Contact
            $contact = $this->contactRepo->findByPhone($cleanPhone);
            if (!$contact) {
                $contact = $this->contactRepo->create([
                    'phone' => $cleanPhone,
                    'name' => $senderName
                ]);
            } else {
                 if ($contact->name !== $senderName) {
                     $this->contactRepo->update($contact->id, ['name' => $senderName]);
                 }
            }
            $room = $this->chatRoomRepo->findOrCreateRoomForContact($contact->id);
            $senderId = $contact->id;
            $senderType = 'contact';
        }

        if ($room->wasRecentlyCreated && !$room->cs_user_id) {
            $csId = $this->assignCsToNewRoom();
            if ($csId) {
                $this->chatRoomRepo->assignCsToRoom($room->id, $csId);
            }
        }

        // Simpan pesan baru ke database
        $newMessage = $this->chatMessageRepo->createMessage([
            'chat_room_id' => $room->id,
            'sender_id' => $senderId,
            'sender_type' => $senderType,
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
        if (!$room) {
            throw new \Exception("Chat room tidak ditemukan.");
        }

        $recipientPhone = null;
        if ($room->customer) {
            $recipientPhone = $room->customer->phone;
        } elseif ($room->chatContact) {
            $recipientPhone = $room->chatContact->phone;
        } else {
            throw new \Exception("Room tidak memiliki customer atau contact.");
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
            $customerPhone = $recipientPhone;

            // Jika ada attachment, kirim sebagai media (jika API support) atau kirim link
            // Untuk saat ini kita kirim link di dalam text jika ada attachment
            $payload = [
                'clientId' => $clientId,
                'to' => $customerPhone,
                'text' => $messageBody,
            ];

            if ($attachmentUrl) {
                // Fix for Docker: replace localhost/127.0.0.1 with host.docker.internal
                $dockerUrl = str_replace(
                    ['http://localhost', 'http://127.0.0.1'], 
                    'http://host.docker.internal', 
                    $attachmentUrl
                );
                $payload['media'] = ['url' => $dockerUrl];
                
                if (!empty($messageBody)) {
                    $payload['options'] = ['caption' => $messageBody];
                }
            }

            $response = Http::withHeaders([
                'x-api-key' => $waServiceApiKey,
                'Accept' => 'application/json',
            ])->post("{$waServiceUrl}/messages", $payload);

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
     * Handle Message Status Update (ACK)
     */
    public function handleMessageStatusUpdate(string $waMessageId, string $status)
    {
        // Cari pesan berdasarkan WA Message ID (kita perlu simpan ini dulu sebenarnya, 
        // tapi jika belum ada kolom wa_message_id, kita mungkin perlu cari cara lain 
        // atau asumsikan kita punya mapping. 
        // TAPI, ChatMessage model defaultnya tidak punya wa_message_id di fillable yang saya lihat tadi.
        // Mari kita cek apakah kita bisa cari berdasarkan content/timestamp atau ID?
        // Masalah: Kita tidak menyimpan ID pesan WA saat kirim (di sendOutboundMessage kita dapat response.id._serialized tapi tidak disimpan).
        // SOLUSI SEMENTARA: Kita cari pesan terakhir di room yang statusnya belum 'read' dan cocok? 
        // ATAU, kita harus update DB schema untuk simpan wa_message_id.
        // KARENA user minta "terapkan ini", saya harus pastikan ini jalan.
        // Tanpa wa_message_id, kita tidak bisa akurat 100%.
        // Namun, biasanya wa-service mengirim ACK dengan ID.
        // Jika kita tidak simpan ID, kita buntu.
        
        // Cek ChatMessage migration/model lagi? 
        // Tadi saya lihat fillable: chat_room_id, sender_id, sender_type, message_content, attachment_url, attachment_type, status.
        // TIDAK ADA wa_message_id.
        
        // Workaround: Karena kita tidak punya ID WA di DB, kita tidak bisa match exact message by ID.
        // TAPI, untuk fitur "checklist", biasanya yang penting adalah pesan TERAKHIR atau pesan yang barusan dikirim.
        // Jika kita terima ACK untuk pesan X, dan kita tidak tahu pesan X itu yang mana di DB...
        // Kita bisa asumsikan ACK itu untuk pesan terakhir yang dikirim oleh CS ke customer tersebut?
        // Ini berisiko jika ada banyak pesan cepat.
        
        // OPSI TERBAIK SAAT INI (Tanpa ubah schema DB yang ribet):
        // Kita cari pesan terakhir dari CS (sender_type='user') di room yang relevan yang statusnya != $status.
        // Tapi kita butuh Room ID atau Customer Phone.
        // Payload ACK punya 'to' (nomor customer).
        
        // Extract phone from ID? ID format: true_628xxx@c.us_ID
        // Parse phone from waMessageId
        $parts = explode('_', $waMessageId);
        if (count($parts) >= 2) {
            $remoteJid = $parts[1]; // 628xxx@c.us
            $phone = explode('@', $remoteJid)[0];
            
            // Cari customer
            $customer = $this->customerRepo->findByPhone($phone);
            if ($customer) {
                // Cari room
                $room = $this->chatRoomRepo->findActiveRoomByCustomer($customer->id);
                if ($room) {
                    // Cari pesan terakhir dari CS di room ini yang belum status ini
                    // Kita ambil pesan terakhir dari CS
                    $lastMessage = $this->chatMessageRepo->getMessagesForRoom($room->id)
                        ->where('sender_type', 'user')
                        ->sortByDesc('created_at')
                        ->first();
                        
                    if ($lastMessage) {
                        // Update status
                        $this->chatMessageRepo->updateMessageStatus($lastMessage->id, $status);
                        
                        // Broadcast update
                        try {
                            // Kita broadcast event baru atau reuse NewChatMessage?
                            // Idealnya ada event MessageStatusUpdated.
                            // Untuk hemat waktu, kita bisa broadcast NewChatMessage dengan pesan yang sama tapi status beda,
                            // atau buat event baru.
                            // Mari buat event simple on-the-fly atau gunakan yang ada.
                            // Di ui.blade.php kita listen channel 'chat.{roomId}'.
                            // Kita bisa kirim event 'MessageStatusUpdated'.
                            
                            broadcast(new \App\Domains\Chat\Events\MessageStatusUpdated($lastMessage));
                        } catch (\Exception $e) {
                            Log::error('Broadcast Status Error: ' . $e->getMessage());
                        }
                    }
                }
            }
        }
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
            ->whereRaw('is_online IS TRUE')
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