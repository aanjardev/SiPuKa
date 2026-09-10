<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembelian</title>
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
    <h1>Laporan Pembelian Bulan {{ $period }}</h1>
    <div class="meta">Cabang: {{ $branchLabel ?? 'Semua Cabang' }}</div>
    <div class="meta">Total data: {{ $rows->count() }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 6%;">No</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Item Dibeli</th>
                <th>Cabang</th>
                <th>Status</th>
                <th class="text-right">Harga Deal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $index => $row)
                @php
                    $itemList = $row->item_pembelian_draft->map(function ($item) {
                        $nama = $item->nama_item ?? '-';
                        $qty = (int) ($item->qty ?? 0);
                        return $nama . ' (' . $qty . ')';
                    })->implode(', ');
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->kode_transaksi ?? ('#' . $row->id) }}</td>
                    <td>{{ optional($row->created_at)->format('d/m/Y H:i') }}</td>
                    <td>{{ $row->customer->nama ?? '-' }}</td>
                    <td>{{ $itemList ?: '-' }}</td>
                    <td>{{ $row->perusahaan_cabang->nama ?? '-' }}</td>
                    <td>{{ $row->status_pembelian ?? '-' }}</td>
                    <td class="text-right">Rp{{ number_format((int) ($row->harga_deal ?? 0), 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals">
        Total Harga Deal: Rp{{ number_format($totalDeal ?? 0, 0, ',', '.') }}
    </div>
    <div class="totals">
        Total Item Deal: {{ number_format($totalItemDeal ?? 0, 0, ',', '.') }}
    </div>
    <div class="totals">
        Total Transaksi Deal: {{ number_format($totalTransaksiDeal ?? 0, 0, ',', '.') }}
    </div>
    <div class="totals">
        Total Transaksi No-Deal: {{ number_format($totalTransaksiNoDeal ?? 0, 0, ',', '.') }}
    </div>
</body>
</html>
