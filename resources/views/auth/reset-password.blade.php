@extends('layout.app')

@section('title', 'Reset Password - ROMS')

@section('content')
<style>
    /* Ini adalah style yang sama dari halaman Lupa Password */
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
        margin-bottom: 10px; /* Tambahan margin */
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
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.9rem;
        margin-top: -5px;
        margin-bottom: 10px;
    }
</style>

<div class="verify-container">
    <div class="verify-box">
        <div class="verify-header">
            <div class="verify-icon">
                🔑
            </div>
            <h3>Buat Password Baru</h3>
            <p>Masukkan password baru Anda. Pastikan password kuat dan mudah diingat.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                ❌ {{ $errors->first() }}
            </div>
        @endif

        @if(session('status'))
            <div class="alert alert-success">
                ✓ {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            
            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       id="password"
                       name="password"
                       required
                       placeholder="Masukkan password baru">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                <input type="password"
                       class="form-control"
                       id="password_confirmation"
                       name="password_confirmation"
                       required
                       placeholder="Ulangi password baru">
            </div>

            <button type="submit" class="btn btn-verify">
                Simpan Password Baru
            </button>
        </form>

    </div>
</div>
@endsection