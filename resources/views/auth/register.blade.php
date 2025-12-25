@extends('layout.app')

@section('title', 'Register - ROMS')

@section('content')
<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .register-container {
        display: flex;
        width: 100vw;
        height: 100vh;
    }

    .register-left {
        flex: 1;
        background-color: #84994F;
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        padding: 40px;
    }

    .register-left h2 {
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 20px;
    }

    .register-left p {
        font-size: 0.95rem;
        opacity: 0.95;
        line-height: 1.5;
        max-width: 80%;
    }

    .register-right {
        flex: 1;
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
    }

    .register-box {
        width: 90%;
        max-width: 420px;
    }

    .register-box h3 {
        color: #B45253;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 1.2rem;
    }

    .register-box p {
        color: #555;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    .form-label {
        font-weight: 500;
        color: #333;
        font-size: 0.9rem;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #ddd;
        padding: 8px 12px;
        font-size: 0.9rem;
    }

    .form-control:focus {
        border-color: #FCB53B;
        box-shadow: 0 0 0 0.15rem rgba(252, 181, 59, 0.25);
    }

    .btn-register {
        background-color: #FCB53B;
        border: none;
        border-radius: 10px;
        padding: 8px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        font-size: 0.9rem;
    }

    .btn-register:hover {
        background-color: #B45253;
        transform: translateY(-2px);
    }

    .divider {
        text-align: center;
        margin: 15px 0;
        color: #999;
        position: relative;
        font-size: 0.85rem;
    }

    .divider::before, .divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 40%;
        height: 1px;
        background: #ddd;
    }

    .divider::before { left: 0; }
    .divider::after { right: 0; }

    .social-btn {
        border-radius: 10px;
        border: 1px solid #ccc;
        padding: 10px;
        background-color: #fff;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .social-btn:hover {
        background-color: #FFE797;
        border-color: #FCB53B;
    }

    .login-link {
        margin-top: 12px;
        text-align: center;
        color: #555;
        font-size: 0.85rem;
    }

    .login-link a {
        color: #B45253;
        font-weight: 600;
        text-decoration: none;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    @media (max-width: 992px) {
        .register-container {
            flex-direction: column;
            height: 100vh;
        }

        .register-left {
            display: none;
        }

        .register-right {
            padding: 30px 20px;
            height: 100vh;
        }

        .register-box {
            max-width: 360px;
        }
    }
</style>

<div class="register-container">
    <div class="register-left">
        <h2>Bergabung dengan ROMS Sekarang!</h2>
        <p>Daftar dan nikmati kemudahan dalam mengelola pengiriman Anda.  
           ROMS membantu bisnis dan pelanggan dengan sistem pengiriman yang cepat dan efisien.</p>
    </div>

    <div class="register-right">
        <div class="register-box">
            <h3>Selamat Datang di ROMS!</h3>
            <p>Belum Punya Akun? Yuk, Gabung Sekarang Juga!</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-2">
                    <label for="name" class="form-label">Nama Lengkap *</label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required autofocus
                           placeholder="Masukkan nama">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-2">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           placeholder="Masukkan email"
                           placeholder="Masukkan email">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label">Kata Sandi *</label>
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           required
                           placeholder="Masukkan kata sandi">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi *</label>
                    <input type="password"
                           class="form-control"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           placeholder="Masukkan kembali kata sandi">
                </div>

                <button type="submit" class="btn-register">Daftar Sekarang</button>

                <script>
                
                document.getElementById('email').addEventListener('input', function () {
                    const emailField = this;
                    // Allow all email domains
                    if (emailField.value !== '') {
                        emailField.setCustomValidity("");
                    } else {
                        emailField.setCustomValidity("");
                    }
                });
                </script>
            </form>

            <div class="divider">atau</div>

            <div class="d-grid gap-2">
                <a href="{{ route('google.redirect') }}" class="social-btn">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                         alt="Google" style="width: 18px; height: 18px;">
                    Daftar dengan Google
                </a>
            </div>

            <div class="login-link">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk Sekarang</a>
            </div>
        </div>
    </div>
</div>
@endsection