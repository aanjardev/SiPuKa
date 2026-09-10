<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi Akun SiDiKa</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .welcome-text {
            font-size: 18px;
            margin-bottom: 25px;
            color: #2c3e50;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .info-box h3 {
            margin: 0 0 10px 0;
            color: #007bff;
            font-size: 16px;
        }
        .activation-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 30px 0;
            transition: transform 0.3s ease;
        }
        .activation-button:hover {
            transform: translateY(-2px);
        }
        .expiry-warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .expiry-warning strong {
            color: #856404;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer p {
            margin: 5px 0;
            color: #6c757d;
            font-size: 14px;
        }
        .company-info {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 0;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .activation-button {
                display: block;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 Selamat Datang di SiDiKa!</h1>
            <p>Sistem Informasi Dinoyo Kamera</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="welcome-text">
                Hai <strong>{{ $user->name }}</strong>,<br>
                Akun Anda telah dibuat di SiDiKa. Untuk mulai menggunakan sistem, silakan aktivasi akun Anda terlebih dahulu.
            </p>

            <!-- Account Info -->
            <div class="info-box">
                <h3>📋 Informasi Akun</h3>
                <p><strong>Nama:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
            </div>

            <!-- Verification Instructions -->
            <div style="text-align: center; margin: 30px 0;">
                <div style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); color: white; padding: 20px; border-radius: 10px; font-size: 18px;">
                    <i class="fa-solid fa-envelope" style="font-size: 24px; margin-bottom: 10px;"></i><br>
                    <strong>Kode Verifikasi Akan Dikirim ke Email Ini</strong><br>
                    <small style="opacity: 0.9;">Check inbox Anda untuk mendapatkan kode 6 digit</small>
                </div>
            </div>

            <!-- Expiry Warning -->
            <div class="expiry-warning">
                <strong>⏰ Penting:</strong> Token aktivasi berlaku selama <strong>{{ $expiryHours }} jam</strong> 
                hingga <strong>{{ $expiryDate }}</strong>. Setelah itu, Anda perlu meminta admin untuk generate ulang token aktivasi.
            </div>

            <!-- Instructions -->
            <div style="margin: 30px 0;">
                <h3>📝 Cara Aktivasi Akun:</h3>
                <ol style="padding-left: 20px; line-height: 1.8;">
                    <li>Buka email yang Anda terima (email ini)</li>
                    <li>Klik link "Aktivasi Akun" atau kunjungi halaman aktivasi</li>
                    <li>Masukkan email Anda di form aktivasi</li>
                    <li>Check email baru untuk mendapatkan kode verifikasi 6 digit</li>
                    <li>Masukkan kode verifikasi di halaman verifikasi</li>
                    <li>Buat password baru untuk akun Anda</li>
                    <li>Login dan mulai menggunakan SiDiKa!</li>
                </ol>
            </div>

            <!-- Help -->
            <div style="background-color: #e7f3ff; border-left: 4px solid #17a2b8; padding: 20px; border-radius: 5px; margin: 25px 0;">
                <h3 style="color: #17a2b8; margin: 0 0 10px 0;">💡 Butuh Bantuan?</h3>
                <p style="margin: 0;">Jika Anda mengalami kesulitan atau link tidak berfungsi, silakan hubungi admin sistem Anda.</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Terima kasih telah bergabung dengan SiDiKa!</strong></p>
            <p>Sistem Informasi Dinoyo Kamera</p>
            
            <div class="company-info">
                <p>Email ini dikirim otomatis. Jangan balas email ini.</p>
                <p>&copy; {{ date('Y') }} SiDiKa. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
