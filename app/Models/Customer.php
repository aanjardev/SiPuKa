<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Penjualan;
use App\Models\Pembelian;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customer';

     protected $fillable = [
        'kode_customer',
        'nama',
        'no_telp',
        'email',
        'identitas',
        'jenis_kelamin',
        'alamat',
        'referensi',
        'keterangan',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {

            $prefix = 'CU';

            $latestCode = static::where('kode_customer', 'like', $prefix . '%')
                                ->latest('kode_customer')
                                ->pluck('kode_customer')
                                ->first();

            $number = 1;

            if ($latestCode) {

                $number = (int) substr($latestCode, -4);
                $number++;
            }

            $customer->kode_customer = $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
        });
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'customer_id');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'customer_id');
    }
}
