<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'kode_sku',
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
