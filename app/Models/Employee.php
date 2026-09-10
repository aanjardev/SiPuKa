<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    
    protected $table = 'karyawan';
    

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nik',
        'jabatan',
        'gaji',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
        'nomor_telepon',
        'alamat'
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id');
    }
}
