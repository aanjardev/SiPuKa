<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terlalu Banyak Permintaan - SiDiKa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-purple-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-100 rounded-full mb-4">
                    <i class="fas fa-hourglass-half text-purple-500 text-3xl animate-pulse"></i>
                </div>
                <h1 class="text-6xl font-bold text-purple-600 mb-2">429</h1>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Terlalu Banyak Permintaan</h2>
                <p class="text-gray-600 mb-6">
                    Anda terlalu sering mengakses halaman ini dalam waktu singkat. 
                    Tunggu beberapa saat sebelum mencoba kembali.
                </p>
            </div>

            <div class="space-y-3">
                <button onclick="location.reload()" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2" disabled id="reloadBtn">
                    <i class="fas fa-refresh"></i>
                    <span id="btnText">Tunggu <span id="countdown">30</span> detik</span>
                </button>
                
                <button onclick="location.href='/'" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-home"></i>
                    Kembali ke Beranda
                </button>
                
                <button onclick="history.back()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Halaman Sebelumnya
                </button>
            </div>

            <div class="mt-6 p-4 bg-purple-50 rounded-lg">
                <p class="text-sm text-purple-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Rate Limit:</strong> Untuk keamanan sistem, kami membatasi jumlah permintaan. 
                    Silakan tunggu sebentar.
                </p>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                    <span>Perlindungan sistem sedang aktif</span>
                </div>
            </div>
        </div>
    </div>

    <script>

        let seconds = 30;
        const reloadBtn = document.getElementById('reloadBtn');
        const countdownSpan = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            seconds--;
            countdownSpan.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
                reloadBtn.disabled = false;
                document.getElementById('btnText').innerHTML = '<i class="fas fa-refresh mr-2"></i>Coba Lagi Sekarang';
            }
        }, 1000);
    </script>
</body>
</html>
