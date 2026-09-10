<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GambarProduk extends Model
{
    use HasFactory;

    protected $table = 'gambar_produk';

    protected $fillable = ['id_produk', 'path_gambar', 'is_main'];

    protected $appends = ['url'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id');
    }

    protected static function booted(): void
    {
        static::deleting(function (GambarProduk $gambar) {
            $gambar->deleteVariants($gambar->path_gambar);
        });
    }

    public function getUrlAttribute(): string
    {
        $base = rtrim(env('CDN_BASE_URL', 'https://sidika.qurrotul-ainii0266.workers.dev'), '/');

        if ($base) {
            return $base . '/' . ltrim($this->path_gambar, '/');
        }

        return Storage::disk('r2')->url($this->path_gambar);
    }

    private function deleteVariants(?string $path): void
    {
        if (!$path) {
            return;
        }

        $disk = Storage::disk('r2');

        if (preg_match('#^(.*)/(thumb|medium|large)/([^/]+)$#', $path, $m)) {
            $base = $m[1];
            $file = $m[3];
            foreach (['thumb', 'medium', 'large'] as $size) {
                $candidate = "{$base}/{$size}/{$file}";
                if ($disk->exists($candidate)) {
                    $disk->delete($candidate);
                }
            }
            return;
        }

        if ($disk->exists($path)) {
            $disk->delete($path);
        }
    }
}
