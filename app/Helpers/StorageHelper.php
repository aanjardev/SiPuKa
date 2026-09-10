<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StorageHelper
{
    /**
     * Check apakah R2 storage tersedia & accessible
     * @return bool
     */
    public static function isR2Available(): bool
    {
        try {
            // Coba check bucket existence
            $bucket = config('filesystems.disks.r2.bucket');
            if (!$bucket) {
                return false;
            }

            // Simple test: try to list files (jika bisa, berarti credentials valid)
            Storage::disk('r2')->listContents('/');
            return true;
        } catch (\Throwable $e) {
            Log::warning("R2 storage not available: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get preferred storage disk
     * Returns 'r2' jika available, otherwise 'public'
     *
     * @return string
     */
    public static function getPreferredDisk(): string
    {
        if (self::isR2Available()) {
            return 'r2';
        }

        Log::info("Using local storage instead of R2");
        return 'public';
    }

    /**
     * Check storage health
     * @return array {
     *   'r2_available' => bool,
     *   'local_available' => bool,
     *   'preferred_disk' => string
     * }
     */
    public static function getStorageHealth(): array
    {
        return [
            'r2_available' => self::isR2Available(),
            'local_available' => Storage::disk('public')->exists('/'),
            'preferred_disk' => self::getPreferredDisk(),
        ];
    }
}
