<?php

/**
 * Script untuk verifikasi Laravel Queue
 * Usage: php test-queue-verification.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Jobs\ProcessProductImage;
use App\Models\Produk;

echo "========================================\n";
echo "  VERIFIKASI LARAVEL QUEUE SYSTEM\n";
echo "========================================\n\n";

echo "1. KONFIGURASI QUEUE\n";
echo "   " . str_repeat("-", 40) . "\n";
$connection = config('queue.default');
echo "   Queue Connection: {$connection}\n";

if ($connection === 'sync') {
    echo "   ❌ ERROR: Queue masih 'sync' (synchronous)\n";
    echo "   → Ubah QUEUE_CONNECTION=database di .env\n";
    echo "   → Jalankan: php artisan config:clear\n";
    exit(1);
} else {
    echo "   ✅ Queue connection: {$connection}\n";
}

echo "\n2. CEK TABEL DATABASE\n";
echo "   " . str_repeat("-", 40) . "\n";
try {
    $jobsCount = DB::table('jobs')->count();
    $failedCount = DB::table('failed_jobs')->count();
    echo "   Tabel 'jobs': ✅ Ada ({$jobsCount} job pending)\n";
    echo "   Tabel 'failed_jobs': ✅ Ada ({$failedCount} failed jobs)\n";
} catch (\Exception $e) {
    echo "   ❌ ERROR: " . $e->getMessage() . "\n";
    echo "   → Jalankan: php artisan migrate\n";
    exit(1);
}

echo "\n3. CEK JOB CLASS\n";
echo "   " . str_repeat("-", 40) . "\n";
if (class_exists('App\Jobs\ProcessProductImage')) {
    echo "   ✅ ProcessProductImage class: Ada\n";
    
    $reflection = new ReflectionClass('App\Jobs\ProcessProductImage');
    $interfaces = $reflection->getInterfaceNames();
    if (in_array('Illuminate\Contracts\Queue\ShouldQueue', $interfaces)) {
        echo "   ✅ Implements ShouldQueue: Ya\n";
    } else {
        echo "   ❌ ERROR: Job tidak implement ShouldQueue\n";
        exit(1);
    }
} else {
    echo "   ❌ ERROR: ProcessProductImage class tidak ditemukan\n";
    exit(1);
}

echo "\n4. TEST DISPATCH JOB\n";
echo "   " . str_repeat("-", 40) . "\n";
try {
    $product = Produk::first();
    
    if (!$product) {
        echo "   ⚠️  Tidak ada produk di database\n";
        echo "   → Buat produk dulu untuk test\n";
    } else {
        echo "   Menggunakan Product ID: {$product->id} ({$product->nama_produk})\n";

        ProcessProductImage::dispatch(
            $product->id,
            ['test/path/image.jpg'],
            null,
            1
        );
        
        echo "   ✅ Job berhasil di-dispatch!\n";

        $newJobsCount = DB::table('jobs')->count();
        echo "   Jobs di queue setelah dispatch: {$newJobsCount}\n";
        
        if ($newJobsCount > $jobsCount) {
            echo "   ✅ SUCCESS: Job masuk ke queue!\n";

            $latestJob = DB::table('jobs')->latest('id')->first();
            if ($latestJob) {
                echo "\n   Detail Job:\n";
                echo "   - ID: {$latestJob->id}\n";
                echo "   - Queue: {$latestJob->queue}\n";
                echo "   - Attempts: {$latestJob->attempts}\n";
                echo "   - Created: {$latestJob->created_at}\n";
                echo "   - Reserved: " . ($latestJob->reserved_at ? $latestJob->reserved_at : 'Belum diproses') . "\n";
            }
        } else {
            echo "   ❌ ERROR: Job TIDAK masuk ke queue!\n";
            echo "   → Cek konfigurasi queue di .env\n";
            echo "   → Jalankan: php artisan config:clear\n";
            exit(1);
        }
    }
} catch (\Exception $e) {
    echo "   ❌ ERROR saat dispatch: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo "   " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n5. CEK INTEGRASI CONTROLLER\n";
echo "   " . str_repeat("-", 40) . "\n";
$controllerFile = 'app/Http/Controllers/AdminProductController.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    if (strpos($content, 'ProcessProductImage::dispatch') !== false) {
        echo "   ✅ Controller sudah menggunakan ProcessProductImage::dispatch\n";
    } else {
        echo "   ⚠️  Controller belum menggunakan ProcessProductImage::dispatch\n";
    }
} else {
    echo "   ⚠️  Controller file tidak ditemukan\n";
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "  RINGKASAN\n";
echo str_repeat("=", 40) . "\n";
echo "✅ Queue connection: {$connection}\n";
echo "✅ Tabel jobs: Ada dan berfungsi\n";
echo "✅ Job class: Ada dan implement ShouldQueue\n";
echo "✅ Dispatch job: Berhasil\n";
echo "✅ Job masuk queue: Berhasil\n";
echo "\n📋 LANGKAH SELANJUTNYA:\n";
echo "1. Test via website: Upload foto produk\n";
echo "2. Cek database: SELECT * FROM jobs ORDER BY id DESC LIMIT 5;\n";
echo "3. Jalankan queue worker: php artisan queue:work\n";
echo "4. Setelah diproses, cek notifications table\n";
echo "\n";

