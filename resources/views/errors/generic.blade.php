<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Umum - SiDiKa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-gray-50 to-slate-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-exclamation-circle text-gray-500 text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">Terjadi Kesalahan</h1>
                <p class="text-gray-600 mb-6">
                    Terjadi kesalahan yang tidak terduga. 
                    Kami sedang bekerja untuk memperbaikinya.
                </p>
            </div>

            <div class="space-y-3">
                <button onclick="location.reload()" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
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

            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Kode Error:</strong> {{ $exception->getCode() ?? 'Unknown' }}<br>
                    <strong>Waktu:</strong> {{ now()->format('d M Y H:i:s') }}
                </p>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-bug"></i>
                    <span>Technical team has been notified</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
