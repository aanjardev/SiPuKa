<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPembelian extends Model
{
    use HasFactory;
    protected $table = 'item_pembelian_draft';

    protected $fillable = [
        'pembelian_id',
        'kode_sku', // Diisi QC
        'nama_item',
        'kategori_id',
        'serial_number',
        'serial_lens',
        'kondisi_fisik',
        'kondisi_baut',
        'kondisi_tutup_usb',
        'kondisi_grip',
        'kondisi_jamur_lensa',
        'kondisi_view_finder',
        'kondisi_mounting',
        'kondisi_slot_memori',
        'kondisi_jamur_sensor',
        'kondisi_lcd',
        'kondisi_tombol',
        'kondisi_zoom_lensa',
        'kondisi_af_lensa',
        'kondisi_diafragma_lensa',
        'kondisi_kalibrasi_fokus',
        'kondisi_flash',
        'kondisi_sound_mic',
        'kondisi_lain_lain',
        'kelengkapan',
        'qty', // Diisi QC
        'harga_jual', // Diisi QC
        'harga_beli', // Diisi QC
        'harga_servis', // Diisi QC
        'grade', // Diisi QC
        'status', // (Status 'Baru'/'Second')
        'deskripsi_produk', // Diisi QC
        'status_qc', // Default 'menunggu_qc'
        'catatan_qc', // Diisi QC
    ];

    public function kategori()
    {

        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Relasi ke data Pembelian (induknya)
     */
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    /**
     * Accessor untuk menghitung persentase kelengkapan berdasarkan:
     * 1. kode_sku
     * 2. harga_jual
     * 3. deskripsi_produk
     *
     * Setiap kolom yang terisi (tidak null dan tidak kosong) = 25%
     */
    public function getPersentaseLengkapAttribute()
    {
        $totalFields = 3;
        $completedFields = 0;

        if (!empty($this->kode_sku)) {
            $completedFields++;
        }

        if (!empty($this->harga_jual) && $this->harga_jual > 0) {
            $completedFields++;
        }

        if (!empty($this->deskripsi_produk) && trim($this->deskripsi_produk) !== '') {
            $completedFields++;
        }

        return ($completedFields / $totalFields) * 100;
    }
}
