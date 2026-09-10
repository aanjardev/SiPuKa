<?php

namespace App\Jobs;

use App\Models\Produk;
use App\Models\GambarProduk;
use App\Models\User;
use App\Notifications\JobCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes for image processing

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $productId,
        public array $temporaryFilePaths,
        public ?int $mainImageIndex = null,
        public ?int $userId = null,
        public bool $autoEnableVisibility = false
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();
        try {
            $product = Produk::findOrFail($this->productId);
            Log::info('ProcessProductImage starting', [
                'product_id' => $this->productId,
                'autoEnableVisibility' => $this->autoEnableVisibility,
                'temporary_files_count' => count($this->temporaryFilePaths)
            ]);
            $hasMain = $product->gambarUtama()->exists();
            $createdIds = [];

            foreach ($this->temporaryFilePaths as $index => $tempPath) {

                if (!Storage::disk('local')->exists($tempPath)) {
                    Log::warning("Temporary file not found: {$tempPath}");
                    continue;
                }

                $tempFilePath = Storage::disk('local')->path($tempPath);

                try {

                    // ImageUpload will automatically increase memory limit and use Imagick if available
                    $prefix = 'product/' . $product->id;

                    // Try a lightweight move first (no re-encode) to avoid heavy memory usage.
                    try {
                        $paths = \App\Helpers\SimpleImageMover::move(
                            $tempFilePath,
                            $prefix
                        );
                        Log::info("Simple mover used for image", ['file' => $tempPath, 'product_id' => $product->id, 'path' => $paths['path']]);
                    } catch (\Throwable $moverError) {
                        // If simple mover fails, fall back to optimized (may use more memory)
                        Log::warning("Simple mover failed, attempting optimized upload", [
                            'file' => $tempPath,
                            'error' => $moverError->getMessage(),
                        ]);

                        try {
                            $paths = \App\Helpers\ImageUploadOptimized::upload(
                                $tempFilePath,
                                $prefix
                            );
                            Log::info("Optimized upload used for image", ['file' => $tempPath, 'product_id' => $product->id, 'path' => $paths['path']]);
                        } catch (\Throwable $imageError) {
                            // Log detailed error untuk debugging, but continue to next image
                            Log::error("ImageUpload failed: " . $imageError->getMessage(), [
                                'file' => $tempPath,
                                'product_id' => $product->id,
                                'error' => $imageError->getMessage(),
                            ]);
                            // try delete temp and continue
                            try {
                                if (Storage::disk('local')->exists($tempPath)) {
                                    Storage::disk('local')->delete($tempPath);
                                }
                            } catch (\Throwable $deleteError) {
                                Log::warning("Failed to delete temporary file after image error: {$tempPath}", ['error' => $deleteError->getMessage()]);
                            }

                            continue;
                        }
                    }

                    $permanentPath = $paths['path'];

                    Storage::disk('local')->delete($tempPath);

                    $gambar = GambarProduk::create([
                        'id_produk' => $product->id,
                        'path_gambar' => $permanentPath,
                        'is_main' => false,
                    ]);

                    $createdIds[] = [
                        'id' => $gambar->id,
                        'index' => $index
                    ];
                } catch (\Throwable $e) {
                    Log::error("Failed to process image: {$tempPath}", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    try {
                        if (Storage::disk('local')->exists($tempPath)) {
                            Storage::disk('local')->delete($tempPath);
                        }
                    } catch (\Throwable $deleteError) {
                        Log::warning("Failed to delete temporary file: {$tempPath}", [
                            'error' => $deleteError->getMessage()
                        ]);
                    }


                    continue;
                }
            }

            if ($this->mainImageIndex !== null) {
                $mainRecord = null;
                foreach ($createdIds as $created) {
                    if (($created['index'] ?? null) === $this->mainImageIndex) {
                        $mainRecord = $created;
                        break;
                    }
                }

                if ($mainRecord) {
                    GambarProduk::where('id_produk', $product->id)->update(['is_main' => false]);
                    GambarProduk::where('id', $mainRecord['id'])->update(['is_main' => true]);
                }
            } elseif (!$hasMain && !empty($createdIds)) {

                GambarProduk::where('id', $createdIds[0]['id'])->update(['is_main' => true]);
            }

            DB::commit();

            // PERBAIKAN: Auto-enable visibility setelah berhasil upload gambar
            if ($this->autoEnableVisibility) {
                $product->update(['is_visible' => true]);
                Log::info("Auto-enabled product visibility after image upload", [
                    'product_id' => $this->productId,
                    'product_name' => $product->nama_produk
                ]);
            }

            if ($this->userId) {
                $user = User::find($this->userId);
                if ($user) {
                    $user->notify(new JobCompleted(
                        'Upload Foto Produk',
                        "Foto produk '{$product->nama_produk}' berhasil diunggah dan diproses.",
                        'success'
                    ));
                }
            }

            Log::info("Product images processed successfully for product ID: {$this->productId}");

        } catch (\Throwable $th) {
            DB::rollBack();

            $this->cleanupTemporaryFiles();

            Log::error("Failed to process product images: " . $th->getMessage(), [
                'product_id' => $this->productId,
                'trace' => $th->getTraceAsString()
            ]);

            throw $th;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {

        $this->cleanupTemporaryFiles();

        if ($this->userId) {
            try {
                $user = User::find($this->userId);
                if ($user) {
                    $user->notify(new JobCompleted(
                        'Upload Foto Produk Gagal',
                        "Gagal mengunggah foto produk. Silakan coba lagi atau hubungi administrator.",
                        'error'
                    ));
                }
            } catch (\Throwable $e) {
                Log::error("Failed to send failure notification: " . $e->getMessage());
            }
        }

        Log::error("Job ProcessProductImage failed permanently", [
            'product_id' => $this->productId,
            'error' => $exception->getMessage()
        ]);
    }

    /**
     * Clean up temporary files.
     */
    private function cleanupTemporaryFiles(): void
    {
        foreach ($this->temporaryFilePaths as $tempPath) {
            try {
                if (Storage::disk('local')->exists($tempPath)) {
                    Storage::disk('local')->delete($tempPath);
                }
            } catch (\Throwable $e) {
                Log::warning("Failed to delete temporary file: {$tempPath}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Convert memory limit string to bytes
     *
     * @param string $value Memory limit string (e.g., "128M", "512M", "1G")
     * @return int Memory limit in bytes
     */
    private static function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g':
                $value *= 1024;

            case 'm':
                $value *= 1024;

            case 'k':
                $value *= 1024;
        }

        return $value;
    }
}
