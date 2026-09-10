<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { margin-bottom: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f3f3f3; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .totals { margin-top: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Laporan Penjualan Bulan {{ $period }}</h1>
    <div class="meta">Cabang: {{ $branchLabel ?? 'Semua Cabang' }}</div>
    <div class="meta">Total data: {{ $rows->count() }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Item Terjual</th>
                <th>Cabang</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $index => $row)
                @php
                    $fallbackTotal = $row->detail_penjualan->sum(function ($detail) {
                        return (int) ($detail->qty ?? 0) * (int) ($detail->harga_jual_satuan ?? 0);
                    });
                    $rowTotal = ($row->harga_total ?? 0) > 0 ? $row->harga_total : $fallbackTotal;
                    $itemList = $row->detail_penjualan->map(function ($detail) {
                        $nama = $detail->produk->nama_produk ?? '-';
                        $qty = (int) ($detail->qty ?? 0);
                        return $nama . ' (' . $qty . ')';
                    })->implode(', ');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->kode_transaksi }}</td>
                    <td>{{ optional($row->created_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        {{ $row->customer->nama ?? '-' }}
                        @if (!empty($row->customer->no_telp))
                            <br>
                            <small>{{ $row->customer->no_telp }}</small>
                        @endif
                    </td>
                    <td>{{ $itemList ?: '-' }}</td>
                    <td>{{ $row->perusahaan_cabang->nama ?? '-' }}</td>
                    <td class="text-right">Rp{{ number_format($rowTotal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        Total Penjualan: Rp{{ number_format($totalNominal ?? 0, 0, ',', '.') }}
    </div>
    <div class="totals">
        Total HPP: Rp{{ number_format($totalHpp ?? 0, 0, ',', '.') }}
    </div>
</body>
</html>
