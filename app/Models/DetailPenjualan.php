<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Penjualan;
use App\Models\Produk;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $table = 'detail_penjualan';

    protected $guarded = [];

    public function sale()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function product()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function produk()
    {
        return $this->product();
    }
}
