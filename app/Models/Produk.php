<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'id_kategori_harga',
        'kode_sku',
        'serial_number',
        'serial_lens',
        'id_kategori',
        'nama_produk',
        'harga_jual',
        'stok_produk',
        'deskripsi_produk',
        'instagram_link',
        'status',
        'grade',
        'harga_beli',
        'harga_servis',
        'is_visible',
        'is_archived',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'is_archived' => 'boolean',
    ];

    public function getGradeLabelAttribute(): string
    {
        return config('product_grades.labels.'.$this->grade, $this->grade ?: '-');
    }

    public function getVideoUrlAttribute(): ?string
    {
        $url = $this->instagram_link;
        if (! is_string($url) || ! filter_var($url, FILTER_VALIDATE_URL)
            || ! in_array(strtolower((string) parse_url($url, PHP_URL_SCHEME)), ['http', 'https'], true)) {
            return null;
        }

        return $url;
    }

    public function kategoriHarga()
    {
        return $this->belongsTo(KategoriHarga::class, 'id_kategori_harga');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id');
    }

    public function gambar()
    {
        return $this->hasMany(GambarProduk::class, 'id_produk', 'id');
    }

    public function gambarUtama()
    {
        return $this->hasOne(GambarProduk::class, 'id_produk', 'id')->where('is_main', true);
    }

    public function semuaGambar()
    {
        return $this->hasMany(GambarProduk::class, 'id_produk', 'id');
    }

    /**
     * Alias untuk kompatibilitas view yang menggunakan ->photos
     * sehingga $product->photos akan mengembalikan collection gambar.
     */
    public function photos()
    {
        return $this->gambar();
    }
}
