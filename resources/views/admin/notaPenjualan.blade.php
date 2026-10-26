<!DOCTYPE html>
<html>
<head>
    <title>Nota Penjualan #{{ $penjualan->kode_transaksi }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #2d2d2d;
            line-height: 1.5;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 750px;
            margin: 15px auto 0;
            padding: 0;
        }

        /* ===== HEADER ===== */
        .header {
            display: table;
            width: 100%;
            padding-bottom: 12px;
            border-bottom: 3px solid #1a1a2e;
            margin-bottom: 18px;
        }
        .logo-container {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            padding-right: 15px;
        }
        .logo-container img {
            height: 65px;
            width: auto;
            display: block;
        }
        .text-details {
            display: table-cell;
            vertical-align: middle;
        }
        .text-details h3 {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 3px 0;
            letter-spacing: 0.5px;
        }
        .text-details p {
            font-size: 11px;
            color: #555;
            margin: 0 0 2px 0;
            line-height: 1.4;
        }

        /* ===== TITLE BAR ===== */
        .title-bar {
            background: #1a1a2e;
            color: #fff;
            text-align: center;
            padding: 8px 0;
            margin-bottom: 16px;
            border-radius: 3px;
        }
        .title-bar h2 {
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin: 0;
        }

        /* ===== INFO GRID ===== */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 18px;
        }
        .info-left {
            display: table-cell;
            width: 55%;
            vertical-align: top;
        }
        .info-right {
            display: table-cell;
            width: 45%;
            vertical-align: top;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }
        .info-label {
            display: table-cell;
            width: 110px;
            font-size: 11px;
            color: #777;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            font-size: 12px;
            color: #2d2d2d;
            font-weight: 500;
            vertical-align: top;
        }
        .info-value::before {
            content: ": ";
        }

        /* ===== SECTION TITLE ===== */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a1a2e;
            margin: 18px 0 8px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #e8e8e8;
        }

        /* ===== ITEM TABLE ===== */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .item-table thead th {
            background: #1a1a2e;
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 7px 10px;
            text-align: left;
            border: none;
        }
        .item-table thead th.text-right { text-align: right; }
        .item-table tbody td {
            padding: 7px 10px;
            font-size: 11.5px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .item-table tbody tr:nth-child(even) td {
            background: #fafafa;
        }
        .item-table tbody tr:last-child td {
            border-bottom: 2px solid #1a1a2e;
        }
        .item-table .row-num {
            color: #999;
            font-size: 11px;
            text-align: center;
            width: 40px;
        }
        .item-table .product-name {
            font-weight: 600;
            color: #2d2d2d;
        }
        .item-table .product-detail {
            font-size: 10px;
            color: #888;
            margin-top: 2px;
        }

        /* ===== HARGA DEPRESIASI ===== */
        .depreiasi-note {
            margin: 8px 0 0 0;
            padding: 6px 10px;
            background: #fff8e1;
            border-left: 3px solid #f9a825;
            font-size: 11px;
            color: #6d4c00;
        }
        .depreiasi-note strong { color: #4e342e; }

        /* ===== PRICE SUMMARY ===== */
        .price-summary {
            width: 320px;
            max-width: 100%;
            float: right;
            margin-top: 8px;
            border: 1px solid #e0e0e0;
            border-radius: 3px;
            overflow: hidden;
        }
        .price-row {
            display: table;
            width: 100%;
            padding: 5px 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .price-row:last-child { border-bottom: none; }
        .price-row .pr-label {
            display: table-cell;
            width: 55%;
            font-size: 11.5px;
            color: #555;
            vertical-align: middle;
        }
        .price-row .pr-value {
            display: table-cell;
            width: 45%;
            font-size: 11.5px;
            color: #2d2d2d;
            text-align: right;
            vertical-align: middle;
            font-weight: 500;
        }
        .price-row .pr-value.discount { color: #c62828; }
        .price-row.total {
            background: #1a1a2e;
            padding: 8px 12px;
        }
        .price-row.total .pr-label {
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .price-row.total .pr-value {
            color: #fff;
            font-weight: 700;
            font-size: 13px;
        }

        /* ===== SIGNATURE ===== */
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 35px;
        }
        .sig-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            padding: 0 20px;
        }
        .sig-label {
            font-size: 11px;
            color: #777;
            margin-bottom: 45px;
            font-weight: 500;
        }
        .sig-line {
            border-top: 1px solid #999;
            width: 70%;
            margin: 0 auto;
            padding-top: 5px;
        }
        .sig-name {
            font-size: 11.5px;
            font-weight: 600;
            color: #2d2d2d;
        }

        /* ===== NOTES & TERMS ===== */
        .notes-box {
            margin-top: 18px;
            padding: 10px 12px;
            background: #f9f9fb;
            border-radius: 3px;
            border-left: 3px solid #1a1a2e;
        }
        .notes-box p {
            font-size: 11px;
            color: #555;
            line-height: 1.5;
            margin: 0;
        }

        .terms-list {
            margin-top: 8px;
            padding-left: 18px;
        }
        .terms-list li {
            font-size: 10.5px;
            color: #777;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 2px solid #1a1a2e;
            text-align: center;
        }
        .footer p {
            font-size: 10px;
            color: #999;
            margin: 0;
            letter-spacing: 0.3px;
        }

        /* ===== UTILS ===== */
        .text-right { text-align: right; }
        .clearfix::after { content: ""; display: table; clear: both; }
    </style>
</head>
<body>

<div class="container">

    {{-- HEADER PERUSAHAAN --}}
    <div class="header">
        <div class="logo-container">
            <img src="{{ public_path('mainIMG/logopk.png') }}" alt="Logo">
        </div>
        <div class="text-details">
            <h3>{{ \App\Helpers\Brand::name() }}</h3>
            <p>{{ $penjualan->perusahaan_cabang->nama ?? '-' }} &mdash; {{ $penjualan->perusahaan_cabang->alamat ?? '-' }}</p>
            <p>Telp: {{ $penjualan->perusahaan_cabang->nomor_telepon ?? '-' }} &bull; IG: {{ \App\Models\CatalogSettings::first()?->instagram_link ?? "-" }}</p>
        </div>
    </div>

    {{-- TITLE --}}
    <div class="title-bar">
        <h2>Nota Penjualan</h2>
    </div>

    {{-- INFO TRANSAKSI --}}
    <div class="info-grid">
        <div class="info-left">
            <div class="info-row">
                <span class="info-label">Customer</span>
                <span class="info-value">{{ $penjualan->customer->nama ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Telp</span>
                <span class="info-value">{{ $penjualan->customer->no_telp ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-value">{{ $penjualan->user->name ?? '-' }}</span>
            </div>
        </div>
        <div class="info-right">
            <div class="info-row">
                <span class="info-label">No. Transaksi</span>
                <span class="info-value">{{ $penjualan->kode_transaksi }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ $penjualan->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Metode Bayar</span>
                <span class="info-value">{{ strtoupper($penjualan->kas ?? '-') }}</span>
            </div>
        </div>
    </div>

    {{-- DAFTAR ITEM --}}
    {{-- <div class="section-title">Daftar Item</div> --}}
    <table class="item-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 47%;">Nama Produk</th>
                <th style="width: 8%;" class="text-right">Qty</th>
                <th style="width: 20%;" class="text-right">Harga Satuan</th>
                <th style="width: 20%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach (($penjualan->detail_penjualan ?? []) as $index => $detail)
            @php
                $product = $detail->produk ?? null;
                $qty = (int) ($detail->qty ?? 0);
                $price = (int) ($detail->harga_jual_satuan ?? 0);
                $lineTotal = $qty * $price;
            @endphp
            <tr>
                <td class="row-num">{{ $index + 1 }}</td>
                <td>
                    <div class="product-name">{{ $product->nama_produk ?? '-' }}</div>
                    @if (!empty($product?->kode_sku))
                        <div class="product-detail">SKU: {{ $product->kode_sku }}</div>
                    @endif
                    @if (!empty($product?->serial_number) || !empty($product?->serial_lens))
                        <div class="product-detail">
                            @if (!empty($product?->serial_number))
                                SN: {{ $product->serial_number }}
                            @endif
                            @if (!empty($product?->serial_lens))
                                @if (!empty($product?->serial_number)) &nbsp;|&nbsp; @endif
                                SNL: {{ $product->serial_lens }}
                            @endif
                        </div>
                    @endif
                </td>
                <td class="text-right">{{ $qty }}</td>
                <td class="text-right">Rp {{ number_format($price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($lineTotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- HARGA DEPRESIASI --}}
    @php
        $hargaDepresiasiNota = (int) ($penjualan->detail_penjualan->first()->harga_depresiasi ?? 0);
    @endphp
    @if ($hargaDepresiasiNota > 0)
    <div class="depreiasi-note">
        <strong>Harga Depresiasi:</strong> Rp {{ number_format($hargaDepresiasiNota, 0, ',', '.') }}
    </div>
    @endif

    {{-- RINGKASAN HARGA --}}
    <div class="clearfix"></div>
    {{-- <div class="section-title" style="margin-top: 25px;">Ringkasan Harga</div> --}}

    @php
        $subtotal = (int) ($subtotal ?? ($penjualan->subtotal ?? 0));
        $diskon = (int) ($penjualan->diskon ?? 0);
        $biayaTambahan = (int) ($penjualan->biaya_tambahan ?? 0);
        if ($biayaTambahan === 0 && isset($penjualan->harga_total)) {
            $biayaTambahan = max(0, (int) $penjualan->harga_total - $subtotal + $diskon);
        }
        $totalBayar = $penjualan->harga_total ?? max(0, $subtotal - $diskon + $biayaTambahan);
        $hargaBeliKembaliMaksimal = (int) ($penjualan->detail_penjualan->first()->harga_depresiasi ?? 0);
    @endphp

    <div class="price-summary">
        <div class="price-row">
            <span class="pr-label">Subtotal Produk</span>
            <span class="pr-value">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
        </div>
        @if($diskon > 0)
        <div class="price-row">
            <span class="pr-label">Diskon</span>
            <span class="pr-value discount">- Rp {{ number_format($diskon, 0, ',', '.') }}</span>
        </div>
        @endif
        @if($biayaTambahan > 0)
        <div class="price-row">
            <span class="pr-label">Biaya Tambahan</span>
            <span class="pr-value">Rp {{ number_format($biayaTambahan, 0, ',', '.') }}</span>
        </div>
        @endif
        <div class="price-row total">
            <span class="pr-label">Total Bayar</span>
            <span class="pr-value">Rp {{ number_format($totalBayar, 0, ',', '.') }}</span>
        </div>
    </div>
    <div class="clearfix"></div>

    {{-- TANDA TANGAN --}}
    <div class="signature-section">
        <div class="sig-col">
            <div class="sig-label">Pembeli / Customer</div>
            <div class="sig-line">
                <div class="sig-name">( {{ $penjualan->customer->nama ?? 'Nama Pembeli' }} )</div>
            </div>
        </div>
        <div class="sig-col">
            <div class="sig-label">Hormat Kami,</div>
            <div class="sig-line">
                <div class="sig-name">( {{ $penjualan->perusahaan_cabang->nama ?? '-' }} )</div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>

    {{-- CATATAN TRANSAKSI --}}
    @if(!empty($penjualan->keterangan))
    <div class="section-title" style="margin-top: 20px;">Catatan</div>
    <div class="notes-box">
        <p>{{ $penjualan->keterangan }}</p>
    </div>
    @endif

    {{-- KETENTUAN --}}
    <div class="section-title" style="margin-top: 18px;">Ketentuan</div>
    <ul class="terms-list">
        <li>Barang yang sudah dibeli tidak dapat dikembalikan kecuali ada perjanjian tertulis.</li>
        <li>Kerusakan setelah barang diterima bukan tanggung jawab toko kecuali tercantum dalam garansi.</li>
        <li>Nota ini berlaku sebagai bukti transaksi penjualan.</li>
    </ul>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Terima kasih telah bertransaksi di {{ $penjualan->perusahaan_cabang->nama ?? 'Toko Kami' }}</p>
    </div>

</div>

</body>
</html>
