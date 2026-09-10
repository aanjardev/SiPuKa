<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - SiDiKa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-yellow-50 to-amber-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                    <i class="fas fa-tools text-yellow-600 text-3xl animate-pulse"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Sistem Dalam Perbaikan</h1>
                <p class="text-gray-600 mb-6">
                    SiDiKa sedang dalam maintenance untuk memberikan layanan yang lebih baik. 
                    Proses ini akan selesai dalam beberapa saat.
                </p>
            </div>

            <div class="space-y-3">
                <button onclick="location.reload()" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                    Periksa Kembali
                </button>
                
                <button onclick="location.href='/'" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-home"></i>
                    Kembali ke Beranda
                </button>
            </div>

            <div class="mt-6 p-4 bg-yellow-50 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Estimasi:</strong> <span id="estimate-time">5-10 menit</span><br>
                    <strong>Alasan:</strong> Update sistem dan peningkatan keamanan
                </p>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                    <span>Sistem akan segera kembali normal</span>
                </div>
            </div>
        </div>
    </div>

    <script>

        let refreshCount = 0;
        const maxRefresh = 10; // Max 10 kali refresh (5 menit)
        
        function autoRefresh() {
            if (refreshCount >= maxRefresh) {

                document.getElementById('estimate-time').textContent = '15-20 menit';
                return;
            }
            
            const countdown = document.createElement('div');
            countdown.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-3 text-sm';
            countdown.innerHTML = '<i class="fas fa-sync-alt animate-spin mr-2"></i>Auto check dalam <span id="countdown">30</span> detik...';
            document.body.appendChild(countdown);

            let seconds = 30;
            const timer = setInterval(() => {
                seconds--;
                document.getElementById('countdown').textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(timer);
                    countdown.remove();
                    refreshCount++;
                    location.reload();
                }
            }, 1000);

            document.addEventListener('click', () => {
                clearInterval(timer);
                countdown.remove();
            });
        }

        setTimeout(autoRefresh, 2000);
    </script>
</body>
</html>
