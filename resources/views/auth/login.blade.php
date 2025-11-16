@extends('layout.app')

@section('title', 'Login - ROMS')

@section('content')
<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .login-container {
        display: flex;
        width: 100vw;
        height: 100vh;
    }

    .login-left {
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

    .login-left h2 {
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 20px;
    }

    .login-left p {
        font-size: 0.95rem;
        opacity: 0.95;
        line-height: 1.5;
        max-width: 80%;
    }

    .login-right {
        flex: 1;
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
    }

    .login-box {
        width: 90%;
        max-width: 480px;
    }

    .login-box h3 {
        color: #B45253;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .login-box p {
        color: #555;
        font-size: 0.95rem;
        margin-bottom: 25px;
    }

    .form-label {
        font-weight: 500;
        color: #333;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #ddd;
        padding: 10px 14px;
        font-size: 0.95rem;
    }

    .form-control:focus {
        border-color: #FCB53B;
        box-shadow: 0 0 0 0.2rem rgba(252, 181, 59, 0.25);
    }

    .btn-login {
        background-color: #FCB53B;
        border: none;
        border-radius: 10px;
        padding: 10px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
        font-size: 0.95rem;
    }

    .btn-login:hover {
        background-color: #B45253;
        transform: translateY(-2px);
    }

    .divider {
        text-align: center;
        margin: 20px 0;
        color: #999;
        position: relative;
    }

    .divider::before, .divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 40%;
        height: 1px;
        background: #ddd;
    }

    .divider::before {
        left: 0;
    }

    .divider::after {
        right: 0;
    }

    .social-btn {
        border-radius: 10px;
        border: 1px solid #ccc;
        padding: 12px;
        background-color: #fff;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .social-btn:hover {
        background-color: #FFE797;
        border-color: #FCB53B;
    }

    .register-link {
        margin-top: 20px;
        text-align: center;
        color: #555;
    }

    .register-link a {
        color: #B45253;
        font-weight: 600;
        text-decoration: none;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    @media (max-width: 992px) {
        .login-container {
            flex-direction: column;
        }

        .login-left {
            display: none;
        }

        .login-right {
            padding: 40px 20px;
            height: 100vh;
        }
    }
</style>

<div class="login-container">
    <div class="login-left">
        <h2>Bergabung dengan ROMS Sekarang!</h2>
        <p>Daftar dan nikmati kemudahan dalam mengelola pengiriman Anda.  
           ROMS membantu bisnis dan pelanggan dengan sistem pengiriman yang cepat dan efisien.</p>
    </div>

    <div class="login-right">
        <div class="login-box">
            <h3>Halo Sahabat ROMS!</h3>
            <p>Selamat datang kembali. Silakan login menggunakan email dan password Anda.</p>

            <form method="POST" action="/login">
                @csrf
                @if ($errors->has('email'))
                    <div class="alert alert-danger text-center" role="alert">
                        {{ $errors->first('email') }}
                    </div>
                @endif

                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="email"
                           placeholder="Masukkan email"
                           pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                           title="Hanya alamat email yang valid yang diizinkan">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Kata Sandi *</label>
                    <input type="password"
                           class="form-control @error('password') is-invalid @enderror"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="Masukkan kata sandi">
                </div>

                <div class="mb-3 text-end">
                    <a href="{{ route('password.request') }}" style="color: #B45253; text-decoration: none; font-size: 0.9rem;">
                        Lupa Kata Sandi?
                    </a>
                </div>

                <button type="submit" class="btn btn-login">Masuk</button>

                <script>
                document.getElementById('email').addEventListener('input', function () {
                    const emailField = this;
                    const pattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                    if (!pattern.test(emailField.value) && emailField.value !== '') {
                        emailField.setCustomValidity("Hanya alamat email yang valid yang diizinkan.");
                    } else {
                        emailField.setCustomValidity("");
                    }
                });
                </script>
            </form>

            <div class="divider">atau</div>

            <div class="d-grid gap-2">
                <a href="{{ route('google.redirect') }}" class="social-btn">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" style="width: 20px; height: 20px;">
                    Masuk dengan Google
                </a>
            </div>

            <div class="register-link">
                Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
            </div>
        </div>
    </div>
</div>
@endsection