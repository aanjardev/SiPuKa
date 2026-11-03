<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use League\Flysystem\UnableToReadFile;

class ImageResponseService
{
    public function respond(Request $request, string $path, int $width = 0)
    {
        abort_if($path === '' || str_contains($path, '..') || str_contains($path, '\\') || str_starts_with($path, '/'), 404);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $types = ['webp' => 'image/webp', 'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'avif' => 'image/avif'];
        abort_unless(isset($types[$extension]), 404);
        abort_unless(in_array($width, [0, 320, 640, 1280], true), 404);
        $local = str_starts_with($path, 'public/');
        if ($local) {
            $source = Storage::disk('public')->path(substr($path, 7));
            abort_unless(is_file($source), 404);
        } else {
            $source = $this->cachedFile($path, 'source.'.$extension, function ($target) use ($path) {
                try {
                    $stream = Storage::disk('r2')->readStream($path);
                } catch (UnableToReadFile $exception) {
                    abort(404);
                }
                abort_unless(is_resource($stream), 404);
                $output = fopen($target, 'wb');
                if (! $output) {
                    fclose($stream);
                    throw new \RuntimeException('Cache gambar tidak dapat ditulis.');
                }
                try {
                    if (stream_copy_to_stream($stream, $output) === false) {
                        throw new \RuntimeException('Gagal menyalin gambar ke cache.');
                    }
                } finally {
                    fclose($stream);
                    fclose($output);
                }
            });
        }
        $file = $source;
        if ($width) {
            $dimensions = @getimagesize($source);
            // Preserve animations and avoid decoding exceptionally large legacy uploads.
            if (! $dimensions || $dimensions[0] * $dimensions[1] > 16000000 || ! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $width = 0;
            }
        }
        if ($width) {
            // Include the source version so overwritten local files cannot reuse old thumbnails.
            $version = filemtime($source).'-'.filesize($source);
            $file = $this->cachedFile($path, $width.'-'.$version.'.webp', function ($target) use ($source, $width) {
                $image = Image::make($source)->orientate();
                $image->resize($width, $width, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $image->save($target, 82, 'webp');
                $image->destroy();
            });
        }
        $response = response()->file($file, [
            'Content-Type' => $width ? 'image/webp' : $types[$extension],
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'public, max-age=3600',
        ]);
        $response->setEtag(hash('sha256', $path.'|'.$width.'|'.filemtime($file).'|'.filesize($file)));
        $response->setLastModified(new \DateTimeImmutable('@'.filemtime($file)));
        $response->isNotModified($request);

        return $response;
    }

    private function cachedFile(string $path, string $variant, callable $write): string
    {
        $directory = Storage::disk('local')->path('image-cache/'.hash('sha256', $path));
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException('Folder cache gambar tidak dapat dibuat.');
        }
        $file = $directory.'/'.$variant;
        if (is_file($file) && filemtime($file) > time() - 3600) {
            return $file;
        }
        $lock = fopen($directory.'/write.lock', 'c');
        if (! $lock || ! flock($lock, LOCK_EX)) {
            throw new \RuntimeException('Cache gambar tidak dapat dikunci.');
        }
        $temporary = null;
        try {
            clearstatcache(true, $file);
            if (! is_file($file) || filemtime($file) <= time() - 3600) {
                $temporary = tempnam($directory, 'pending-');
                $write($temporary);
                if (! rename($temporary, $file)) {
                    throw new \RuntimeException('Cache gambar tidak dapat disimpan.');
                }
            }
        } finally {
            if ($temporary && is_file($temporary)) {
                unlink($temporary);
            }
            flock($lock, LOCK_UN);
            fclose($lock);
        }

        return $file;
    }
}
