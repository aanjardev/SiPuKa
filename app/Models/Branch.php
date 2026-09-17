<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $table = 'perusahaan_cabang';
    protected $fillable = [
        'singleton_key',
        'nama',
        'alamat',
        'nomor_telepon',
        'maps_embed_url',
        'is_active',
        'email',
        'deskripsi',
    ];

    protected $casts = [
        'singleton_key' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke jam operasional
     */
    public function jamOperasional()
    {
        return $this->hasMany(JamOperasionalCabang::class, 'perusahaan_cabang_id');
    }

    /**
     * Get jam operasional yang terstruktur per hari
     */
    public function getJamOperasionalTerstruktur()
    {
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $jamOperasional = $this->jamOperasional()->pluck('is_buka', 'hari')->toArray();
        
        $result = [];
        foreach ($hariList as $hari) {
            $result[$hari] = $jamOperasional[$hari] ?? true; // Default buka jika tidak ada setting
        }
        
        return $result;
    }

    /**
     * Cek apakah cabang buka sekarang
     */
    public function isBukaSekarang()
    {
        $jamOperasional = new JamOperasionalCabang();
        return $jamOperasional->where('perusahaan_cabang_id', $this->id)
            ->get()
            ->first(function ($jam) {
                return $jam->isBukaSekarang();
            }) !== null;
    }

    public function sales()
    {
        return $this->hasMany(Penjualan::class, 'perusahaan_cabang_id');
    }

    public function purchases()
    {
        return $this->hasMany(Pembelian::class, 'perusahaan_cabang_id');
    }
}
