<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Reset Password SiDiKa</title>
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
            background: linear-gradient(135deg, #d17272ff 0%, #ee5a24 100%);
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
        .reset-code {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 30px 0;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }
        .instructions {
            background-color: #f8f9fa;
            border-left: 4px solid #ff6b6b;
            padding: 20px;
            margin: 25px 0;
            border-radius: 5px;
        }
        .instructions h3 {
            margin: 0 0 15px 0;
            color: #ff6b6b;
            font-size: 16px;
        }
        .instructions ol {
            margin: 0;
            padding-left: 20px;
        }
        .instructions li {
            margin-bottom: 10px;
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
        .security-note {
            background-color: #e7f3ff;
            border-left: 4px solid #17a2b8;
            padding: 20px;
            border-radius: 5px;
            margin: 25px 0;
        }
        .security-note h3 {
            color: #17a2b8;
            margin: 0 0 10px 0;
        }
        .user-info {
            background-color: #e8f5e8;
            border-left: 4px solid #28a745;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .user-info p {
            margin: 5px 0;
            color: #155724;
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 10px;
                border-radius: 0;
            }
            .header, .content, .footer {
                padding: 20px;
            }
            .reset-code {
                font-size: 28px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🔐 Reset Password SiDiKa</h1>
            <p>Sistem Informasi Dinoyo Kamera</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p style="font-size: 18px; margin-bottom: 25px;">
                Hai <strong>{{ $user->name }}</strong>,<br>
                Kami menerima permintaan reset password untuk akun SiDiKa Anda.
            </p>

            <!-- User Info -->
            <div class="user-info">
                <p><strong>📋 Informasi Akun:</strong></p>
                <p><strong>Nama:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
            </div>

            <!-- Reset Code Display -->
            <div class="reset-code">
                {{ $verificationCode }}
            </div>

            <!-- Instructions -->
            <div class="instructions">
                <h3>📋 Cara Reset Password:</h3>
                <ol>
                    <li>Masukkan kode di halaman verifikasi reset password</li>
                    <li>Kode ini berlaku selama <strong>30 menit</strong></li>
                    <li>Setelah verifikasi, Anda bisa membuat password baru</li>
                    <li>Password baru akan langsung aktif</li>
                </ol>
            </div>

            <!-- Security Note -->
            <div class="security-note">
                <h3>🔒 Keamanan Akun</h3>
                <p style="margin: 0;">
                    <strong>Jangan bagikan kode ini</strong> kepada siapa pun. Kode reset password bersifat rahasia 
                    dan hanya untuk Anda gunakan dalam proses reset password.
                </p>
            </div>

            <!-- Expiry Warning -->
            <div class="expiry-warning">
                <strong>⏰ Penting:</strong> Kode reset password ini berlaku hingga 
                <strong>{{ $expiryDate }}</strong>. Jika kode kadaluarsa, 
                silakan minta kode baru melalui halaman profil.
            </div>

            <!-- Help -->
            <div style="margin: 30px 0;">
                <h3>💡 Tidak Meminta Reset Password?</h3>
                <p style="margin: 10px 0;">
                    Jika Anda tidak meminta reset password ini, segera hubungi admin sistem Anda. 
                    Password Anda masih aman dan tidak berubah.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Terima kasih menggunakan SiDiKa!</strong></p>
            <p>Sistem Informasi Dinoyo Kamera</p>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                <p>Email ini dikirim otomatis. Jangan balas email ini.</p>
                <p>&copy; {{ date('Y') }} SiDiKa. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
