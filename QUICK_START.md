# 🚀 Quick Start - Email Verification

## Setup Gmail (5 Menit)

### 1️⃣ Generate App Password Gmail
```
1. Buka: https://myaccount.google.com/security
2. Aktifkan "2-Step Verification"
3. Buka: https://myaccount.google.com/apppasswords
4. App name: "ROMS Laravel"
5. Klik "Create"
6. COPY 16 karakter password
```

### 2️⃣ Update .env
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=wyandhanupapoy@gmail.com
MAIL_PASSWORD=your_16_char_app_password_here  # ← Paste di sini (tanpa spasi)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=wyandhanupapoy@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**PENTING:** Hapus semua spasi dari App Password!

### 3️⃣ Clear Cache & Test
```powershell
php artisan config:clear
php artisan serve
```

### 4️⃣ Test Registrasi
```
1. Buka: http://localhost:8000/register
2. Isi form registrasi (gunakan email asli Anda)
3. Cek email → Dapatkan kode 6 digit
4. Input kode di halaman verify
5. ✅ Berhasil! Auto login ke dashboard
```

---

## 🔥 Flow Sistem

```
Register → Generate Kode → Kirim Email → Input Kode → Verified → Login → Dashboard
```

---

## 📝 Fitur yang Sudah Dibuat

✅ Registrasi dengan validasi
✅ Generate kode verifikasi 6 digit random
✅ Kirim email dengan template profesional
✅ Halaman verify dengan UI menarik
✅ Validasi kode (6 digit, 15 menit expired)
✅ Fitur kirim ulang kode
✅ Auto login setelah verifikasi
✅ Security: kode dihapus setelah digunakan

---

## 🎨 Halaman yang Tersedia

- `/register` - Form registrasi
- `/verify` - Input kode verifikasi
- `/login` - Login (jika sudah verified)
- `/dashboard` - Halaman setelah login

---

## 📧 Cara Kerja Email

**Dari:** wyandhanupapoy@gmail.com  
**Ke:** Email user yang mendaftar  
**Subject:** Kode Verifikasi ROMS - [KODE]  
**Isi:** Template HTML profesional dengan kode 6 digit

---

## ⚡ Troubleshooting Cepat

**Email tidak masuk?**
```powershell
# 1. Cek folder Spam/Junk
# 2. Clear cache
php artisan config:clear
# 3. Cek log
tail storage/logs/laravel.log
```

**Kode salah/kadaluarsa?**
- Klik tombol "Kirim Ulang Kode"
- Kode berlaku 15 menit
- Pastikan input 6 digit angka

---

## 📂 File Penting

```
app/
  ├── Http/Controllers/Auth/AuthControllers.php (sudah ada)
  ├── Models/EmailVerification.php ✅ BARU
  └── Mail/VerificationCodeMail.php ✅ BARU

resources/views/
  ├── auth/verify.blade.php ✅ BARU
  └── emails/verification.blade.php ✅ BARU

database/migrations/
  └── 2025_11_06_000000_create_email_verifications_table.php ✅ BARU
```

---

## 🎯 Testing Checklist

- [ ] Generate Gmail App Password
- [ ] Update `.env` dengan App Password
- [ ] Run `php artisan config:clear`
- [ ] Run `php artisan serve`
- [ ] Register user baru
- [ ] Cek email masuk (inbox/spam)
- [ ] Input kode verifikasi
- [ ] Berhasil login ke dashboard

---

**📖 Untuk tutorial lengkap, baca: `EMAIL_VERIFICATION_TUTORIAL.md`**
