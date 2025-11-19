## 📦 Integrasi WhatsApp Service

Sistem ini mengadopsi arsitektur *microservice* yang terdiri dari:
1.  **Aplikasi Utama (Laravel):** Bertindak sebagai *proxy* API dan *frontend* manajemen.
2.  **`wa-service` (Node.js):** Bertindak sebagai *backend* yang menjalankan Puppeteer dan memelihara koneksi WebSocket ke WhatsApp.

### Prasyarat Pengembangan (Development Requirements)

Pengembangan alur ini di lingkungan lokal **mewajibkan** penggunaan **Docker Desktop**.

**Alasan Teknis:**
Kontainer `wa-service` perlu mengirim *webhook* (misalnya, pesan WhatsApp yang masuk) kembali ke *server* Laravel yang berjalan di *host* (misal: `php artisan serve` di `localhost:8000`).

Docker Desktop menyediakan *hostname* internal, `host.docker.internal`, yang mengizinkan kontainer mengakses `localhost` milik mesin *host*. Konfigurasi ini sudah disiapkan di `.env.sample` milik `wa-service`.

### ⚠️ Known Issues (Status: 16 Nov 2025)

Ini adalah *checkpoint* stabil pertama. Terdapat beberapa fungsionalitas yang belum selesai dan *bug* yang diketahui:

1.  **Halaman Tidak Redirect:** Setelah pemindaian berhasil dan status "TERHUBUNG", antarmuka (UI) belum diimplementasikan untuk melakukan *redirect* otomatis ke halaman *chat*.
2.  **Fatal `LOGOUT` Crash:** Jika terjadi *logout* dari aplikasi WhatsApp di HP (via menu Perangkat Tertaut), `wa-service` akan menerima event `LOGOUT`. Ini akan memicu *handler* *fatal disconnect* dan menyebabkan *service* *crash* lalu *restart* dalam *loop* (meminta QR baru terus-menerus).
    * **Solusi Sementara:** Jika *loop* ini terjadi, diperlukan *hard reset* sesi:
        1.  Lakukan *logout* manual dari semua sesi di aplikasi WhatsApp HP.
        2.  Matikan kontainer `wa-service` (`docker compose stop wa_service`).
        3.  Hapus *Docker volume* yang terkait dengan `wa-service` (yang berisi `db.sqlite` dan folder `sessions`).
        4.  Jalankan ulang `wa-service` (`docker compose up -d`) dan lakukan pemindaian QR baru.