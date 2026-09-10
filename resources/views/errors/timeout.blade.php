<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Sibuk - SiDiKa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-100 rounded-full mb-4">
                    <i class="fas fa-clock text-orange-500 text-3xl animate-pulse"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Server Sedang Sibuk</h1>
                <p class="text-gray-600 mb-6">
                    Permintaan Anda membutuhkan waktu lebih lama dari biasanya. 
                    Hal ini bisa terjadi karena traffic sedang tinggi atau proses yang kompleks.
                </p>
            </div>

            <div class="space-y-3">
                <button onclick="location.reload()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-refresh"></i>
                    Muat Ulang Halaman
                </button>
                
                <button onclick="history.back()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Halaman Sebelumnya
                </button>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Tips:</strong> Tunggu beberapa saat lalu coba lagi. 
                    Jika masalah berlanjut, hubungi admin sistem.
                </p>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <span>Sistem masih berjalan normal</span>
                </div>
            </div>
        </div>
    </div>

    <script>

        setTimeout(() => {
            const countdown = document.createElement('div');
            countdown.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-3 text-sm';
            countdown.innerHTML = '<i class="fas fa-sync-alt animate-spin mr-2"></i>Auto refresh dalam <span id="countdown">10</span> detik...';
            document.body.appendChild(countdown);

            let seconds = 10;
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
