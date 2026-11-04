<?php

namespace App\Services;

use App\Helpers\StoredImage;
use App\Models\SellCameraPoster;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SellCameraPosterService
{
    public function __construct(private SellCameraPosterStorage $storage) {}

    public function uploadBatch(array $files, array $altTexts, string $token): bool
    {
        if (SellCameraPoster::where('batch_token', $token)->exists()) {
            return false;
        }
        $paths = [];
        try {
            foreach ($files as $file) {
                $paths[] = $this->storage->upload($file);
            }
        } catch (\Throwable $exception) {
            $this->cleanup($paths);
            report($exception);
            throw ValidationException::withMessages(['images' => 'Poster gagal disimpan. Tidak ada poster dalam unggahan ini yang ditambahkan. Silakan coba kembali.']);
        }
        try {
            $created = DB::transaction(function () use ($paths, $altTexts, $token) {
                $posters = SellCameraPoster::orderBy('id')->lockForUpdate()->get();
                if ($posters->contains('batch_token', $token)) {
                    return false;
                }
                $position = (int) $posters->max('position');
                foreach ($paths as $index => $path) {
                    SellCameraPoster::create(['image_path' => $path, 'alt_text' => $altTexts[$index] ?? null, 'position' => ++$position, 'batch_token' => $token, 'upload_index' => $index]);
                }

                return true;
            }, 3);
        } catch (\Throwable $exception) {
            $this->cleanup($paths);
            // A simultaneous replay may have committed while this request was uploading.
            if ($exception instanceof \Illuminate\Database\QueryException && SellCameraPoster::where('batch_token', $token)->exists()) {
                return false;
            }
            throw $exception;
        }
        if (! $created) {
            $this->cleanup($paths);
        }

        return $created;
    }

    public function update(SellCameraPoster $poster, array $data, ?\Illuminate\Http\UploadedFile $image): void
    {
        $newPath = null;
        if ($image) {
            try {
                $newPath = $this->storage->upload($image);
            } catch (\Throwable $exception) {
                report($exception);
                throw ValidationException::withMessages(['image' => 'Poster pengganti gagal disimpan. Poster lama tetap tersedia.']);
            }
        }
        try {
            $oldPath = DB::transaction(function () use ($poster, $data, $newPath) {
                $current = SellCameraPoster::lockForUpdate()->findOrFail($poster->id);
                $old = $current->image_path;
                $current->update(['alt_text' => $data['alt_text'] ?? null] + ($newPath ? ['image_path' => $newPath] : []));

                return $old;
            });
        } catch (\Throwable $exception) {
            $this->cleanup(array_filter([$newPath]));
            throw $exception;
        }
        if ($newPath) {
            $this->cleanup([$oldPath]);
        }
    }

    public function move(SellCameraPoster $poster, string $direction, int $expectedPosition): void
    {
        DB::transaction(function () use ($poster, $direction, $expectedPosition) {
            $posters = SellCameraPoster::orderBy('position')->orderBy('id')->lockForUpdate()->get();
            $index = $posters->search(fn ($item) => $item->id === $poster->id);
            abort_if($index === false, 404);
            if ($posters[$index]->position !== $expectedPosition) {
                return;
            }
            $neighbor = $direction === 'up' ? $index - 1 : $index + 1;
            if ($neighbor < 0 || $neighbor >= $posters->count()) {
                return;
            }
            $posters[$index]->update(['position' => $posters[$neighbor]->position]);
            $posters[$neighbor]->update(['position' => $expectedPosition]);
        }, 3);
    }

    public function delete(SellCameraPoster $poster): void
    {
        $poster->delete();
        $this->cleanup([$poster->image_path]);
    }

    private function cleanup(array $paths): void
    {
        foreach ($paths as $path) {
            try {
                StoredImage::delete($path);
            } catch (\Throwable $exception) {
                report($exception);
            }
        }
    }
}
