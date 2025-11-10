@extends('layout.app')

@section('title', 'Lupa Password - ROMS')

@section('content')
<style>
    /* Ini adalah style yang sama dari halaman Verifikasi */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #FFE797;
        margin: 0;
        padding: 0;
    }
    .verify-container {
        display: flex;
        min-height: 100vh;
        align-items: center;
        justify-content: center;
    }
    .verify-box {
        background-color: #fff;
        border-radius: 20px;
        padding: 50px 40px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
    .verify-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .verify-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #84994F 0%, #6B7D3F 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 40px;
    }
    .verify-header h3 {
        color: #B45253;
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 1.8rem;
    }
    .verify-header p {
        color: #555;
        font-size: 0.95rem;
        line-height: 1.6;
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
        width: 100%;
    }
    .form-control:focus {
        border-color: #FCB53B;
        box-shadow: 0 0 0 0.2rem rgba(252, 181, 59, 0.25);
    }
    .btn-verify {
        background-color: #FCB53B;
        border: none;
        border-radius: 12px;
        padding: 14px;
        color: white;
        font-weight: 600;
        font-size: 1.05rem;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 20px;
        cursor: pointer;
    }
    .btn-verify:hover {
        background-color: #B45253;
    }
    .back-link {
        text-align: center;
        margin-top: 20px;
    }
    .back-link a {
        color: #84994F;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
</style>

<div class="verify-container">
    <div class="verify-box">
        <div class="verify-header">
            <div class="verify-icon">
                📧
            </div>
            <h3>Lupa Password</h3>
            <p>Masukkan alamat email Anda yang terdaftar. Kami akan mengirimkan kode verifikasi.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                ❌ {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email"
                       class="form-control @error('email') is-invalid @enderror"
                       id="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autofocus
                       placeholder="Masukkan email Anda">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-verify">
                Kirim Kode Verifikasi
            </button>
        </form>

        <div class="back-link">
            <a href="/login">← Kembali ke Login</a>
        </div>
    </div>
</div>
@endsection