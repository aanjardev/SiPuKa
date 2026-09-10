<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Str;

class SimpleImageMover
{
    /**
     * Move a temporary file into storage without re-encoding.
     * Returns array with 'path' key (public path or r2 path).
     */
    public static function move(string $tempPath, string $prefix): array
    {
        if (!file_exists($tempPath)) {
            throw new \Exception("Temporary file does not exist: {$tempPath}");
        }

        $ext = pathinfo($tempPath, PATHINFO_EXTENSION) ?: 'jpg';
        $name = Str::random(40) . '.' . $ext;
        $dir = rtrim($prefix, '/');
        $path = "{$dir}/{$name}";

        // Try R2 first
        try {
            Storage::disk('r2')->putFileAs($dir, new File($tempPath), $name, [
                'visibility' => 'public',
                'ContentType' => mime_content_type($tempPath) ?: 'application/octet-stream',
            ]);

            return ['path' => $path, 'original_hash' => null];
        } catch (\Throwable $e) {
            // fallback to public disk
            try {
                $public = Storage::disk('public');
                if (!$public->exists($dir)) {
                    $public->makeDirectory($dir, 0755, true);
                }

                $public->putFileAs($dir, new File($tempPath), $name, [
                    'visibility' => 'public',
                ]);

                return ['path' => "public/{$path}", 'original_hash' => null];
            } catch (\Throwable $e2) {
                throw new \Exception('Failed to move file to storage: ' . $e2->getMessage());
            }
        }
    }
}
