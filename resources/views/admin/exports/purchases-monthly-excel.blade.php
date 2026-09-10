<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pembelian - Excel</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { margin-bottom: 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f3f3f3; text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Pembelian Bulan {{ $period }}</h1>
    <div class="meta">Cabang: {{ $branchLabel ?? 'Semua Cabang' }}</div>
    <div class="meta">Total transaksi: {{ $rows->count() }}</div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">No</th>
                <th>Kode Transaksi</th>
                <th>Tanggal</th>
                <th>Customer</th>
                <th>Kode Item</th>
                <th>Nama Item</th>
                <th>Kondisi Unit</th>
                <th>Unit No</th>
                <th>Harga Customer</th>
                <th>Harga Toko</th>
                <th>Cabang</th>
                <th>Status</th>
                <th class="text-right">Harga Deal (transaksi)</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 0; @endphp
            @forelse ($rows as $row)
                @foreach ($row->item_pembelian_draft as $item)
                    @php
                        $qty = (int) ($item->qty ?? 1);
                        $kodeItem = $item->kode ?? $item->kode_item ?? '-';
                        $namaItem = $item->nama_item ?? $item->nama ?? '-';
                        $kondisi = $item->kondisi ?? $item->kondisi_unit ?? '-';
                        $hargaCustomer = $item->harga_customer ?? $item->harga_jual ?? 0;
                        $hargaToko = $item->harga_toko ?? $item->harga_beli ?? 0;
                    @endphp
                    @for ($u = 0; $u < max($qty, 1); $u++)
                        @php $counter++; @endphp
                        <tr>
                            <td class="text-center">{{ $counter }}</td>
                            <td>{{ $row->kode_transaksi ?? ('#' . $row->id) }}</td>
                            <td>{{ optional($row->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $row->customer->nama ?? '-' }}</td>
                            <td>{{ $kodeItem }}</td>
                            <td>{{ $namaItem }}</td>
                            <td>{{ $kondisi }}</td>
                            <td class="text-center">{{ $u + 1 }}</td>
                            <td class="text-right">Rp{{ number_format((int) $hargaCustomer, 0, ',', '.') }}</td>
                            <td class="text-right">Rp{{ number_format((int) $hargaToko, 0, ',', '.') }}</td>
                            <td>{{ $row->perusahaan_cabang->nama ?? '-' }}</td>
                            <td>{{ $row->status_pembelian ?? '-' }}</td>
                            <td class="text-right">Rp{{ number_format((int) ($row->harga_deal ?? 0), 0, ',', '.') }}</td>
                        </tr>
                    @endfor
                @endforeach
            @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
