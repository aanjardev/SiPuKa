<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Kategori extends Model
{
    use HasFactory;
    protected $table = 'kategori';

    protected $fillable = ['nama_kategori', 'path_gambar'];
    protected $appends = ['image_url'];

    public function produk()
    {
        return $this->hasMany(Produk::class, 'id_kategori', 'id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->path_gambar) {
            return null;
        }

        $base = rtrim(env('CDN_BASE_URL', 'https://sidika.qurrotul-ainii0266.workers.dev'), '/');

        if ($base) {
            return $base . '/' . ltrim($this->path_gambar, '/');
        }

        return Storage::disk('r2')->url($this->path_gambar);
    }
}
