<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        
        body { font-family: sans-serif; font-size: 13px; margin: 0; padding: 0; }
        .container { width: 90%; margin: 0 auto; }

        
        .header {
            text-align: left;
            padding: 10px 0;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
            overflow: auto;
        }
        .logo-container {
            float: left;
            width: 15%;
            padding-right: 15px;
            box-sizing: border-box;
        }
        .text-details {
            overflow: hidden;
        }
        .text-details h3 {
            font-size: 24px; 
            margin: 0 0 5px 0 !important;
        }
        .text-details p {
            font-size: 14px; 
            margin: 0 0 3px 0 !important;
        }
        


        
        .footer { border-top: 1px solid #ccc; border-bottom: none; position: fixed; bottom: 0; width: 90%; }

        .content { margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }

        
        .item-table th, .item-table td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }
        .item-table th { background-color: #f0f0f0; }

        
        .price-table { border: none; }
        .price-table td { border: none; padding: 4px 8px; }

        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }

        .section-title {
            font-size: 13px;
            margin-top: 15px;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            font-weight: bold;
        }

        
        .signature-box { width: 100%; margin-top: 30px; }
        .signature-col { width: 50%; text-align: center; float: left; }
    </style>
</head>
<body>

<div class="container">
    {{-- HEADER PERUSAHAAN (DENGAN LOGO 70PX) --}}
    <div class="header">
        <div class="logo-container">
            {{-- Logo: Tinggi diubah menjadi 70px --}}
            <img src="{{ public_path('mainIMG/logoDinoyo.png') }}" alt="Logo Dinoyo Kamera" style="height: 70px;">
        </div>
        <div class="text-details">
            <h3 style="margin: 0; color: #333;">DINOYO KAMERA</h3>
            <p style="margin: 0;">Alamat: {{ $pembelian->perusahaan_cabang->nama ?? '-' }} ({{ $pembelian->perusahaan_cabang->alamat ?? '-' }})</p>
            <p style="margin: 0;">Kontak: {{ $pembelian->perusahaan_cabang->nomor_telepon ?? '-' }} | Instagram: @dinoyokamera</p>
        </div>
    </div>

    <div class="content">
        <h2 style="text-align: center; margin-bottom: 20px;">NOTA PEMBELIAN</h2>

        {{-- DETAIL TRANSAKSI --}}
        <div style="float: left; width: 70%;">
            <p style="margin: 5px;"><strong>No. Transaksi:</strong> {{ $pembelian->kode_transaksi }}</p>
            <p style="margin: 5px;"><strong>Tanggal:</strong> {{ $pembelian->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div style="float: right; width: 30%; text-align: left;">
            <p style="margin: 5px;"><strong>Customer:</strong> {{ $pembelian->customer->nama ?? '-' }}</p>
            <p style="margin: 5px;"><strong>Telp:</strong> {{ $pembelian->customer->no_telp ?? '-' }}</p>
            <p style="margin: 5px;"><strong>Kasir:</strong> {{ $pembelian->user->name ?? '-' }}</p>
        </div>
        <div style="clear: both;"></div>

        {{-- DAFTAR ITEM (DETAIL + KONDISI) --}}
        <div class="section-title">DAFTAR ITEM</div>
        <table class="item-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 45%;">Detail Item</th>
                    <th style="width: 50%;">Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pembelian->item_pembelian_draft as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div><strong>Nama:</strong> {{ $item->nama_item ?? '-' }}</div>
                        <div><strong>SN:</strong> {{ $item->serial_number ?? '-' }}</div>
                        <div><strong>SNL:</strong> {{ $item->serial_lens ?? '-' }}</div>
                        <div><strong>Kategori:</strong> {{ $item->kategori->nama_kategori ?? '-' }}</div>
                    </td>
                    <td>
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
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 50%; vertical-align: top; padding: 0; border: none;">
                                    @foreach ($conditionChunks[0] ?? [] as $label => $value)
                                        <div><strong>{{ $label }}:</strong> {{ $value }}</div>
                                    @endforeach
                                </td>
                                <td style="width: 50%; vertical-align: top; padding: 0; border: none;">
                                    @foreach ($conditionChunks[1] ?? [] as $label => $value)
                                        <div><strong>{{ $label }}:</strong> {{ $value }}</div>
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- RINGKASAN HARGA (HANYA HARGA DEAL) --}}
        <div style="clear: both;"></div>
        <div class="section-title" style="margin-top: 30px;">RINGKASAN HARGA</div>
        <table class="price-table" style="width: 50%; float: right; border: 1px solid #000;">
            <tr>
                <td class="fw-bold" style="background-color: #eee; padding: 8px 10px; width: 60%; border: none; font-size: 12px; border-right: 1px solid #000;">HARGA DEAL (FINAL)</td>
                <td class="fw-bold text-right" style="background-color: #eee; padding: 8px 10px; width: 40%; border: none; font-size: 12px;">Rp {{ number_format($pembelian->harga_deal, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div style="clear: both;"></div>

        {{-- TEMPAT TANDA TANGAN --}}
        <div class="signature-box">
            <div class="signature-col">
                <p style="margin-bottom: 50px;">Pembeli / Customer</p>
                <p style="border-bottom: 1px solid #000; width: 80%; margin: 0 auto; padding-bottom: 5px;">( {{ $pembelian->customer->nama ?? 'Nama Pembeli' }} )</p>
            </div>
            <div class="signature-col">
                <p style="margin-bottom: 50px;">Hormat Kami,</p>
                <p style="border-bottom: 1px solid #000; width: 80%; margin: 0 auto; padding-bottom: 5px;">( {{ $pembelian->user->name ?? 'Petugas' }} )</p>
            </div>
        </div>
        <div style="clear: both;"></div>

        {{-- NOTES / KETENTUAN --}}
        <div class="section-title" style="margin-top: 30px;">CATATAN & KETENTUAN</div>
        <ul style="font-size: 11px; padding-left: 15px; margin-top: 5px; list-style-type: disc;">
            <li style="margin-bottom: 5px;">Pembelian yang sudah disepakati (DEAL) tidak dapat dibatalkan atau ditukar kembali.</li>
            <li style="margin-bottom: 5px;">Kondisi unit dan serial number yang tertera pada nota ini adalah hasil pengecekan oleh petugas dan telah disetujui oleh Pembeli.</li>
            <li style="margin-bottom: 5px;">Nota ini berlaku sebagai bukti transaksi jual beli.</li>
        </ul>
    </div>

    {{-- FOOTER PERUSAHAAN --}}
    <div class="footer">
        <p style="margin: 0; font-size: 10px;">Terima kasih telah bertransaksi di {{ $pembelian->perusahaan_cabang->nama ?? 'Toko Kami' }}.</p>
    </div>
</div>

</body>
</html>
