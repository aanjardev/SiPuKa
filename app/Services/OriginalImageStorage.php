<?php

namespace App\Services;

use App\Helpers\SimpleImageMover;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OriginalImageStorage
{
    public function upload(UploadedFile $file, string $folder): string
    {
        // Preserve original bytes and use a MIME-derived extension for PHP temp uploads.
        $temporary = $file->store('temp/original-images', 'local');
        if (! $temporary) {
            throw new \RuntimeException('Gagal menyiapkan file gambar.');
        }
        try {
            return SimpleImageMover::move(Storage::disk('local')->path($temporary), $folder.'/'.Str::uuid())['path'];
        } finally {
            Storage::disk('local')->delete($temporary);
        }
    }
}
