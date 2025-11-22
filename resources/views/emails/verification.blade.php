<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - ROMS</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .email-header {
            background: linear-gradient(135deg, #84994F 0%, #6B7D3F 100%);
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        
        .email-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 700;
        }
        
        .email-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .email-body {
            padding: 40px 30px;
        }
        
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        
        .message {
            font-size: 15px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .code-container {
            background-color: #FFE797;
            border: 2px dashed #FCB53B;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            margin: 30px 0;
        }
        
        .code-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .verification-code {
            font-size: 42px;
            font-weight: 700;
            color: #B45253;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        
        .code-info {
            font-size: 13px;
            color: #666;
            margin-top: 15px;
        }
        
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 25px 0;
            border-radius: 4px;
        }
        
        .warning-box p {
            font-size: 14px;
            color: #856404;
            margin: 0;
        }
        
        .email-footer {
            background-color: #f8f9fa;
            padding: 25px 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .footer-text {
            font-size: 13px;
            color: #6c757d;
            line-height: 1.6;
        }
        
        .footer-link {
            color: #B45253;
            text-decoration: none;
        }
        
        .footer-link:hover {
            text-decoration: underline;
        }
        
        .social-links {
            margin-top: 15px;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 5px;
            color: #84994F;
            text-decoration: none;
            font-size: 12px;
        }
        
        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 30px 20px;
            }
            
            .verification-code {
                font-size: 36px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <h1>🔐 ROMS</h1>
            <p>Repeat Order Management System</p>
        </div>
        
        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                Halo, <strong>{{ $userName }}</strong>! 👋
            </div>
            
            <div class="message">
                <p>Terima kasih telah mendaftar di <strong>ROMS (Repeat Order Management System)</strong>!</p>
                <p style="margin-top: 15px;">Untuk menyelesaikan proses registrasi Anda, silakan gunakan kode verifikasi berikut:</p>
            </div>
            
            <!-- Verification Code Box -->
            <div class="code-container">
                <div class="code-label">Kode Verifikasi Anda</div>
                <div class="verification-code">{{ $code }}</div>
                <div class="code-info">
                    ⏱️ Kode ini berlaku selama <strong>15 menit</strong>
                </div>
            </div>
            
            <div class="message">
                <p>Masukkan kode di atas pada halaman verifikasi untuk mengaktifkan akun Anda.</p>
            </div>
            
            <!-- Warning Box -->
            <div class="warning-box">
                <p>
                    <strong>⚠️ Penting:</strong> Jika Anda tidak melakukan pendaftaran di ROMS, 
                    abaikan email ini. Kode verifikasi ini bersifat rahasia dan hanya untuk Anda.
                </p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">
                Email ini dikirim secara otomatis oleh sistem ROMS.<br>
                Jika Anda memiliki pertanyaan, hubungi kami di 
                <a href="mailto:wyandhanupapoy@gmail.com" class="footer-link">wyandhanupapoy@gmail.com</a>
            </p>
            
            <div class="social-links">
                <p style="font-size: 12px; color: #6c757d; margin-top: 10px;">
                    © {{ date('Y') }} ROMS SOMEAH. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>
</html>