<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
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
        .item-table tbody td {
            padding: 8px 10px;
            font-size: 11px;
            vertical-align: top;
        }
        .item-table tbody tr:last-child td {
            border-bottom: 2px solid #1a1a2e;
        }
        .item-table .row-num {
            color: #555;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            width: 35px;
            background: #f4f5f8;
            border-top: 1px solid #ddd;
            border-bottom: 2px solid #ddd;
            border-right: 1px solid #e2e4ea;
            vertical-align: middle;
        }

        /* Detail item styling */
        .item-row-header td {
            background: #f4f5f8;
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #e2e4ea;
            padding: 8px 10px;
        }
        .item-product-title {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }
        .item-meta-grid {
            display: table;
            width: 100%;
        }
        .item-meta-item {
            display: table-cell;
            font-size: 11px;
            color: #444;
            padding-right: 15px;
        }
        .item-meta-item strong {
            color: #666;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
        }

        .item-row-condition td {
            padding: 8px 10px 12px 10px;
            background: #fff;
            border-bottom: 2px solid #ddd;
        }
        .condition-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #888;
            margin-bottom: 6px;
        }

        /* Condition grid */
        .condition-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .condition-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 10px;
        }
        .condition-col:last-child {
            padding-right: 0;
            padding-left: 10px;
        }
        .condition-item {
            margin-bottom: 2px;
            font-size: 10.5px;
            line-height: 1.4;
        }
        .condition-item .c-label {
            color: #888;
            font-weight: 500;
        }
        .condition-item .c-value {
            color: #2d2d2d;
        }

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
            padding: 8px 12px;
            background: #1a1a2e;
        }
        .price-row .pr-label {
            display: table-cell;
            width: 55%;
            font-size: 12px;
            color: #fff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }
        .price-row .pr-value {
            display: table-cell;
            width: 45%;
            font-size: 13px;
            color: #fff;
            text-align: right;
            font-weight: 700;
            vertical-align: middle;
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

        /* ===== TERMS ===== */
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
            <p>{{ $pembelian->perusahaan_cabang->nama ?? '-' }} &mdash; {{ $pembelian->perusahaan_cabang->alamat ?? '-' }}</p>
            <p>Telp: {{ $pembelian->perusahaan_cabang->nomor_telepon ?? '-' }} &bull; IG: {{ \App\Models\CatalogSettings::first()?->instagram_link ?? "-" }}</p>
        </div>
    </div>

    {{-- TITLE --}}
    <div class="title-bar">
        <h2>Nota Pembelian</h2>
    </div>

    {{-- INFO TRANSAKSI --}}
    <div class="info-grid">
        <div class="info-left">
            <div class="info-row">
                <span class="info-label">Customer</span>
                <span class="info-value">{{ $pembelian->customer->nama ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Telp</span>
                <span class="info-value">{{ $pembelian->customer->no_telp ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kasir</span>
                <span class="info-value">{{ $pembelian->user->name ?? '-' }}</span>
            </div>
        </div>
        <div class="info-right">
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ $pembelian->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">No. Transaksi</span>
                <span class="info-value">{{ $pembelian->kode_transaksi }}</span>
            </div>
        </div>
    </div>

    {{-- DAFTAR ITEM --}}
    {{-- <div class="section-title">Daftar Item</div> --}}
    <table class="item-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 95%;">Daftar Item &amp; Kondisi Produk</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembelian->item_pembelian_draft as $index => $item)
            {{-- BARIS 1: Identitas Item Produk --}}
            <tr class="item-row-header">
                <td class="row-num" rowspan="2">{{ $index + 1 }}</td>
                <td>
                    <div class="item-product-title">{{ $item->nama_item ?? '-' }}</div>
                    <div class="item-meta-grid">
                        <div class="item-meta-item"><strong>Kategori:</strong> {{ $item->kategori->nama_kategori ?? '-' }}</div>
                        <div class="item-meta-item"><strong>SN:</strong> {{ $item->serial_number ?: '-' }}</div>
                        <div class="item-meta-item"><strong>SNL:</strong> {{ $item->serial_lens ?: '-' }}</div>
                    </div>
                </td>
            </tr>
            {{-- BARIS 2: Rincian Kondisi Produk --}}
            <tr class="item-row-condition">
                <td>
                    <div class="condition-title">Rincian Kondisi:</div>
                    @php
                        $conditions = [
                            'Fisik' => $item->kondisi_fisik ?? '-',
                            'Baut' => $item->kondisi_baut ?? '-',
                            'Tutup USB' => $item->kondisi_tutup_usb ?? '-',
                            'Grip' => $item->kondisi_grip ?? '-',
                            'Jamur Lensa' => $item->kondisi_jamur_lensa ?? '-',
                            'View Finder' => $item->kondisi_view_finder ?? '-',
                            'Mounting' => $item->kondisi_mounting ?? '-',
                            'Slot Memori' => $item->kondisi_slot_memori ?? '-',
                            'Jamur Sensor' => $item->kondisi_jamur_sensor ?? '-',
                            'LCD' => $item->kondisi_lcd ?? '-',
                            'Tombol' => $item->kondisi_tombol ?? '-',
                            'Zoom Lensa' => $item->kondisi_zoom_lensa ?? '-',
                            'AF Lensa' => $item->kondisi_af_lensa ?? '-',
                            'Diafragma' => $item->kondisi_diafragma_lensa ?? '-',
                            'Kalibrasi Fokus' => $item->kondisi_kalibrasi_fokus ?? '-',
                            'Flash' => $item->kondisi_flash ?? '-',
                            'Sound Mic' => $item->kondisi_sound_mic ?? '-',
                            'Lain-lain' => $item->kondisi_lain_lain ?? '-',
                            'Kelengkapan' => $item->kelengkapan ?? '-',
                        ];
                        $conditionChunks = array_chunk($conditions, (int) ceil(count($conditions) / 2), true);
                    @endphp
                    <div class="condition-grid">
                        <div class="condition-col">
                            @foreach ($conditionChunks[0] ?? [] as $label => $value)
                                <div class="condition-item">
                                    <span class="c-label">{{ $label }}:</span>
                                    <span class="c-value">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="condition-col">
                            @foreach ($conditionChunks[1] ?? [] as $label => $value)
                                <div class="condition-item">
                                    <span class="c-label">{{ $label }}:</span>
                                    <span class="c-value">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- RINGKASAN HARGA --}}
    <div class="clearfix"></div>
    {{-- <div class="section-title" style="margin-top: 25px;">Ringkasan Harga</div> --}}

    <div class="price-summary">
        <div class="price-row">
            <span class="pr-label">HARGA DEAL</span>
            <span class="pr-value">Rp {{ number_format($pembelian->harga_deal, 0, ',', '.') }}</span>
        </div>
    </div>
    <div class="clearfix"></div>

    {{-- TANDA TANGAN --}}
    <div class="signature-section">
        <div class="sig-col">
            <div class="sig-label">Pembeli / Customer</div>
            <div class="sig-line">
                <div class="sig-name">( {{ $pembelian->customer->nama ?? 'Nama Pembeli' }} )</div>
            </div>
        </div>
        <div class="sig-col">
            <div class="sig-label">Hormat Kami,</div>
            <div class="sig-line">
                <div class="sig-name">( {{ $pembelian->perusahaan_cabang->nama ?? '-' }} )</div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>

    {{-- KETENTUAN --}}
    <div class="section-title" style="margin-top: 20px;">Catatan &amp; Ketentuan</div>
    <ul class="terms-list">
        <li>Pembelian yang sudah disepakati (DEAL) tidak dapat dibatalkan atau ditukar kembali.</li>
        <li>Kondisi unit dan serial number yang tertera pada nota ini adalah hasil pengecekan oleh petugas dan telah disetujui oleh Pembeli.</li>
        <li>Nota ini berlaku sebagai bukti transaksi jual beli.</li>
    </ul>

    {{-- FOOTER --}}
    <div class="footer">
        <p>Terima kasih telah bertransaksi di {{ $pembelian->perusahaan_cabang->nama ?? 'Toko Kami' }}</p>
    </div>

</div>

</body>
</html>
