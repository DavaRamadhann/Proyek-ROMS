# Fitur: Manajemen Broadcast (Broadcast Management)

## Deskripsi
Fitur ini memungkinkan admin untuk mengirimkan pesan massal (siaran) kepada segmen pelanggan tertentu. Ini digunakan untuk promosi, pengumuman produk baru, atau ucapan hari raya.

## Alur Kerja (Workflow)

### 1. Pembuatan Broadcast
- **Aktor**: Admin
- **Proses**:
  1. Admin masuk ke menu Broadcast.
  2. Admin mengisi form:
     - **Nama Campaign**: Judul internal untuk broadcast.
     - **Target Segment**: Kriteria penerima (saat ini input teks manual/filter sederhana).
     - **Isi Pesan**: Konten teks yang akan dikirim.
  3. `BroadcastController::store` menyimpan data broadcast.

### 2. Pemrosesan Broadcast
- **Aktor**: Sistem (Queue Job)
- **Proses**:
  1. Setelah broadcast dibuat, controller langsung men-dispatch job `ProcessBroadcast`.
  2. Job ini berjalan di latar belakang (asynchronous).
  3. Job akan:
     - Mengambil daftar pelanggan yang sesuai dengan target segment (Logic filtering perlu detail lebih lanjut di implementasi Job).
     - Melakukan iterasi (looping) untuk setiap pelanggan.
     - Mengirim pesan WhatsApp satu per satu.
     - Mencatat log pengiriman untuk setiap pelanggan.
  4. Status broadcast diperbarui menjadi `completed` setelah selesai.

## Komponen Teknis
- **Controller**: `BroadcastController`
- **Model**: `Broadcast`
- **Job**: `ProcessBroadcast` (Menangani antrian pengiriman agar tidak timeout).

## Batasan Saat Ini
- **Segmentasi Terbatas**: Input target segment masih berupa string, belum ada UI builder untuk filter kompleks (misal: "Pelanggan yang beli > 5 kali").
- **Format Pesan**: Hanya mendukung teks. Belum ada dukungan untuk upload gambar atau template pesan interaktif (buttons/list) di UI pembuatan.
- **Jadwal**: Broadcast langsung diproses saat dibuat (`ProcessBroadcast::dispatch`), belum ada fitur penjadwalan waktu kirim (Scheduled Broadcast).
