# Fitur: Manajemen Pelanggan (Customer Management)

## Deskripsi
Fitur dasar untuk mengelola data pelanggan. Data ini menjadi pusat bagi fitur lainnya (Chat, Order, Reminder). Sistem menyimpan informasi kontak dan preferensi pelanggan.

## Alur Kerja (Workflow)

### 1. Pencatatan Pelanggan Baru
- **Cara 1 (Otomatis via Chat)**:
  - Saat pesan masuk dari nomor baru, `ChatService` otomatis membuat data pelanggan baru.
  - Nama pelanggan diambil dari profil WhatsApp atau diset sama dengan nomor telepon jika tidak ada.
- **Cara 2 (Manual via Order/Menu)**:
  - Admin dapat menginput data pelanggan secara manual saat membuat pesanan atau lewat menu Pelanggan.

### 2. Pengelolaan Data
- **Aktor**: Admin / CS
- **Proses**:
  - Melihat daftar pelanggan dengan fitur pencarian (Nama/No HP).
  - Mengedit detail pelanggan (Email, Tag Segmen).
  - Sistem memiliki flag `is_manual_name`. Jika admin mengubah nama secara manual, sistem tidak akan menimpa nama tersebut dengan nama profil WhatsApp lagi di masa depan.

## Komponen Teknis
- **Controller**: `CustomerController`
- **Model**: `Customer`
- **Database**: Tabel `customers` menyimpan `name`, `phone`, `email`, `is_manual_name`.

## Batasan Saat Ini
- Belum ada profil pelanggan 360 derajat yang menampilkan total belanja, frekuensi beli, atau nilai LTV (Lifetime Value).
- Segmentasi masih manual (input string), belum otomatis berdasarkan perilaku belanja.
