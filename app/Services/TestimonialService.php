<?php

namespace App\Services;

use App\Helpers\StoredImage;
use App\Models\Testimonial;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TestimonialService
{
    public function __construct(private OriginalImageStorage $images) {}

    public function save(Testimonial $testimonial, array $data, ?UploadedFile $screenshot, bool $removeScreenshot = false): void
    {
        $creating = ! $testimonial->exists;
        if ($creating && Testimonial::where('submission_token', $data['submission_token'])->exists()) {
            return;
        }
        $newPath = null;
        if ($screenshot) {
            try {
                $newPath = $this->images->upload($screenshot, 'catalog/testimonials');
            } catch (\Throwable $exception) {
                report($exception);
                throw ValidationException::withMessages(['screenshot' => 'Screenshot gagal disimpan. Data sebelumnya tetap tersedia. Silakan coba kembali.']);
            }
        }
        try {
            $oldPath = DB::transaction(function () use ($testimonial, $data, $newPath, $removeScreenshot, $creating) {
                $current = $creating ? $testimonial : Testimonial::lockForUpdate()->findOrFail($testimonial->id);
                $oldPath = $current->screenshot_path;
                if ($newPath || $removeScreenshot) {
                    $data['screenshot_path'] = $newPath;
                }
                $current->fill($data)->save();

                return $oldPath;
            });
        } catch (\Throwable $exception) {
            $this->cleanup($newPath);
            if ($exception instanceof \Illuminate\Database\QueryException && $creating && Testimonial::where('submission_token', $data['submission_token'])->exists()) {
                return;
            }
            throw $exception;
        }
        if ($newPath || $removeScreenshot) {
            $this->cleanup($oldPath);
        }
    }

    public function delete(Testimonial $testimonial): void
    {
        $testimonial->delete();
        $this->cleanup($testimonial->screenshot_path);
    }

    private function cleanup(?string $path): void
    {
        if (! $path) {
            return;
        }
        try {
            StoredImage::delete($path);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
