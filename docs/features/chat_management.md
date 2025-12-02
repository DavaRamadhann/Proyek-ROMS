# Fitur: Pusat Layanan Pelanggan Terpadu (Chat Management)

## Deskripsi
Fitur ini memusatkan semua komunikasi pelanggan dari WhatsApp ke dalam satu dasbor terpadu. Fitur ini memungkinkan Customer Service (CS) untuk menangani pesan masuk, mengirim balasan, dan melihat riwayat percakapan dalam satu antarmuka. Sistem juga mendukung distribusi chat otomatis (Round Robin) dan koneksi ke WhatsApp Official API.

## Alur Kerja (Workflow)

### 1. Koneksi WhatsApp
- **Aktor**: Admin
- **Proses**:
  1. Admin mengakses halaman status WhatsApp di dashboard.
  2. Sistem menampilkan QR Code yang diambil dari layanan WhatsApp Gateway.
  3. Admin memindai QR Code menggunakan aplikasi WhatsApp di ponsel bisnis.
  4. Setelah terhubung, status koneksi diperbarui menjadi "CONNECTED" atau "READY".
- **Teknis**: `WhatsAppConnectionController` menangani pengambilan QR Code dan status dari layanan pihak ketiga via API.

### 2. Penerimaan Pesan Masuk (Inbound)
- **Aktor**: Sistem (Webhook)
- **Proses**:
  1. Pelanggan mengirim pesan ke nomor WhatsApp bisnis.
  2. Webhook menerima payload pesan.
  3. `ChatService::handleInboundMessage` diproses:
     - Mencari atau membuat data `Customer` berdasarkan nomor telepon.
     - Mencari atau membuat `ChatRoom` untuk pelanggan tersebut.
     - Jika `ChatRoom` baru, sistem menjalankan logika **Round Robin** untuk menugaskan CS yang tersedia (`assignCsToNewRoom`).
     - Pesan disimpan ke database (`ChatMessage`).
     - Event `NewChatMessage` di-broadcast untuk update real-time di UI.

### 3. Distribusi Chat (Round Robin)
- **Logika**:
  - Sistem menyimpan daftar ID CS yang aktif dalam cache (`active_cs_user_ids`).
  - Sistem melacak ID CS terakhir yang mendapatkan tugas (`last_assigned_cs_id`).
  - Chat room baru akan diberikan ke CS berikutnya dalam antrian urutan.
  - Hal ini memastikan beban kerja terbagi rata di antara agen CS.

### 4. Menjawab Pesan (Outbound)
- **Aktor**: Customer Service (CS)
- **Proses**:
  1. CS membuka dashboard chat (`/app/chat/ui`).
  2. CS memilih chat room dari daftar.
  3. CS mengetik pesan dan menekan kirim.
  4. `ChatService::sendOutboundMessage` diproses:
     - Pesan disimpan di database dengan status 'pending'.
     - Sistem mengirim request API ke layanan WhatsApp Gateway.
     - Jika sukses, status pesan berubah menjadi 'sent'.
     - Jika gagal, status menjadi 'failed' dan error dicatat di log.

## Komponen Teknis
- **Controller**: `ChatController`, `WhatsAppConnectionController`
- **Service**: `ChatService`
- **Model**: `ChatRoom`, `ChatMessage`
- **Repository**: `ChatRoomRepository`, `ChatMessageRepository`
- **Events**: `NewChatMessage` (untuk real-time update via Pusher/Reverb)
- **Job**: Tidak ada job khusus, proses berjalan sinkron (kecuali broadcast event).

## Batasan Saat Ini
- Belum mendukung pesan media (gambar/file) secara penuh di UI (hanya teks).
- Status pesan (dibaca/diterima) belum tersinkronisasi penuh dari webhook (hanya status kirim).
