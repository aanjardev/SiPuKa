@forelse ($data_pembelian as $index => $pembelian)
    {{-- Baris sekarang akan membuka modal detail jika diklik, kecuali tombol aksi --}}
    <tr class="purchase-row" data-detail-url="{{ route('admin.purchases.show', $pembelian->id) }}">
        <td class="text-center text-muted fw-bold">{{ ($data_pembelian->firstItem() ?? 0) + $index }}</td>

        {{-- Kode Transaksi --}}
        <td>
            <span class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small">
                {{ $pembelian->kode_transaksi ?? '#' . $pembelian->id }}
            </span>
        </td>

        {{-- Customer --}}
        <td>
            <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                {{ $pembelian->customer->nama ?? '-' }}
            </span>
        </td>

        {{-- Tanggal --}}
        <td class="text-muted small opacity-90">
            <span class="fw-medium text-dark">{{ $pembelian->created_at->format('d M Y') }}</span>
            <span class="opacity-75">{{ $pembelian->created_at->format('H:i') }} WIB</span>
        </td>

        {{-- Cabang --}}
        <td class="text-dark small">
            {{ $pembelian->perusahaan_cabang->nama ?? '-' }}
        </td>

        {{-- Item Dibeli --}}
        <td class="col text-wrap" style="max-width: 300px; min-width: 250px;">
            <span class="text-secondary small d-block text-wrap text-break"
                title="{{ $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ') }}">
                @php
                    $itemNames = $pembelian->item_pembelian_draft->pluck('nama_item')->implode(', ');
                    echo $itemNames ?: '-';
                @endphp
            </span> {{-- Menutup <span> di sini (versi input-pembelian) --}}
        </td>

        {{-- Status --}}
        <td class="text-center"> {{-- Kolom Status dimulai di sini --}}
            @if($pembelian->status_pembelian == 'deal')
                {{-- Menggunakan badge style modern dari main + rounded-pill dari input-pembelian --}}
                <span class="badge rounded-pill bg-success-subtle text-success-emphasis">Deal</span>
            @elseif($pembelian->status_pembelian == 'tidak_deal')
                {{-- Menggunakan badge style modern dari main + rounded-pill dari input-pembelian --}}
                <span class="badge rounded-pill bg-danger-subtle text-danger-emphasis">No-Deal</span>
            @else
                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary">Draft</span>
            @endif
        </td>

        {{-- Harga Deal --}}
        <td class="fw-bold text-dark"> {{-- Menggunakan class visual dari input-pembelian --}}
            @if($pembelian->harga_deal)
                {{-- Menggunakan check keberadaan data dari main --}}
                Rp{{ number_format($pembelian->harga_deal, 0, ',', '.') }}
            @else
                -
            @endif
        </td>

        {{-- Aksi --}}
        <td class="text-center" style="width:120px">
            <div class="d-flex justify-content-center gap-2">
                {{-- Edit --}}
                <a href="{{ route('admin.purchases.edit', $pembelian->id) }}"
                    class="btn-action btn-action-edit no-row-navigation"
                    title="Edit">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>

                {{-- Hapus --}}
                <form action="{{ route('admin.purchases.destroy', $pembelian->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" {{-- Pastikan type="button" agar tidak langsung submit --}}
                            class="btn-action btn-action-delete no-row-navigation"
                            title="Hapus"
                            onclick="confirmDeletePurchase(this)"> {{-- Menggunakan fungsi confirm delete yang lebih spesifik --}}
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr class="tr-empty">
        <td colspan="9" class="text-center py-5">
            <div class="d-flex flex-column align-items-center opacity-50">
                <i class="fa-solid fa-cart-shopping fa-3x mb-3 text-muted"></i>
                <h6 class="text-muted">Belum ada data pembelian</h6>
                <p class="text-muted small mb-0">Silakan lakukan <a href="{{ route('admin.purchases.create') }}">transaksi pembelian</a> baru.</p>
            </div>
        </td>
    </tr>
@endforelse
