<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;

/**
 * Optimized Image Upload Helper
 * - Handles memory limits
 * - Auto-resizes large images
 * - Fallback to local storage
 */
class ImageUploadOptimized
{
    private static int $MAX_DIMENSION = 2000;  // Reduced from 5000 untuk memory safety
    private static int $WEBP_QUALITY = 80;     // Reduced from 92 untuk file size
    private static int $MAX_FILE_SIZE = 10 * 1024 * 1024; // 10MB
    private static int $MEMORY_LIMIT = 256; // MB

    /**
     * Upload single optimized image (WebP)
     *
     * @param mixed $file
     * @param string $prefix
     * @return array {
     *   path, original_hash
     * }
     */
    public static function upload($file, string $prefix = 'uploads'): array
    {
        // CRITICAL: Memory management
        $originalMemoryLimit = ini_get('memory_limit');
        $originalTimeout = ini_get('max_execution_time');

        ini_set('memory_limit', self::$MEMORY_LIMIT . 'M');
        set_time_limit(300); // 5 minutes untuk processing

        try {
            return self::processImage($file, $prefix);
        } catch (\Throwable $e) {
            \Log::error("Image upload failed", [
                'file' => $file,
                'error' => $e->getMessage(),
                'memory' => ini_get('memory_limit'),
            ]);
            throw $e;
        } finally {
            // Restore original settings
            ini_set('memory_limit', $originalMemoryLimit);
            set_time_limit($originalTimeout);
            // Force garbage collection
            gc_collect_cycles();
        }
    }

    /**
     * Process image dengan error handling yang lebih baik
     */
    private static function processImage($file, string $prefix): array
    {
        // 1. Validate file exists
        if (!file_exists($file)) {
            throw new \Exception("File tidak ditemukan: $file");
        }

        // 2. Check file size dulu sebelum proses
        $fileSize = filesize($file);
        if ($fileSize > self::$MAX_FILE_SIZE) {
            $sizeMB = round($fileSize / 1024 / 1024, 1);
            throw new \Exception("File terlalu besar ({$sizeMB}MB). Maksimal 10MB.");
        }

        // 3. Validate image
        $info = @getimagesize($file);
        if (!$info) {
            throw new \Exception("File bukan gambar valid atau corrupt.");
        }

        $w = $info[0];
        $h = $info[1];

        if ($w > self::$MAX_DIMENSION || $h > self::$MAX_DIMENSION) {
            throw new \Exception("Resolusi terlalu besar ({$w}x{$h}). Maksimal " . self::$MAX_DIMENSION . "px.");
        }

        // 4. Process image
        try {
            \Log::info("Processing image", [
                'file' => $file,
                'size' => round($fileSize / 1024, 1) . 'KB',
                'dimensions' => "{$w}x{$h}",
                'memory_limit' => ini_get('memory_limit'),
            ]);

            // Load image
            $img = Image::make($file)->orientate();

            // Resize jika terlalu besar (lebih hemat memory)
            if ($w > 1920 || $h > 1920) {
                \Log::info("Auto-resizing large image", [
                    'from' => "{$w}x{$h}",
                    'to' => "1920x1920"
                ]);

                $img->resize(1920, 1920, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Encode ke WebP
            $encoded = Image::make($img)->encode("webp", self::$WEBP_QUALITY);
            $hash = sha1($encoded);

            $baseName = $hash . '.webp';
            $path = "$prefix/$baseName";

            \Log::info("Image encoded successfully", [
                'path' => $path,
                'hash' => $hash,
            ]);

            // 5. Try upload (R2 first, fallback to local)
            return self::uploadImage($encoded, $path);

        } catch (\Throwable $e) {
            \Log::error("Image processing error", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Upload image dengan fallback logic
     */
    private static function uploadImage($buffer, string $path): array
    {
        // Check if already exists
        if (Storage::disk('r2')->exists($path)) {
            \Log::info("Image already exists in R2", ['path' => $path]);
            return [
                'path' => $path,
                'original_hash' => null,
            ];
        }

        // Try R2 first
        try {
            self::uploadToR2($buffer, $path);
            \Log::info("Image uploaded to R2 successfully", ['path' => $path]);
            return [
                'path' => $path,
                'original_hash' => null,
            ];
        } catch (\Throwable $r2Error) {
            // Fallback to local storage
            \Log::warning("R2 upload failed, falling back to local storage", [
                'error' => $r2Error->getMessage(),
                'path' => $path
            ]);

            return self::uploadToLocal($buffer, $path);
        }
    }

    /**
     * Upload ke Cloudflare R2
     */
    private static function uploadToR2($buffer, string $path): void
    {
        $temp = sys_get_temp_dir() . '/' . Str::uuid() . '.webp';

        try {
            $buffer->save($temp);

            Storage::disk("r2")->putFileAs(
                dirname($path),
                new File($temp),
                basename($path),
                [
                    'visibility' => 'public',
                    'ContentType' => 'image/webp',
                    'CacheControl' => 'public, max-age=31536000, immutable'
                ]
            );
        } finally {
            @unlink($temp);
        }
    }

    /**
     * Upload ke local storage sebagai fallback
     */
    private static function uploadToLocal($buffer, string $path): array
    {
        $temp = sys_get_temp_dir() . '/' . Str::uuid() . '.webp';

        try {
            $buffer->save($temp);

            $localDisk = Storage::disk('public');

            // Ensure directory exists
            $dir = dirname($path);
            if (!$localDisk->exists($dir)) {
                $localDisk->makeDirectory($dir, 0755, true);
            }

            // Upload to public storage
            $localDisk->putFileAs(
                $dir,
                new File($temp),
                basename($path),
                [
                    'visibility' => 'public',
                ]
            );

            \Log::info("Image uploaded to local storage", [
                'path' => "public/$path"
            ]);

            // Return public accessible path
            return [
                'path' => "public/$path",
                'original_hash' => null,
            ];
        } finally {
            @unlink($temp);
        }
    }
}
