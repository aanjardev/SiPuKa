<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchasesMonthlyExport implements FromCollection, WithHeadings
{
    private Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    /**
     * Return a flat collection where each element is one Excel row (one unit).
     */
    public function collection(): Collection
    {
        $out = collect();
        $counter = 0;

        foreach ($this->rows as $purchase) {
            $kodeTrans = $purchase->kode_transaksi ?? ('#' . $purchase->id);
            $tanggal = optional($purchase->created_at)->format('Y-m-d H:i');
            $customer = $purchase->customer->nama ?? '-';
            $cabang = $purchase->perusahaan_cabang->nama ?? '-';
            $status = $purchase->status_pembelian ?? '-';
            $hargaDeal = (int) ($purchase->harga_deal ?? 0);

            foreach ($purchase->item_pembelian_draft as $item) {
                $qty = max(1, (int) ($item->qty ?? 1));
                $kodeItem = $item->kode ?? $item->kode_item ?? '-';
                $namaItem = $item->nama_item ?? $item->nama ?? '-';
                // Use purchase-level offered prices if present, otherwise fallback to item-level
                $hargaCustomer = (int) ($purchase->harga_tawaran_customer ?? $item->harga_customer ?? $item->harga_jual ?? 0);
                $hargaToko = (int) ($purchase->harga_tawaran_toko ?? $item->harga_toko ?? $item->harga_beli ?? 0);

                // Detailed kondisi fields from ItemPembelian
                $kondisi_fisik = $item->kondisi_fisik ?? '-';
                $kondisi_baut = $item->kondisi_baut ?? '-';
                $kondisi_tutup_usb = $item->kondisi_tutup_usb ?? '-';
                $kondisi_grip = $item->kondisi_grip ?? '-';
                $kondisi_jamur_lensa = $item->kondisi_jamur_lensa ?? '-';
                $kondisi_view_finder = $item->kondisi_view_finder ?? '-';
                $kondisi_mounting = $item->kondisi_mounting ?? '-';
                $kondisi_slot_memori = $item->kondisi_slot_memori ?? '-';
                $kondisi_jamur_sensor = $item->kondisi_jamur_sensor ?? '-';
                $kondisi_lcd = $item->kondisi_lcd ?? '-';
                $kondisi_tombol = $item->kondisi_tombol ?? '-';
                $kondisi_zoom_lensa = $item->kondisi_zoom_lensa ?? '-';
                $kondisi_af_lensa = $item->kondisi_af_lensa ?? '-';
                $kondisi_diafragma_lensa = $item->kondisi_diafragma_lensa ?? '-';
                $kondisi_kalibrasi_fokus = $item->kondisi_kalibrasi_fokus ?? '-';
                $kondisi_flash = $item->kondisi_flash ?? '-';
                $kondisi_sound_mic = $item->kondisi_sound_mic ?? '-';
                $kondisi_lain_lain = $item->kondisi_lain_lain ?? '-';

                for ($u = 1; $u <= $qty; $u++) {
                    $counter++;
                    $out->push([
                        $counter,
                        $kodeTrans,
                        $tanggal,
                        $customer,
                        $kodeItem,
                        $namaItem,
                        $kondisi_fisik,
                        $kondisi_baut,
                        $kondisi_tutup_usb,
                        $kondisi_grip,
                        $kondisi_jamur_lensa,
                        $kondisi_view_finder,
                        $kondisi_mounting,
                        $kondisi_slot_memori,
                        $kondisi_jamur_sensor,
                        $kondisi_lcd,
                        $kondisi_tombol,
                        $kondisi_zoom_lensa,
                        $kondisi_af_lensa,
                        $kondisi_diafragma_lensa,
                        $kondisi_kalibrasi_fokus,
                        $kondisi_flash,
                        $kondisi_sound_mic,
                        $kondisi_lain_lain,
                        $u, // Unit No
                        $hargaCustomer,
                        $hargaToko,
                        $cabang,
                        $status,
                        $hargaDeal,
                    ]);
                }
            }
        }

        return $out;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Transaksi',
            'Tanggal',
            'Customer',
            'Kode Item',
            'Nama Item',
            'Kondisi Fisik',
            'Kondisi Baut',
            'Kondisi Tutup USB',
            'Kondisi Grip',
            'Kondisi Jamur Lensa',
            'Kondisi View Finder',
            'Kondisi Mounting',
            'Kondisi Slot Memori',
            'Kondisi Jamur Sensor',
            'Kondisi LCD',
            'Kondisi Tombol',
            'Kondisi Zoom Lensa',
            'Kondisi AF Lensa',
            'Kondisi Diafragma Lensa',
            'Kondisi Kalibrasi Fokus',
            'Kondisi Flash',
            'Kondisi Sound/Mic',
            'Kondisi Lain-lain',
            'Unit No',
            'Harga Customer',
            'Harga Toko',
            'Cabang',
            'Status',
            'Harga Deal (Transaksi)'
        ];
    }
}
