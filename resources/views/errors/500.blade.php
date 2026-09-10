<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - SiDiKa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-red-50 to-pink-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-triangle text-red-500 text-3xl"></i>
                </div>
                <h1 class="text-6xl font-bold text-red-600 mb-2">500</h1>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Server Error</h2>
                <p class="text-gray-600 mb-6">
                    Terjadi kesalahan pada server internal. 
                    Tim kami sudah diberitahu dan sedang memperbaikinya.
                </p>
            </div>

            <div class="space-y-3">
                <button onclick="location.reload()" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-refresh"></i>
                    Coba Lagi
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

            <div class="mt-6 p-4 bg-red-50 rounded-lg">
                <p class="text-sm text-red-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Status:</strong> Error sudah dilaporkan ke tim teknis. 
                    Estimasi perbaikan: 5-10 menit.
                </p>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                    <span>Tim sedang bekerja memperbaiki masalah</span>
                </div>
            </div>
        </div>
    </div>

    <script>

        setTimeout(() => {
            const countdown = document.createElement('div');
            countdown.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-3 text-sm';
            countdown.innerHTML = '<i class="fas fa-sync-alt animate-spin mr-2"></i>Auto refresh dalam <span id="countdown">15</span> detik...';
            document.body.appendChild(countdown);

            let seconds = 15;
            const timer = setInterval(() => {
                seconds--;
                document.getElementById('countdown').textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(timer);
                    location.reload();
                }
            }, 1000);

            document.addEventListener('click', () => {
                clearInterval(timer);
                countdown.remove();
            });
        }, 1000);
    </script>
</body>
</html>
