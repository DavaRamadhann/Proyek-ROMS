@extends('layout.app')

@section('title', 'Verifikasi Email - ROMS')

@section('content')
<style>
    /* =================================================
    BAGIAN CSS (STYLE)
    =================================================
    */
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

    .user-email {
        background-color: #f8f9fa;
        padding: 12px 20px;
        border-radius: 10px;
        text-align: center;
        margin: 20px 0;
        border: 2px dashed #84994F;
    }

    .user-email strong {
        color: #84994F;
        font-size: 1.05rem;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: block;
        text-align: center; 
    }

    .code-input-container {
        display: flex;
        justify-content: center;
        gap: 10px; 
        margin-top: 15px; 
    }

    .code-box {
        width: 48px;  
        height: 60px; 
        font-size: 1.8rem; 
        text-align: center;
        border: 2px solid #ddd;
        border-radius: 10px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        transition: all 0.3s ease;
        -moz-appearance: textfield;
    }

    /* Ini style untuk placeholder '0' abu-abu */
    .code-box::placeholder {
        color: #ced4da; 
        opacity: 1; 
        font-weight: 700; 
    }

    .code-box::-webkit-outer-spin-button,
    .code-box::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .code-box:focus {
        border-color: #FCB53B;
        box-shadow: 0 0 0 0.3rem rgba(252, 181, 59, 0.25);
        outline: none;
    }

    .code-box.is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.9rem;
        margin-top: 8px;
        text-align: center;
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
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(180, 82, 83, 0.3);
    }

    .btn-verify:active {
        transform: translateY(0);
    }

    .divider {
        text-align: center;
        margin: 25px 0;
        color: #999;
        position: relative;
        font-size: 0.9rem;
    }

    .divider::before, .divider::after {
        content: '';
        position: absolute;
        top: 50%;
        width: 42%;
        height: 1px;
        background: #ddd;
    }

    .divider::before {
        left: 0;
    }

    .divider::after {
        right: 0;
    }

    .resend-section {
        text-align: center;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 12px;
    }

    .resend-text {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 12px;
    }

    .btn-resend {
        background-color: transparent;
        border: 2px solid #84994F;
        border-radius: 10px;
        padding: 10px 25px;
        color: #84994F;
        font-weight: 600;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-resend:hover {
        background-color: #84994F;
        color: white;
    }

    .alert {
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
        font-size: 0.95rem;
        border: none;
    }

    .alert-success {
        background-color: #d1f2eb;
        color: #0c5d47;
        border-left: 4px solid #28a745;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    .info-box {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .info-box p {
        margin: 0;
        color: #856404;
        font-size: 0.9rem;
        line-height: 1.5;
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

    .back-link a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .verify-box {
            padding: 40px 25px;
            margin: 20px;
        }

        .code-box {
            width: 40px;
            height: 55px;
            font-size: 1.5rem;
        }
        .code-input-container {
            gap: 8px;
        }

        .verify-header h3 {
            font-size: 1.5rem;
        }
    }
</style>

<div class="verify-container">
    <div class="verify-box">
        <div class="verify-header">
            <div class="verify-icon">
                🔐
            </div>
            <h3>Verifikasi 
                @if($verificationType === 'register')
                    Pendaftaran
                @elseif($verificationType === 'google')
                    Google Login
                @else
                    Reset Password
                @endif
            </h3>
            <p>Masukkan kode verifikasi 6 digit yang telah kami kirimkan</p>
        </div>

        @if(session('status'))
            <div class="alert alert-success">
                ✉️ {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                ❌ {{ $errors->first() }}
            </div>
        @endif

        <div class="user-email">
            📧 Kode dikirim ke: <strong>{{ $email }}</strong>
        </div>

        {{-- 
        =================================================
        BAGIAN HTML (KONTEN)
        =================================================
        --}}
        <form method="POST" action="@if($verificationType === 'google') {{ route('verification.google.verify') }} @elseif($verificationType === 'reset_password') {{ route('password.verify') }} @else {{ route('verification.verify') }} @endif" id="verifyForm">
            @csrf
            <div class="mb-3">
                <label for="code-1" class="form-label">Kode Verifikasi</label>
                
                <div class="code-input-container" id="code-container">
                    <input type="tel" class="code-box" id="code-1" placeholder="0" maxlength="1" pattern="[0-9]" required>
                    <input type="tel" class="code-box" id="code-2" placeholder="0" maxlength="1" pattern="[0-9]" required>
                    <input type="tel" class="code-box" id="code-3" placeholder="0" maxlength="1" pattern="[0-9]" required>
                    <input type="tel" class="code-box" id="code-4" placeholder="0" maxlength="1" pattern="[0-9]" required>
                    <input type="tel" class="code-box" id="code-5" placeholder="0" maxlength="1" pattern="[0-9]" required>
                    <input type="tel" class="code-box" id="code-6" placeholder="0" maxlength="1" pattern="[0-9]" required>
                </div>
                
                <input type="hidden" name="code" id="verification-code">
                
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-verify">
                ✓ Verifikasi Sekarang
            </button>
        </form>

        <div class="divider">atau</div>

        <div class="resend-section">
            <p class="resend-text">Tidak menerima kode?</p>
            <form method="POST" action="@if($verificationType === 'google') {{ route('verification.google.resend') }} @elseif($verificationType === 'reset_password') {{ route('password.resend') }} @else {{ route('verification.resend') }} @endif" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-resend">
                    🔄 Kirim Ulang Kode
                </button>
            </form>
        </div>

        <div class="info-box">
            <p>
                <strong>💡 Tips:</strong> Periksa folder spam/junk jika tidak menemukan email. 
                Kode berlaku selama 15 menit.
            </p>
        </div>

        <div class="back-link">
            <a href="{{ $verificationType === 'register' ? route('login') : route('password.request') }}">← Kembali ke {{ $verificationType === 'register' ? 'Login' : 'Lupa Password' }}</a>
        </div>
    </div>
</div>

{{-- 
=================================================
BAGIAN SCRIPT (JAVASCRIPT)
=================================================
--}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const codeContainer = document.getElementById('code-container');
    const boxes = codeContainer.querySelectorAll('.code-box');
    const hiddenInput = document.getElementById('verification-code');
    const verifyForm = document.getElementById('verifyForm');

    boxes.forEach((box, index) => {
        
        box.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');

            if (e.target.value.length === 1 && index < boxes.length - 1) {
                boxes[index + 1].focus();
            }
            
            updateHiddenInput();
        });

        box.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && e.target.value.length === 0 && index > 0) {
                boxes[index - 1].focus();
            }
        });

        box.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasteData = e.clipboardData.getData('text').trim();
            
            if (pasteData.length === boxes.length && /^[0-9]+$/.test(pasteData)) {
                boxes.forEach((b, i) => {
                    b.value = pasteData[i];
                });
                boxes[boxes.length - 1].focus();
                updateHiddenInput();
            }
        });
    });

    function updateHiddenInput() {
        let combinedCode = "";
        boxes.forEach(box => {
            combinedCode += box.value;
        });
        hiddenInput.value = combinedCode;

        if (combinedCode.length === 6) {
            // verifyForm.submit(); // (Opsional auto-submit)
        }
    }

    boxes[0].focus();
});
</script>
@endsection