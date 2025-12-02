# 🚀 Proyek ROMS - WhatsApp Automation & CS Dashboard

Sistem manajemen Customer Service (CS) berbasis WhatsApp yang memungkinkan pengelolaan pesan secara *real-time*, *multi-user*, dan terpusat.

## 📦 Arsitektur Sistem (Microservices)

Sistem ini terdiri dari tiga komponen utama yang saling berkomunikasi:

1.  **Backend & Frontend (Laravel 11):**
    * Bertindak sebagai "Otak" sistem, menyimpan database chat, dan menyediakan *dashboard* untuk CS.
    * Menangani *logic* bisnis dan API Gateway.
2.  **WhatsApp Gateway (`wa-service` - Node.js):**
    * Berjalan di dalam **Docker**.
    * Menjalankan *Puppeteer* untuk mengelola sesi WhatsApp Web secara otomatis.
    * Mengirimkan pesan masuk ke Laravel via **Webhook**.
    * **Repository:** [DavaRamadhann/Whatsapp-Service](https://github.com/DavaRamadhann/Whatsapp-Service)
3.  **Real-time Updates (Polling):**
    * Chat inbox menggunakan mekanisme polling (setiap 3 detik) untuk mengambil pesan baru.
    * Solusi sederhana dan reliable tanpa perlu WebSocket server eksternal.

---

## 🛠️ Prasyarat (Requirements)

Agar sistem berjalan lancar di lingkungan lokal (Localhost), pastikan Anda memiliki:

1.  **PHP 8.2+ & Composer** (Untuk Laravel).
2.  **Node.js & NPM** (Untuk kompilasi frontend assets).
3.  **Docker Desktop** (**WAJIB**).
    * Diperlukan agar kontainer `wa-service` bisa berkomunikasi dengan Laravel via `host.docker.internal`.

---

## ⚡ Panduan Instalasi Singkat

### 1. Setup Laravel (Host)

Konfigurasi file `.env` Anda untuk terhubung dengan `wa-service`.

### 2. Cara Install (Untuk Tim)

**Opsi A: Via File Explorer (Paling Gampang)**
1. Buka folder proyek ini di **File Explorer**.
2. Cari file bernama `setup.bat`.
3. **Klik 2x** file tersebut.
4. Tunggu proses selesai (Estimasi 3-5 menit, lumayan bisa buat nyeduh kopi ☕).

**Opsi B: Via VS Code Terminal**
1. Buka Terminal di VS Code (`Ctrl + ` ` `).
2. Ketik perintah: `.\setup.bat` lalu Enter.
3. Tunggu proses selesai (Sambil ngopi juga boleh ☕).

### 3. Setup WA Service (Docker)

* Pastikan Docker Desktop sudah berjalan.
* Pada file `.env` di folder `wa-service`, pastikan URL Webhook mengarah ke `host.docker.internal` dengan path API yang benar:
    ```env
    # Sesuaikan port 8000 dengan port Laravel Anda
    WEBHOOK_URL=[http://host.docker.internal:8000/api/v1/webhook/whatsapp-inbound](http://host.docker.internal:8000/api/v1/webhook/whatsapp-inbound)
    ```
* Jalankan service:
    ```bash
    docker compose up -d
    ```

---

## 🚦 Status Fitur (Updated: 29 Nov 2025)

| Fitur | Status | Keterangan |
| :--- | :---: | :--- |
| **Koneksi WhatsApp** | ✅ | Scan QR Code, Session Management, **Auto-Reconnect**, & **Anti-Flicker**. |
| **Kirim Pesan (Outbound)** | ✅ | CS dapat membalas chat dari Dashboard ke nomor WA pelanggan. |
| **Terima Pesan (Inbound)** | ✅ | Webhook aktif, pesan masuk tersimpan otomatis di database. |
| **Real-time Chat** | ✅ | Pesan masuk muncul otomatis (via polling setiap 3 detik). |
| **Auto-Assign CS** | ✅ | Logika *Round-robin* untuk membagi chat ke CS yang tersedia. |
| **Sistem Role (Admin & CS)** | ✅ | Role-based access control dengan 2 role: Admin dan CS. |
| **Manajemen CS** | ✅ | Admin dapat mendaftarkan, edit, dan hapus akun CS. |
| **Dashboard CS** | ✅ | Dashboard khusus CS dengan akses terbatas (tanpa fitur admin). |
| **Login Redirect** | ✅ | Auto-redirect berdasarkan role (Admin → /dashboard, CS → /cs/dashboard). |
| **Sidebar Role-Aware** | ✅ | Menu sidebar menyesuaikan dengan role user. |
| **Input Pesanan Manual** | ✅ | Admin/CS dapat membuat pesanan baru secara manual (pilih pelanggan & produk). |
| **Broadcast / Otomasi Pesan** | ✅ | Kirim pesan massal ke semua pelanggan dengan delay aman & log pengiriman. |
| **Manajemen Pesanan** | ✅ | CRUD Pesanan, Update Status (Pending/Shipped/Completed), Hapus Pesanan. |
| **Manajemen Kontak** | ✅ | CRUD Pelanggan, Pencarian, Format Nomor HP Bersih, & Proteksi Nama Manual. |
| **Waktu Pesan** | ⚠️ | Timestamp belum dikonversi ke zona waktu lokal (WIB). |
| **Reminder Repeat Order** | ✅ | Otomatis kirim pesan pengingat ke pelanggan setelah X hari order selesai. |
| **Unified Chat Dashboard** | ✅ | Tampilan 3 kolom (List, Chat, Info) dengan AJAX & Order History. |
| **Automated Segmentation** | ✅ | Status pelanggan otomatis (New, Loyal, Big Spender, Inactive) via RFM. |
| **API Order Integration** | ✅ | Endpoint `POST /api/v1/orders` untuk menerima pesanan dari sistem luar. |
| **Automated Thank You** | ✅ | Pesan "Terima Kasih" otomatis terkirim saat order baru masuk. |
| **Business Intelligence** | ✅ | Laporan Top 10 Pelanggan & Sebaran Wilayah di Dashboard Admin. |
| **Manajemen Template Pesan** | ✅ | Simpan & gunakan ulang format pesan untuk Broadcast & Reminder. |
| **Toggle Status CS (Online/Offline)** | ✅ | CS dapat mengatur status ketersediaan. Chat hanya masuk ke CS yang Online. |
| **Promosi Cross-sell & Upsell** | ✅ | Rekomendasi produk otomatis di pesan reminder berdasarkan riwayat pembelian. |
| **Otomasi Ucapan Terima Kasih** | ✅ | Pesan WA otomatis terkirim saat order baru dibuat (via API/Manual). |
| **Premium UI/UX** | ✅ | Redesign Header (Glassmorphism), Omnibar Search, & Perbaikan Layout Admin. |

---

## 🔐 Kredensial Default

Setelah menjalankan `php artisan db:seed`, gunakan kredensial berikut untuk login:

| Role | Email | Password | Akses |
| :--- | :--- | :--- | :--- |
| **Admin** | admin@someah.com | admin123 | Full access (semua fitur + kelola CS) |
| **CS** | cs@someah.com | cs123 | Limited access (chat, pelanggan, produk, pesanan) |

**Catatan:**
- Admin dapat menambah CS baru dari menu "Kelola CS"
- CS yang didaftarkan admin langsung aktif tanpa verifikasi email
- CS tidak bisa akses fitur: WhatsApp Connection, Reminder, Broadcast, Kampanye, Kelola CS

---

## ⚠️ Known Issues & Troubleshooting

### 1. Timestamp Chat Tidak Akurat (Selalu 07:00 AM)
Waktu pada bubble chat menampilkan jam 07:00 AM untuk semua pesan. Ini terjadi karena:
* Kolom `created_at` di tabel `chat_messages` tidak menyimpan waktu dengan benar (kemungkinan berisi NULL atau string).
* Model `ChatMessage` menonaktifkan timestamps (`public $timestamps = false`).

**Solusi Sementara:** Sistem tetap berfungsi normal, hanya tampilan waktu yang kurang akurat.

**Solusi Permanen (Opsional):**
1. Tambahkan kolom `created_at` dan `updated_at` ke tabel `chat_messages` via migration.
2. Ubah `public $timestamps = false` menjadi `true` di model `ChatMessage`.

### 2. Delay Real-time (Latency)
Terdapat jeda waktu (**latency**) sekitar **3-5 detik** antara pesan dikirim dari HP pelanggan hingga muncul di Dashboard CS. Hal ini normal karena sistem menggunakan mekanisme polling setiap 3 detik untuk mengecek pesan baru.

### 3. Halaman Tidak Redirect Otomatis
Setelah scan QR berhasil dan status berubah menjadi "TERHUBUNG", halaman belum otomatis berpindah.
* **Solusi:** Refresh halaman manual atau klik menu Dashboard.

### 4. Masalah "Fatal LOGOUT" Loop
Jika Anda melakukan *logout* paksa dari aplikasi WhatsApp di HP (Linked Devices), `wa-service` mungkin akan mengalami *crash loop* (meminta QR terus menerus).
* **Solusi Hard Reset:**
    1. Matikan container: `docker compose stop wa_service`
    2. Hapus volume docker/folder sesi `wa-service`.
    3. Nyalakan kembali dan scan ulang.

### 5. Webhook Error 404 / 500
Jika log `wa-service` menunjukkan error saat mengirim webhook:
* **404:** Cek kembali `WEBHOOK_URL` di `.env` wa-service. Pastikan ada prefix `/api/v1`.
* **500:** Cek log Laravel. Pastikan aplikasi berjalan dan database terkoneksi.

---

### 👨‍💻 Developer Notes

* **PENTING:** Fitur Broadcast menggunakan Job Queue. Jalankan `php artisan queue:work` agar pesan terkirim.
* Gunakan `php artisan config:clear` setiap kali mengubah file `.env`.

## 📅 Reminder Scheduler & Command

- **File `app/Console/Commands/SendPendingReminders.php`**  
  Artisan command `reminders:send` yang memproses semua reminder yang statusnya `pending` dan `scheduled_at` sudah lewat. Menampilkan progress bar, statistik pengiriman, dan mendukung opsi `--dry-run`.

- **File `config/reminder.php`**  
  Menyimpan konfigurasi waktu scheduler (`schedule_time`). Nilai default `09:00` dapat di‑override lewat environment variable `REMINDER_SCHEDULE_TIME`.

- **File `routes/console.php`**  
  Menjadwalkan command `reminders:send` setiap hari pada jam yang ditentukan di konfigurasi:
  ```php
  Schedule::command('reminders:send')
      ->dailyAt(config('reminder.schedule_time'))
      ->description('Kirim reminder pending setiap hari jam '.config('reminder.schedule_time'));
  ```

Dengan konfigurasi ini, admin dapat mengatur jam pengiriman reminder secara fleksibel, dan command dapat dijalankan manual (`php artisan reminders:send`) atau otomatis oleh scheduler Laravel.