<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - SiDiKa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-orange-50 to-red-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-100 rounded-full mb-4">
                    <i class="fas fa-lock text-orange-500 text-3xl"></i>
                </div>
                <h1 class="text-6xl font-bold text-orange-600 mb-2">403</h1>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Akses Ditolak</h2>
                <p class="text-gray-600 mb-6">
                    Anda tidak memiliki izin untuk mengakses halaman ini. 
                    Sesi Anda mungkin sudah kadaluarsa atau Anda perlu login terlebih dahulu.
                </p>
            </div>

            <div class="space-y-3">
                <button onclick="location.href='/login'" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Login Kembali
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

            <div class="mt-6 p-4 bg-orange-50 rounded-lg">
                <p class="text-sm text-orange-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Solusi:</strong> Login kembali dengan akun Anda. 
                    Jika masalah berlanjut, hubungi administrator sistem.
                </p>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></div>
                    <span>Sesi mungkin sudah kadaluarsa</span>
                </div>
            </div>
        </div>
    </div>

    <script>

        setTimeout(() => {
            const countdown = document.createElement('div');
            countdown.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-3 text-sm';
            countdown.innerHTML = '<i class="fas fa-clock animate-pulse mr-2"></i>Redirect ke login dalam <span id="countdown">10</span> detik...';
            document.body.appendChild(countdown);

            let seconds = 10;
            const timer = setInterval(() => {
                seconds--;
                document.getElementById('countdown').textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(timer);
                    window.location.href = '/login';
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
