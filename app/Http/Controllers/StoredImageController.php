<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoredImageController extends Controller
{
    public function show(Request $request)
    {
        $path = $request->query('path');
        abort_unless(is_string($path) && $path !== '', 404);
        abort_if(str_contains($path, '..') || str_contains($path, '\\') || str_starts_with($path, '/'), 404);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $types = ['webp' => 'image/webp', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'avif' => 'image/avif'];
        abort_unless(isset($types[$extension]), 404);
        $local = str_starts_with($path, 'public/');
        $key = $local ? substr($path, 7) : $path;
        $disk = Storage::disk($local ? 'public' : 'r2');
        abort_unless($disk->exists($key), 404);
        return $disk->response($key, null, [
            'Content-Type' => $types[$extension],
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
