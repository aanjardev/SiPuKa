<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamOperasionalCabang extends Model
{
    use HasFactory;

    protected $table = 'jam_operasional_cabang';

    protected $fillable = [
        'perusahaan_cabang_id',
        'hari',
        'is_buka',
        'jam_buka',
        'jam_tutup',
        'catatan'
    ];

    protected $casts = [
        'is_buka' => 'boolean',
    ];

    /**
     * Relasi ke perusahaan cabang
     */
    public function perusahaanCabang()
    {
        return $this->belongsTo(PerusahaanCabang::class, 'perusahaan_cabang_id');
    }

    /**
     * Format jam buka-tutup untuk display
     */
    public function getJamOperasionalAttribute()
    {
        if (!$this->is_buka) {
            return 'Tutup';
        }

        if ($this->jam_buka && $this->jam_tutup) {
            return $this->jam_buka . ' - ' . $this->jam_tutup;
        }

        return 'Buka';
    }

    /**
     * Cek apakah cabang buka pada waktu tertentu
     */
    public function isBukaSekarang($waktu = null)
    {
        if (!$this->is_buka) {
            return false;
        }

        $waktu = $waktu ?: now();
        $hariSekarang = $this->getHariSekarang($waktu);

        $jamHariIni = $this->where('hari', $hariSekarang)
            ->where('perusahaan_cabang_id', $this->perusahaan_cabang_id)
            ->first();

        if (!$jamHariIni || !$jamHariIni->is_buka) {
            return false;
        }

        if ($jamHariIni->jam_buka && $jamHariIni->jam_tutup) {
            return $waktu->between($jamHariIni->jam_buka, $jamHariIni->jam_tutup);
        }

        return true;
    }

    /**
     * Get nama hari dalam Bahasa Indonesia
     */
    private function getHariSekarang($waktu)
    {
        $hariInggris = $waktu->format('l');
        $hariIndonesia = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];

        return $hariIndonesia[$hariInggris] ?? 'Senin';
    }

    /**
     * Get semua hari dalam seminggu
     */
    public static function getAllHari()
    {
        return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
    }
}
