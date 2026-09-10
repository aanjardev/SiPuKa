@forelse ($data_qc as $index => $item)
    <tr class="clickable-row" data-detail-url="{{ route('admin.quality-control.edit', $item->id) }}">
        <td class="text-center text-muted fw-bold">{{ ($data_qc->firstItem() ?? 0) + $index }}</td>

        {{-- ID Pembelian --}}
        <td>
            @if($item->pembelian)
                <a href="{{ route('admin.purchases.show', $item->pembelian->id) }}"
                   class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small text-decoration-none clickable-code qc-code-chip"
                   onclick="event.stopPropagation();">
                    {{ $item->pembelian->kode_transaksi ?? ('#' . $item->pembelian->id) }}
                </a>
            @else
                <span class="fw-bold text-secondary font-monospace bg-light px-2 py-1 rounded small qc-code-chip">QC Manual</span>
            @endif
        </td>

        {{-- Nama Item --}}
        <td class="col text-wrap" style="max-width: 200px; min-width: 150px;">
            <span class="text-secondary small d-block text-wrap text-break">{{ $item->nama_item }}</span>
        </td>

        {{-- Serial Number --}}
        <td class="text-secondary font-monospace small">{{ $item->serial_number ?? '-' }}</td>

        {{-- SN Lensa --}}
        <td class="text-secondary font-monospace small">{{ $item->serial_lens ?? '-' }}</td>

        {{-- Kategori --}}
        <td class="text-dark">{{ $item->kategori->nama_kategori ?? '-' }}</td>

        {{-- Progress QC --}}
        <td>
            @php $persen = round($item->persentase_lengkap); @endphp
            <div class="d-flex align-items-center gap-2">
                <div class="progress flex-grow-1 shadow-sm" style="height: 6px; border-radius: 10px; background-color: #f0f2f5;">
                    <div class="progress-bar {{ $persen == 100 ? 'bg-success' : 'bg-primary' }}" role="progressbar" style="width: {{ $persen }}%; border-radius: 10px;" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <span class="small fw-bold text-muted" style="font-size: 0.8rem; min-width: 35px; text-align: right;">{{ $persen }}%</span>
            </div>
        </td>

        {{-- Aksi --}}
        <td class="text-center no-row-navigation">
            <a href="{{ route('admin.quality-control.edit', $item->id) }}" class="btn btn-sm btn-primary shadow-sm px-3 rounded-3 fw-medium qc-nowrap" style="font-size: 0.85rem;" title="Proses QC">
                <i class="fa-solid fa-clipboard-check me-1"></i> Proses
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-5">
            <div class="d-flex flex-column align-items-center opacity-50">
                <i class="fa-solid fa-clipboard-list fa-3x mb-3 text-muted"></i>
                <h6 class="text-muted">Tidak Ada Item Menunggu QC</h6>
                <p class="small text-muted">Semua item dari transaksi 'Deal' akan muncul di sini.</p>
            </div>
        </td>
    </tr>
@endforelse
