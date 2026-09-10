<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - SiDiKa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4">
                    <i class="fas fa-search text-blue-500 text-3xl"></i>
                </div>
                <h1 class="text-6xl font-bold text-blue-600 mb-2">404</h1>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Halaman Tidak Ditemukan</h2>
                <p class="text-gray-600 mb-6">
                    Halaman yang Anda cari tidak ada atau telah dipindahkan. 
                    Mungkin ada kesalahan dalam URL atau halaman sudah tidak tersedia.
                </p>
            </div>

            <div class="space-y-3">
                <button onclick="location.href='/'" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-home"></i>
                    Kembali ke Beranda
                </button>
                
                <button onclick="history.back()" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Halaman Sebelumnya
                </button>
            </div>

            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Tips:</strong> Periksa kembali URL yang Anda ketik atau 
                    gunakan menu navigasi untuk menemukan halaman yang Anda cari.
                </p>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-link"></i>
                    <span>Link mungkin sudah kadaluarsa atau salah</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
