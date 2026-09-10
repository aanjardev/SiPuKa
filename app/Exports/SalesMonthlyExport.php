<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesMonthlyExport implements FromCollection, WithHeadings, WithMapping
{
    private int $rowIndex = 0;

    public function __construct(private Collection $rows)
    {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode',
            'Tanggal',
            'Customer',
            'Item Terjual',
            'Cabang',
            'Total',
        ];
    }

    public function map($sale): array
    {
        $this->rowIndex++;
        $fallbackTotal = $sale->detail_penjualan->sum(function ($detail) {
            return (int) ($detail->qty ?? 0) * (int) ($detail->harga_jual_satuan ?? 0);
        });
        $totalNominal = ($sale->harga_total ?? 0) > 0 ? $sale->harga_total : $fallbackTotal;

        $itemList = $sale->detail_penjualan->map(function ($detail) {
            $nama = $detail->produk->nama_produk ?? '-';
            $qty = (int) ($detail->qty ?? 0);
            return $nama . ' (' . $qty . ')';
        })->implode(', ');

        $customerName = $sale->customer->nama ?? '-';
        $customerPhone = $sale->customer->no_telp ?? '';
        $customerLabel = $customerPhone ? ($customerName . "\n" . $customerPhone) : $customerName;

        return [
            $this->rowIndex,
            $sale->kode_transaksi,
            optional($sale->created_at)->format('Y-m-d H:i'),
            $customerLabel,
            $itemList ?: '-',
            $sale->perusahaan_cabang->nama ?? '-',
            $totalNominal,
        ];
    }
}
