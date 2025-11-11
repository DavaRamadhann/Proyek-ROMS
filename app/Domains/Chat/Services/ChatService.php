<?php
// app/Domains/Chat/Services/ChatService.php

namespace App\Domains\Chat\Services;

// Impor Interface Repository yang sudah kita buat
use App\Domains\Chat\Interfaces\ChatRoomRepositoryInterface;
use App\Domains\Chat\Interfaces\ChatMessageRepositoryInterface;
use App\Domains\Customer\Interfaces\CustomerRepositoryInterface;

// Impor Model (opsional, tapi kadang perlu)
use App\Models\User; // Model User bawaan

// Impor class Laravel untuk HTTP Client (Guzzle) & Logging
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatService
{
    // Kita gunakan Dependency Injection di constructor
    // untuk "menyuntik" semua Repository yang kita butuhkan.
    // Inilah kekuatan DDD + Service Provider yang kita buat tadi.

    protected $chatRoomRepo;
    protected $chatMessageRepo;
    protected $customerRepo;
    protected $waServiceUrl;
    protected $waServiceApiKey;
    protected $waServiceClientId; // ID Klien WA (misal: 'mywa1')

    public function __construct(
        ChatRoomRepositoryInterface $chatRoomRepo,
        ChatMessageRepositoryInterface $chatMessageRepo,
        CustomerRepositoryInterface $customerRepo
    ) {
        $this->chatRoomRepo = $chatRoomRepo;
        $this->chatMessageRepo = $chatMessageRepo;
        $this->customerRepo = $customerRepo;

        // Ambil konfigurasi wa-service dari file .env
        // Pastikan Anda menambahkannya di .env nanti!
        // WA_SERVICE_URL=http://localhost:3100
        // WA_SERVICE_API_KEY=kunci_rahasia_anda
        // WA_SERVICE_CLIENT_ID=mywa1
        $this->waServiceUrl = rtrim(env('WA_SERVICE_URL'), '/');
        $this->waServiceApiKey = env('WA_SERVICE_API_KEY');
        $this->waServiceClientId = env('WA_SERVICE_CLIENT_ID', 'mywa1');
    }

    /**
     * ===================================================================
     * METHOD UTAMA 1: MENANGANI PESAN MASUK (INBOUND)
     * ===================================================================
     * Ini adalah method yang akan dipanggil oleh Webhook Controller
     * saat wa-service mengirimkan data pesan baru.
     *
     * @param array $webhookData Data dari wa-service
     * $webhookData = [
     * "from" => "6281234567890", // Nomor HP Pelanggan
     * "message_body" => "Halo, pesanan saya..."
     * ]
     */
    public function handleInboundMessage(array $webhookData)
    {
        // Normalisasi nomor HP (contoh: 62812... | 0812...)
        // Kita akan buat helper untuk ini nanti, untuk sekarang kita anggap
        // nomor di $webhookData['from'] sudah bersih.
        $customerPhone = $webhookData['from'];
        $messageBody = $webhookData['message_body'];

        // Langkah 1: Cari atau Buat Customer
        $customer = $this->customerRepo->findByPhone($customerPhone);
        if (!$customer) {
            // Jika customer tidak ada, buat baru
            $customer = $this->customerRepo->create([
                'phone' => $customerPhone,
                'name' => 'WA Guest ' . substr($customerPhone, -4),
            ]);
        }

        // Langkah 2: Cari Active Chat Room, atau Buat Room Baru
        $chatRoom = $this->chatRoomRepo->findActiveRoomByCustomer($customer->id);

        if (!$chatRoom) {
            // --- INI ADALAH LOGIKA DISTRIBUSI CHAT ---
            $csUser = $this->findAvailableCs(); // Panggil logika distribusi

            $chatRoom = $this->chatRoomRepo->createRoom([
                'customer_id' => $customer->id,
                'cs_user_id'  => $csUser->id,
                'status'      => 'new', // Status 'new' sampai CS membalas
            ]);
        }

        // Langkah 3: Simpan pesan masuk ke database
        $this->chatMessageRepo->createMessage([
            'chat_room_id' => $chatRoom->id,
            'sender_type'  => 'customer',
            'message_content' => $messageBody,
        ]);

        // (Opsional) Trigger event Pusher/WebSocket agar UI CS refresh otomatis
        // event(new NewChatMessage($chatRoom->id, ...));

        Log::info("Pesan masuk dari {$customerPhone} berhasil diproses dan di-assign ke CS ID: {$chatRoom->cs_user_id}");

        return $chatRoom;
    }

    /**
     * ===================================================================
     * METHOD UTAMA 2: MENANGANI PESAN KELUAR (OUTBOUND)
     * ===================================================================
     * Ini adalah method yang akan dipanggil oleh ChatController
     * saat CS mengirim balasan dari aplikasi web (Blade).
     *
     * @param User $csUser Objek CS yang sedang login
     * @param int $roomId ID Chat Room
     * @param string $messageBody Isi balasan dari CS
     */
    public function sendOutboundMessage(User $csUser, int $roomId, string $messageBody)
    {
        // Langkah 1: Validasi (pastikan CS ini boleh membalas room ini)
        // TODO: Ganti query ini jadi lebih spesifik
        // $chatRoom = $this->chatRoomRepo->findRoomByIdAndCs($roomId, $csUser->id);
        $chatRoom = $this->chatRoomRepo->findActiveRoomById($roomId); // Asumsi method ini ada

        if (!$chatRoom || $chatRoom->cs_user_id !== $csUser->id) {
            throw new \Exception("Chat room tidak ditemukan atau Anda tidak di-assign.");
        }

        // Langkah 2: Simpan balasan CS ke database
        $this->chatMessageRepo->createMessage([
            'chat_room_id' => $roomId,
            'sender_type'  => 'cs',
            'message_content' => $messageBody,
        ]);

        // Langkah 3: Kirim pesan ke wa-service (Node.js) via API
        $customerPhone = $chatRoom->customer->phone;
        
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->waServiceApiKey, //
                'Content-Type' => 'application/json',
            ])->post($this->waServiceUrl . '/messages', [ //
                'clientId' => $this->waServiceClientId,
                'to' => $customerPhone,
                'text' => $messageBody,
            ]);

            if ($response->failed()) {
                // Jika gagal kirim, catat error tapi jangan gagalkan proses
                Log::error("Gagal mengirim WA ke {$customerPhone}: " . $response->body());
                // Mungkin kita perlu update status pesan di DB jadi 'failed'
            } else {
                Log::info("Berhasil mengirim WA ke {$customerPhone}.");
            }

        } catch (\Exception $e) {
            Log::error("Exception saat koneksi ke wa-service: " . $e->getMessage());
        }

        // Langkah 4: Update status room (jika 'new' -> 'open')
        if ($chatRoom->status == 'new') {
            $this->chatRoomRepo->updateRoomStatus($roomId, 'open');
        }

        return true;
    }


    /**
     * ===================================================================
     * LOGIKA INTERNAL: DISTRIBUSI CHAT
     * ===================================================================
     * Ini adalah jantung dari ROMS-005.
     * Untuk Proyek 3, kita buat versi sederhana dulu.
     */
    protected function findAvailableCs(): User
    {
        // Logika Sederhana v1: Ambil semua user dengan role 'cs'
        // Lalu pilih salah satu secara ACAK.
        
        // Pastikan model User Anda punya 'role'
        $availableCs = User::where('role', 'cs')->get(); 

        if ($availableCs->isEmpty()) {
            // Darurat: Assign ke Admin pertama jika tidak ada CS
            // Ini asumsi, di dunia nyata harus ada penanganan error
            Log::error("Tidak ada CS yang tersedia! Menggunakan Admin...");
            return User::where('role', 'admin')->firstOrFail();
        }

        // Pilih satu CS secara acak dari koleksi
        return $availableCs->random();

        /*
         * CATATAN UNTUK PENGEMBANGAN:
         * Logika v2 (Lebih Canggih):
         * 1. Ambil semua CS (role 'cs')
         * 2. Hitung jumlah chat room 'open' untuk setiap CS (pakai $this->chatRoomRepo).
         * 3. Pilih CS dengan jumlah chat 'open' PALING SEDIKIT.
         * Ini disebut "Least Active" load balancing.
         */
    }
}