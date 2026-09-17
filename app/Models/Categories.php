<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Categories extends Model
{
    use HasFactory;
    protected $table = 'kategori';
    protected $fillable = [
        'nama_kategori',
        'path_gambar',
    ];
    protected $appends = ['image_url'];

    /**
     * Relasi dengan Produk
     */
    public function products()
    {
        return $this->hasMany(Produk::class, 'id_kategori');
    }

    public function usedCount()
    {
        return $this->products()->count();
    }

    /**
     * Cek apakah kategori digunakan oleh produk
     */
    public function isUsed()
    {
        return $this->produk()->exists();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->path_gambar) {
            return null;
        }

        $base = rtrim(config('filesystems.disks.r2.url', ''), '/');

        if ($base) {
            return $base . '/' . ltrim($this->path_gambar, '/');
        }

        return Storage::disk('r2')->url($this->path_gambar);
    }
}
