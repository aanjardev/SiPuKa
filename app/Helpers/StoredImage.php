<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StoredImage
{
    public static function url(?string $path): ?string
    {
        if (!$path) { return null; }
        if (str_starts_with($path, 'public/')) {
            return Storage::disk('public')->url(substr($path, 7));
        }
        $base = rtrim(config('filesystems.disks.r2.url', ''), '/');
        return $base ? $base . '/' . ltrim($path, '/') : Storage::disk('r2')->url($path);
    }

    public static function delete(?string $path): void
    {
        if (!$path) { return; }
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
