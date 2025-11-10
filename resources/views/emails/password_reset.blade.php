<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ROMS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9fafb;
            padding: 20px;
            margin: 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .email-header {
            background: linear-gradient(135deg, #84994F 0%, #6B7D3F 100%);
            color: #fff;
            text-align: center;
            padding: 40px 20px;
        }
        
        .email-header h1 {
            font-size: 30px;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .email-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .email-body {
            padding: 40px 35px;
            color: #444;
        }
        
        .greeting {
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .message {
            font-size: 15px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 20px;
        }

        .code-container {
            background-color: #FFF9E5;
            border: 2px solid #FFD55A;
            border-radius: 12px;
            text-align: center;
            padding: 25px;
            margin: 35px 0;
        }
        
        .code-label {
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 13px;
            margin-bottom: 10px;
        }
        
        .verification-code {
            font-size: 42px;
            color: #B45253;
            font-weight: 700;
            letter-spacing: 10px;
            font-family: 'Segoe UI Mono', monospace;
        }
        
        .code-info {
            font-size: 13px;
            color: #666;
            margin-top: 12px;
        }

        .warning-box {
            background-color: #fff3cd;
            border-left: 5px solid #ffc107;
            padding: 15px;
            border-radius: 6px;
            margin-top: 25px;
        }
        
        .warning-box p {
            font-size: 14px;
            color: #856404;
            margin: 0;
        }

        .email-footer {
            background: #f4f6f8;
            text-align: center;
            padding: 25px 30px;
            border-top: 1px solid #eaeaea;
            font-size: 13px;
            color: #777;
        }
        
        .footer-text a {
            color: #B45253;
            text-decoration: none;
        }
        
        .footer-text a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .email-body {
                padding: 30px 25px;
            }
            .verification-code {
                font-size: 36px;
                letter-spacing: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>RESET PASSWORD</h1>
            <p>Repeat Order Management System</p>
        </div>

        <div class="email-body">
            <p class="greeting">Halo, <strong>{{ $userName }}</strong></p>
            
            <p class="message">
                Kami menerima permintaan untuk mereset password akun Anda di ROMS.
            </p>
            <p class="message">
                Gunakan kode verifikasi berikut untuk melanjutkan proses reset password:
            </p>

            <div class="code-container">
                <p class="code-label">KODE VERIFIKASI</p>
                <div class="verification-code">{{ $code }}</div>
                <p class="code-info">Kode ini berlaku selama 15 menit</p>
            </div>
            
            <p class="message">
                Masukkan kode tersebut pada halaman verifikasi untuk melanjutkan ke tahap reset password.
            </p>

            <p class="message">
                Jika Anda tidak merasa melakukan permintaan ini, abaikan email ini dan pastikan akun Anda tetap aman.
            </p>

            <div class="warning-box">
                <p>⚠️ <strong>Catatan Keamanan:</strong> Jangan berikan kode ini kepada siapa pun. Tim ROMS tidak akan pernah meminta kode verifikasi Anda.</p>
            </div>
        </div>

        <div class="email-footer">
            <p class="footer-text">
                Email ini dikirim secara otomatis oleh sistem <strong>ROMS</strong>.<br>
                © 2025 Repeat Order Management System. All rights reserved.<br>
                <a href="#">Kebijakan Privasi</a> | <a href="#">Hubungi Kami</a>
            </p>
        </div>
    </div>
</body>
</html>
