<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\Branch as PerusahaanCabang;
use App\Models\User;
use App\Models\ItemPembelian as ItemPembelianDraft;

class Pembelian extends Model
{
    use HasFactory;
    protected $table = 'pembelian';

    protected $fillable = [
        'kode_transaksi', // TAMBAH INI
        'customer_id',
        'perusahaan_cabang_id',
        'user_id',
        'kas',
        'keterangan',
        'harga_tawaran_customer',
        'harga_tawaran_toko',
        'harga_deal',
        'status_pembelian',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Customer (Satu Pembelian dimiliki oleh satu Customer)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relasi ke Cabang (Satu Pembelian milik satu Cabang)
     * Ganti 'PerusahaanCabang' dengan 'Branch' jika itu nama model Anda
     */
    public function perusahaan_cabang()
    {
        return $this->belongsTo(PerusahaanCabang::class, 'perusahaan_cabang_id');
    }

    /**
     * Relasi ke User/Karyawan (Satu Pembelian diinput oleh satu User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Item Draft (Satu Pembelian memiliki BANYAK item draft)
     */
    public function item_pembelian_draft()
    {
        return $this->hasMany(ItemPembelianDraft::class, 'pembelian_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pembelian) {

            $prefix = 'PB' . date('Ym'); // Contoh: PB202511

            $latestCode = static::where('kode_transaksi', 'like', $prefix . '%')
                                ->latest('kode_transaksi')
                                ->pluck('kode_transaksi')
                                ->first();

            $number = 1;

            if ($latestCode) {

                $number = (int) substr($latestCode, -4);
                $number++;
            }

            $pembelian->kode_transaksi = $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }
}
