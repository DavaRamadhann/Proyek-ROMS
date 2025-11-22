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
3.  **WebSocket Server (Soketi):**
    * Bertindak sebagai jembatan *real-time* (Pusher Protocol).
    * Memungkinkan pesan baru muncul di layar CS tanpa perlu *refresh* halaman.

---

## 🛠️ Prasyarat (Requirements)

Agar sistem berjalan lancar di lingkungan lokal (Localhost), pastikan Anda memiliki:

1.  **PHP 8.2+ & Composer** (Untuk Laravel).
2.  **Node.js & NPM** (Untuk Soketi & Frontend assets).
3.  **Docker Desktop** (**WAJIB**).
    * Diperlukan agar kontainer `wa-service` bisa berkomunikasi dengan Laravel via `host.docker.internal`.
4.  **Soketi** (Untuk fitur Real-time Chat).

---

## ⚡ Panduan Instalasi Singkat

### 1. Setup WebSocket Server (Soketi)

Soketi wajib dijalankan agar chat bisa masuk secara *real-time*.

* **Install Soketi (Global via NPM):**
    ```bash
    npm install -g @soketi/soketi
    ```
* **Jalankan Soketi:**
    ```bash
    soketi start
    ```
    *(Biarkan terminal ini terbuka selama pengembangan. Default port: 6001)*

### 2. Setup Laravel (Host)

Konfigurasi file `.env` Anda agar terhubung dengan Soketi dan `wa-service`.

* **Broadcast & Pusher Config (Wajib diisi):**
    ```env
    BROADCAST_CONNECTION=pusher

    PUSHER_APP_ID=app-id
    PUSHER_APP_KEY=app-key
    PUSHER_APP_SECRET=app-secret
    PUSHER_HOST=127.0.0.1
    PUSHER_PORT=6001
    PUSHER_SCHEME=http
    PUSHER_APP_CLUSTER=mt1

    VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    VITE_PUSHER_HOST="${PUSHER_HOST}"
    VITE_PUSHER_PORT="${PUSHER_PORT}"
    VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
    VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
    ```

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

## 🚦 Status Fitur (Updated: 20 Nov 2025)

| Fitur | Status | Keterangan |
| :--- | :---: | :--- |
| **Koneksi WhatsApp** | ✅ | Scan QR Code & Session Management (Multi-device ready). |
| **Kirim Pesan (Outbound)** | ✅ | CS dapat membalas chat dari Dashboard ke nomor WA pelanggan. |
| **Terima Pesan (Inbound)** | ✅ | Webhook aktif, pesan masuk tersimpan otomatis di database. |
| **Real-time Chat** | ✅ | Pesan masuk muncul otomatis (via Soketi). |
| **Auto-Assign CS** | ✅ | Logika *Round-robin* untuk membagi chat ke CS yang tersedia. |
| **Manajemen Kontak** | ⚠️ | Nomor HP masih format raw (`@c.us`) dan nama belum sinkron. |
| **Waktu Pesan** | ⚠️ | Timestamp belum dikonversi ke zona waktu lokal (WIB). |

---

## ⚠️ Known Issues & Troubleshooting

### 1. Delay Real-time (Latency)
Saat ini terdapat jeda waktu (**latency**) sekitar **5-10 detik** antara pesan dikirim dari HP pelanggan hingga muncul di Dashboard CS. Hal ini dipengaruhi oleh kecepatan pemrosesan Webhook dari `wa-service` ke Laravel.

### 2. Halaman Tidak Redirect Otomatis
Setelah scan QR berhasil dan status berubah menjadi "TERHUBUNG", halaman belum otomatis berpindah.
* **Solusi:** Refresh halaman manual atau klik menu Dashboard.

### 3. Masalah "Fatal LOGOUT" Loop
Jika Anda melakukan *logout* paksa dari aplikasi WhatsApp di HP (Linked Devices), `wa-service` mungkin akan mengalami *crash loop* (meminta QR terus menerus).
* **Solusi Hard Reset:**
    1.  Matikan container: `docker compose stop wa_service`
    2.  Hapus volume docker/folder sesi `wa-service`.
    3.  Nyalakan kembali dan scan ulang.

### 4. Webhook Error 404 / 500
Jika log `wa-service` menunjukkan error saat mengirim webhook:
* **404:** Cek kembali `WEBHOOK_URL` di `.env` wa-service. Pastikan ada prefix `/api/v1`.
* **500:** Cek log Laravel. Biasanya karena Soketi belum dinyalakan (`soketi start`).

---

### 👨‍💻 Developer Notes

* Jangan lupa menjalankan `php artisan queue:work` jika nanti mengaktifkan antrean proses.
* Gunakan `php artisan config:clear` setiap kali mengubah file `.env`.