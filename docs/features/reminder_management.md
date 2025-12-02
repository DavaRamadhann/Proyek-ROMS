# Fitur: Manajemen Pengingat (Reminder Management)

## Deskripsi
Fitur ini adalah inti dari "Repeat Order Management System" (ROMS). Fitur ini memungkinkan admin untuk membuat aturan (rules) kapan pesan pengingat harus dikirim kepada pelanggan setelah mereka menyelesaikan pesanan. Tujuannya adalah mendorong pembelian ulang secara otomatis.

## Alur Kerja (Workflow)

### 1. Konfigurasi Aturan Pengingat (Rule Setup)
- **Aktor**: Admin
- **Proses**:
  1. Admin membuat aturan baru di menu Reminder.
  2. Admin menentukan:
     - **Nama Rule**: Misal "Pengingat Kopi 30 Hari".
     - **Produk Terkait**: (Opsional) Rule ini berlaku untuk produk apa.
     - **Waktu Tunggu**: Jumlah hari setelah pesanan selesai (misal: 30 hari).
     - **Template Pesan**: Isi pesan WhatsApp yang akan dikirim (mendukung placeholder seperti `{name}`).
  3. `ReminderController::store` menyimpan aturan ini ke tabel `reminders`.

### 2. Penjadwalan Pengingat (Scheduling)
- **Pemicu**: Perubahan status pesanan menjadi `completed` di modul Order.
- **Proses**:
  1. Ketika order selesai, sistem mengecek aturan yang aktif.
  2. Sistem membuat jadwal pengiriman di tabel `reminder_logs`.
  3. `scheduled_at` diisi dengan `waktu_sekarang + days_after_delivery`.
  4. Status awal log adalah `pending`.

### 3. Eksekusi Pengingat (Sending)
- **Aktor**: Sistem (Scheduler/Cron Job - *Perlu verifikasi implementasi cron*)
- **Proses (Ideal/To-Be)**:
  1. Scheduler berjalan setiap hari/jam.
  2. Mencari `reminder_logs` dengan status `pending` dan `scheduled_at <= now()`.
  3. Mengirim pesan WhatsApp menggunakan `ChatService` atau layanan WA terkait.
  4. Mengupdate status log menjadi `sent` atau `failed`.

## Komponen Teknis
- **Controller**: `ReminderController`
- **Model**: `Reminder` (Rule), `ReminderLog` (Jadwal Eksekusi)
- **Relasi**:
  - ReminderLog belongs to Order.
  - ReminderLog belongs to Customer.
  - ReminderLog belongs to Reminder (Rule).

## Batasan Saat Ini
- Logika eksekusi pengiriman otomatis (Cron Job/Worker) belum terlihat secara eksplisit di controller (kemungkinan perlu setup scheduler terpisah).
- Personalisasi pesan masih terbatas pada placeholder sederhana.
- Belum ada fitur untuk membatalkan pengingat jika pelanggan sudah memesan lagi sebelum waktu pengingat tiba (Smart Cancellation).
