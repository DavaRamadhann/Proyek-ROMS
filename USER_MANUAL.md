# 📘 Panduan Pengguna Lengkap ROMS (Repeat Order Management System)

**Versi Dokumen:** 1.0
**Terakhir Diperbarui:** November 2025

Selamat datang di **ROMS**, sistem manajemen pelanggan terpadu yang dirancang untuk membantu bisnis Anda meningkatkan penjualan berulang (repeat order), mengelola layanan pelanggan (CS) secara efisien, dan menjalankan kampanye pemasaran otomatis melalui WhatsApp.

---

## 📋 Daftar Isi

1.  [Pendahuluan](#1-pendahuluan)
2.  [Akses & Dashboard](#2-akses--dashboard)
3.  [Modul Customer Service (CS)](#3-modul-customer-service-cs)
    *   [Live Chat & Distribusi Otomatis](#31-live-chat--distribusi-otomatis)
    *   [Status Ketersediaan (Online/Offline)](#32-status-ketersediaan-onlineoffline)
    *   [Manajemen Pelanggan & Segmentasi](#33-manajemen-pelanggan--segmentasi)
4.  [Modul Admin & Operasional](#4-modul-admin--operasional)
    *   [Manajemen Produk & Cross-sell](#41-manajemen-produk--cross-sell)
    *   [Manajemen Pesanan (Order)](#42-manajemen-pesanan-order)
    *   [Koneksi WhatsApp](#43-koneksi-whatsapp)
5.  [Modul Automasi & Pemasaran](#5-modul-automasi--pemasaran)
    *   [Pengingat Repeat Order (Reminder)](#51-pengingat-repeat-order-reminder)
    *   [Broadcast Pesan Massal](#52-broadcast-pesan-massal)
    *   [Manajemen Template Pesan](#53-manajemen-template-pesan)
    *   [Otomasi Ucapan Terima Kasih](#54-otomasi-ucapan-terima-kasih)
6.  [Laporan & Analitik](#6-laporan--analitik)
7.  [Integrasi Teknis (API)](#7-integrasi-teknis-api)

---

## 1. Pendahuluan

ROMS mengintegrasikan manajemen pesanan, layanan pelanggan, dan pemasaran WhatsApp dalam satu platform. Sistem ini memiliki dua peran pengguna utama:
*   **Admin**: Memiliki akses penuh ke pengaturan sistem, laporan, manajemen produk, dan fitur automasi.
*   **Customer Service (CS)**: Fokus pada interaksi langsung dengan pelanggan melalui fitur Chat dan manajemen data pelanggan.

---

## 2. Akses & Dashboard

### Login
Akses aplikasi melalui browser web Anda. Masukkan email dan password yang telah didaftarkan oleh Administrator.

### Dashboard Utama
Setelah login, Anda akan disambut oleh Dashboard yang menyajikan ringkasan penting:
*   **Statistik Harian**: Jumlah chat masuk, pesanan baru, dan total penjualan hari ini.
*   **Status Sistem**: Indikator koneksi WhatsApp (Terhubung/Terputus).
*   **Navigasi**: Sidebar di sebelah kiri untuk mengakses semua menu fitur.

---

## 3. Modul Customer Service (CS)

Modul ini dirancang khusus untuk tim CS agar dapat melayani pelanggan dengan cepat dan efisien.

### 3.1 Live Chat & Distribusi Otomatis
ROMS menggunakan sistem **Round-robin** cerdas untuk membagi chat.
*   **Cara Kerja**: Chat baru dari pelanggan akan masuk secara otomatis ke CS yang sedang **ONLINE** secara bergantian. Ini memastikan beban kerja terbagi rata.
*   **Fitur Chat**:
    *   **Balas Cepat**: Ketik pesan dan tekan Enter.
    *   **Lampiran**: Kirim gambar atau dokumen (PDF, dll) melalui ikon klip kertas.
    *   **Info Pelanggan**: Panel kanan menampilkan detail pelanggan (Nama, No HP, Email, Segmen) saat Anda sedang chatting.

### 3.2 Status Ketersediaan (Online/Offline)
Fitur ini sangat krusial untuk distribusi chat.
*   **Lokasi Tombol**: Pojok kanan atas layar (Header).
*   **Fungsi**:
    *   🟢 **ONLINE**: Anda siap menerima chat baru. Sistem akan mengarahkan pelanggan ke Anda.
    *   ⚪ **OFFLINE**: Anda sedang istirahat atau di luar jam kerja. Chat baru **TIDAK** akan masuk ke Anda.
*   **Tips**: Selalu ubah status ke OFFLINE sebelum meninggalkan meja kerja.

### 3.3 Manajemen Pelanggan & Segmentasi
Menu **Pelanggan** menampilkan database seluruh kontak yang pernah berinteraksi.
*   **Edit Nama**: Jika nama di WhatsApp tidak jelas (misal: "Budi123"), Anda bisa mengeditnya menjadi nama asli (misal: "Budi Santoso") agar lebih profesional.
*   **Segmentasi Otomatis (RFM)**: Sistem otomatis memberi label pada pelanggan berdasarkan kebiasaan belanja mereka:
    *   🏆 **Big Spender**: Total belanja > Rp 1.000.000.
    *   ⭐ **Loyal**: Sudah order lebih dari 3 kali.
    *   💤 **Inactive**: Tidak belanja > 60 hari.
    *   🆕 **New Member**: Baru bergabung < 30 hari.
    *   👤 **Regular**: Pelanggan biasa.

---

## 4. Modul Admin & Operasional

### 4.1 Manajemen Produk & Cross-sell
Menu **Produk** digunakan untuk mengelola katalog barang yang Anda jual.
*   **Tambah/Edit Produk**: Masukkan Nama, SKU, Harga, dan Stok.
*   **Fitur Cross-sell/Upsell**:
    *   Pada form produk, terdapat kolom **"Rekomendasi Cross-sell"**.
    *   Isi dengan kalimat promosi untuk produk pendamping.
    *   *Contoh*: Jika produknya "Kopi Bubuk", isi rekomendasinya: *"Kak, sekalian Gula Aren-nya biar makin nikmat!"*
    *   Teks ini akan muncul otomatis di pesan Reminder pelanggan.

### 4.2 Manajemen Pesanan (Order)
Menu **Pesanan** mencatat semua transaksi.
*   **Input Manual**: Admin bisa memasukkan order baru secara manual jika transaksi terjadi di luar sistem (misal: via telepon).
*   **Status Order**: Pantau status (Pending, Processed, Shipped, Delivered, Cancelled).
*   **Pemicu Automasi**: Saat order dibuat, sistem otomatis mengirim pesan "Terima Kasih". Saat status "Delivered", sistem mulai menghitung waktu untuk pengiriman "Reminder".

### 4.3 Koneksi WhatsApp
Menu **Koneksi WhatsApp** (Khusus Admin).
*   **Scan QR**: Gunakan menu ini untuk menghubungkan nomor WhatsApp Bisnis Anda dengan sistem ROMS.
*   **Status**: Pastikan status selalu "Connected" agar fitur Chat, Broadcast, dan Reminder berjalan lancar.

---

## 5. Modul Automasi & Pemasaran

### 5.1 Pengingat Repeat Order (Reminder)
Fitur andalan untuk meningkatkan retensi pelanggan.
1.  Masuk ke menu **Reminder**.
2.  Klik **Buat Reminder**.
3.  **Pengaturan**:
    *   **Nama**: Misal "Reminder Kopi Habis".
    *   **Waktu**: Kirim **X hari** setelah pesanan statusnya *Delivered*.
    *   **Target Produk**: Bisa spesifik untuk produk tertentu, atau semua produk.
4.  **Pesan**:
    *   Gunakan variabel `{customer_name}`, `{product_name}`, dan `{recommendation}`.
    *   Variabel `{recommendation}` akan mengambil teks cross-sell dari produk yang dibeli pelanggan.

### 5.2 Broadcast Pesan Massal
Kirim info promo atau pengumuman ke banyak pelanggan sekaligus.
1.  Masuk ke menu **Broadcast**.
2.  **Target Audience**: Pilih segmen pelanggan (misal: Hanya ke "Big Spender" atau "Inactive").
3.  **Konten**: Tulis pesan atau pilih template. Bisa lampirkan gambar brosur.
4.  **Jadwal**: Kirim sekarang atau jadwalkan untuk nanti.

### 5.3 Manajemen Template Pesan
Buat format pesan standar agar CS tidak perlu mengetik ulang.
*   Buat template untuk berbagai keperluan: "Jawaban Salam", "Info Rekening", "Konfirmasi Pengiriman", dll.
*   Gunakan variabel dinamis seperti `{name}` agar pesan terasa personal.

### 5.4 Otomasi Ucapan Terima Kasih
Fitur ini berjalan otomatis di latar belakang.
*   Setiap kali ada pesanan baru (baik input manual atau dari API), sistem langsung mengirim pesan WhatsApp ke pelanggan.
*   Isi pesan bisa diatur melalui **Template Pesan** dengan tipe "Order Confirmation".

---

## 6. Laporan & Analitik

Menu **Laporan Bisnis** membantu Anda mengambil keputusan berbasis data.
*   **Top 10 Pelanggan**: Daftar pelanggan dengan frekuensi order tertinggi atau total belanja terbesar. Gunakan data ini untuk memberikan reward/hadiah khusus.
*   **Sebaran Geografis**: Peta/Tabel kota asal pelanggan Anda. Gunakan untuk menentukan target lokasi iklan Facebook/Instagram Ads.

---

## 7. Integrasi Teknis (API)

Menu **Integrasi API** ditujukan untuk tim Developer/IT Anda.
*   **Fungsi**: Menghubungkan Website Toko Online (Shopify, WooCommerce, atau Custom) dengan ROMS.
*   **Cara Pakai**:
    1.  Generate **API Key** di menu ini.
    2.  Gunakan Endpoint `POST /api/v1/orders` untuk mengirim data order dari website ke ROMS.
    3.  Dokumentasi lengkap parameter (JSON Body) tersedia langsung di halaman menu ini.

---

**Catatan Tambahan:**
*   Pastikan koneksi internet stabil untuk penggunaan fitur Real-time Chat.
*   Jika WhatsApp terputus, segera lakukan Scan QR ulang di menu Koneksi WhatsApp.

**Butuh Bantuan Lebih Lanjut?**
Hubungi Administrator Sistem atau Tim Support Teknis Internal Anda.

---
*Dibuat oleh Tim Pengembang ROMS*
