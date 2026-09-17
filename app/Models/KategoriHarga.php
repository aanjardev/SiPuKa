<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriHarga extends Model
{
    protected $table = 'kategori_harga';
    protected $fillable = ['nama', 'slug', 'harga_minimum', 'harga_maksimum', 'urutan', 'is_active'];
    protected $casts = ['harga_minimum'=>'integer', 'harga_maksimum'=>'integer', 'urutan'=>'integer', 'is_active'=>'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan')->orderBy('harga_minimum');
    }
}
