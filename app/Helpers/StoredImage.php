<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StoredImage
{
    public static function url(?string $path, int $width = 0): ?string
    {
        if (! $path) {
            return null;
        }
        $base = rtrim((string) config('filesystems.disks.r2.url', ''), '/');
        if (! $width && ! config('filesystems.disks.r2.serve_via_app', true) && ! str_starts_with($path, 'public/') && $base) {
            return $base.'/'.ltrim($path, '/');
        }

        // Local files and private R2 buckets are served without a storage symlink.
        return url(\Illuminate\Support\Facades\URL::signedRoute('stored-image', array_filter(['path' => $path, 'width' => $width]), null, false));
    }

    public static function delete(?string $path): void
    {
        if (! $path) {
            return;
        }
        Storage::disk('local')->deleteDirectory('image-cache/'.hash('sha256', $path));
        $local = str_starts_with($path, 'public/');
        $disk = Storage::disk($local ? 'public' : 'r2');
        $key = $local ? substr($path, 7) : $path;
        $keys = [$key];
        if (preg_match('#^(.*)/(thumb|medium|large)/([^/]+)$#', $key, $matches)) {
            $keys = array_map(fn ($size) => "{$matches[1]}/{$size}/{$matches[3]}", ['thumb', 'medium', 'large']);
        }
        $disk->delete($keys);
    }
}
