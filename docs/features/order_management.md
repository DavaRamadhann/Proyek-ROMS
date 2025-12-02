# Fitur: Manajemen Pesanan (Order Management)

## Deskripsi
Fitur ini memungkinkan pencatatan dan pengelolaan pesanan pelanggan secara manual. Fitur ini menjadi dasar bagi fitur otomatisasi lainnya seperti pengingat repeat order. Saat ini, pesanan diinput oleh admin/CS, namun struktur data sudah disiapkan untuk integrasi masa depan.

## Alur Kerja (Workflow)

### 1. Pembuatan Pesanan (Manual)
- **Aktor**: Admin / CS
- **Proses**:
  1. User membuka halaman "Buat Pesanan Baru".
  2. User memilih Pelanggan dari database atau membuat baru.
  3. User menambahkan item produk (Nama Produk, Kuantitas, Harga).
  4. Sistem menghitung total harga secara otomatis.
  5. User menyimpan pesanan.
  6. `OrderController::store` menyimpan data ke tabel `orders` dan `order_items`.
  7. Status awal pesanan adalah `pending`.

### 2. Update Status Pesanan
- **Aktor**: Admin / CS
- **Proses**:
  1. User melihat daftar pesanan di halaman index.
  2. User mengubah status pesanan (misal: dari `pending` ke `shipped` atau `completed`).
  3. `OrderController::updateStatus` memproses perubahan.
  4. Jika status berubah menjadi `shipped`, tanggal pengiriman (`shipped_at`) dicatat otomatis.
  5. Jika status berubah menjadi `completed`, sistem memicu **Jadwal Pengingat Otomatis**.

### 3. Pemicu Pengingat Otomatis (Integration Point)
- **Logika**:
  - Saat status pesanan menjadi `completed`, method `scheduleReminder` dipanggil.
  - Sistem mencari aturan pengingat (`Reminder Rule`) yang aktif (saat ini mengambil rule pertama/default).
  - Sistem menghitung tanggal pengiriman pengingat (misal: 30 hari setelah hari ini).
  - Sistem membuat entri di tabel `reminder_logs` dengan status `pending`.
  - Ini menghubungkan modul Order dengan modul Reminder.

## Komponen Teknis
- **Controller**: `OrderController`
- **Model**: `Order`, `OrderItem`
- **Relasi**: 
  - Order belongs to Customer.
  - Order has many OrderItems.
  - Order has one ReminderLog (opsional).

## Batasan Saat Ini
- Input pesanan masih manual, belum ada integrasi otomatis dengan Shopify/WooCommerce.
- Belum ada fitur cetak invoice atau label pengiriman.
- Logika pemilihan rule pengingat masih sederhana (ambil rule pertama yang aktif), belum berdasarkan kategori produk spesifik secara dinamis.
