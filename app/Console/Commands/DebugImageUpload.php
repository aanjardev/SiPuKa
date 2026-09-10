<?php

namespace App\Console\Commands;

use App\Helpers\StorageHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DebugImageUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:image-upload {product_id?}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Debug image upload issues - check storage status and product images';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Image Upload Debugger');
        $this->line('─────────────────────────────────────────');

        // 1. Storage Health
        $this->checkStorageHealth();

        // 2. File System Status
        $this->checkFileSystem();

        // 3. Database Status
        $this->checkDatabase();

        // 4. Product specific check (if provided)
        $productId = $this->argument('product_id');
        if ($productId) {
            $this->checkProduct($productId);
        }

        $this->line('─────────────────────────────────────────');
        $this->info('✅ Debug complete');
    }

    private function checkStorageHealth(): void
    {
        $this->line('');
        $this->info('📦 Storage Health Check');
        $this->line('');

        $health = StorageHelper::getStorageHealth();

        $this->table(
            ['Storage', 'Status'],
            [
                ['R2 (Cloudflare)', $health['r2_available'] ? '✅ Available' : '❌ Not Available'],
                ['Local (Public)', $health['local_available'] ? '✅ Available' : '❌ Not Available'],
                ['Preferred Disk', $health['preferred_disk']],
            ]
        );
    }

    private function checkFileSystem(): void
    {
        $this->line('');
        $this->info('📂 File System Status');
        $this->line('');

        $paths = [
            'storage/app/public/product' => 'Product images',
            'storage/app/temp/product-uploads' => 'Temp uploads',
            'storage/app/temp/product-updates' => 'Temp updates',
            'storage/app/temp/product-creates' => 'Temp creates',
            'storage/logs' => 'Logs directory',
        ];

        foreach ($paths as $path => $label) {
            $fullPath = base_path($path);
            $exists = is_dir($fullPath);
            $readable = is_readable($fullPath);
            $writable = is_writable($fullPath);

            $status = '';
            if (!$exists) $status = '❌ Not exists';
            elseif (!$readable) $status = '⚠️  Not readable';
            elseif (!$writable) $status = '⚠️  Not writable';
            else $status = '✅ OK';

            $this->line("$label: $status");
        }
    }

    private function checkDatabase(): void
    {
        $this->line('');
        $this->info('🗄️  Database Status');
        $this->line('');

        $totalImages = DB::table('gambar_produk')->count();
        $imagesWithoutPath = DB::table('gambar_produk')
            ->whereNull('path_gambar')
            ->orWhere('path_gambar', '')
            ->count();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total images in DB', $totalImages],
                ['Images without path', $imagesWithoutPath],
            ]
        );

        if ($imagesWithoutPath > 0) {
            $this->warn("⚠️  Found {$imagesWithoutPath} images without valid path!");
            $this->warn("These images should be deleted.");
        }
    }

    private function checkProduct($productId): void
    {
        $this->line('');
        $this->info("📷 Product #{$productId} Details");
        $this->line('');

        $product = DB::table('produk')->where('id_produk', $productId)->first();

        if (!$product) {
            $this->error("Product not found: {$productId}");
            return;
        }

        $this->line("Product: {$product->nama_produk}");

        $images = DB::table('gambar_produk')
            ->where('id_produk', $productId)
            ->get();

        $this->table(
            ['ID', 'Path', 'Main', 'Created'],
            $images->map(fn ($img) => [
                $img->id,
                substr($img->path_gambar, 0, 40) . (strlen($img->path_gambar) > 40 ? '...' : ''),
                $img->is_main ? '⭐ Yes' : 'No',
                \Carbon\Carbon::parse($img->created_at)->format('Y-m-d H:i'),
            ])->toArray()
        );

        if (count($images) === 0) {
            $this->warn("⚠️  No images found for this product!");
        }
    }
}
