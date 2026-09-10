@extends('layouts.admin')

@section('title', 'Quality Control (QC)')

@push('page-actions')
    {{-- Tombol Arsip (Fitur dari Main) --}}
    <a href="{{ url('admin/quality-control/archived') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-archive fa-fw"></i>
        <span>Arsip Produk</span>
    </a>
    <a href="{{ route('admin.quality-control.history') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-clock-rotate-left fa-fw"></i>
        <span>Riwayat QC</span>
    </a>
    <a href="{{ route('admin.quality-control.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-plus fa-fw"></i>
        <span>Tambah QC</span>
    </a>
@endpush

@push('styles')
<style>
    .clickable-code {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .clickable-code:hover {
        opacity: 0.8;
        text-decoration: underline !important;
    }

    @media (min-width: 1900px) {
        .qc-table-responsive {
            overflow-x: visible;
        }
        .qc-table-fixed {
            table-layout: fixed;
        }
        .qc-table-fixed th,
        .qc-table-fixed td {
            white-space: normal;
            word-break: break-word;
        }
    }
    .qc-table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 0;
    }
    .qc-table-fixed {
        width: 100%;
        min-width: 100%;
    }
    .qc-table-fixed th,
    .qc-table-fixed td {
        white-space: nowrap;
    }
    .qc-table-fixed td.col-text-wrap {
        white-space: normal !important;
        word-break: break-word;
    }
    .qc-code-chip {
        display: inline-block;
        max-width: 100%;
        white-space: nowrap;
    }
    .qc-nowrap {
        white-space: nowrap;
    }
    .qc-filter-input .form-control {
        min-width: 220px;
    }
    @media (max-width: 1200px) {
        .qc-filter-body {
            gap: 0.5rem;
        }
        .qc-filter-input {
            width: 100%;
            padding-left: 0.5rem !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background: #fff !important;
            padding: 0.3rem 0.75rem;
        }
        .qc-filter-card .qc-filter-input .form-control {
            border: 0 !important;
            background: transparent !important;
            padding: 0.45rem 0;
            font-size: 0.85rem;
        }
        .qc-filter-icon {
            margin-left: 0 !important;
        }
        .qc-filter-controls {
            width: 100%;
            padding-right: 0 !important;
            justify-content: space-between;
        }
        .qc-filter-controls .form-select {
            flex: 1 1 0;
            min-width: 0;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23495057' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            padding-right: 2.25rem;
        }
        .qc-filter-card .qc-filter-controls .form-select {
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.55rem 0.75rem;
            font-size: 0.85rem;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23495057' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            padding-right: 2.25rem;
        }
    }
    @media (max-width: 576px) {
        .qc-filter-body {
            padding: 0.75rem !important;
        }
        .qc-filter-input .form-control {
            font-size: 0.85rem;
        }
        .qc-filter-controls {
            flex-direction: column;
            align-items: stretch !important;
        }
        .qc-table-responsive {
            padding: 0 0.75rem;
        }
        .qc-table-fixed {
            min-width: 900px;
        }
        .qc-table-fixed th,
        .qc-table-fixed td {
            white-space: nowrap;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('qc-list-container');
    const tableBody = document.getElementById('qc-table-body');
    const paginationContainer = document.getElementById('qc-pagination-links-container');
    const form = document.getElementById('filterFormQc');
    const searchInput = form.querySelector('input[name="search"]');
    const kategoriFilter = form.querySelector('select[name="kategori"]');
    const sortFilter = form.querySelector('select[name="sort"]');

    const urlIndex = '{{ route('admin.quality-control.index') }}';

    let isFetching = false;
    let searchTimeout;

    async function fetchQc(url) {
        if (!url) return;
        isFetching = true;
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json, text/html' } });
            if (!res.ok) throw new Error('Network response was not ok. Status: ' + res.status);

            const ct = res.headers.get('content-type') || '';
            if (ct.indexOf('application/json') !== -1) {
                const data = await res.json();
                if (data.table_html !== undefined) {
                    tableBody.innerHTML = data.table_html;
                } else {
                    tableBody.innerHTML = data;
                }
                if (data.pagination_html !== undefined && paginationContainer) {
                    paginationContainer.innerHTML = data.pagination_html;
                }
            } else {
                const text = await res.text();
                tableBody.innerHTML = text;
            }

            attachPaginationHandler();

        } catch (err) {
            console.error('Gagal memuat data:', err);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center p-5 text-muted">
                            <i class="fa-solid fa-triangle-exclamation fa-2x mb-3"></i>
                            <h5 class="mb-1">Gagal memuat data</h5>
                            <p class="mb-0">Silakan coba lagi.</p>
                        </div>
                    </td>
                </tr>`;
        } finally {
            isFetching = false;
        }
    }

    function buildUrl(overrideUrl) {
        if (overrideUrl) return overrideUrl;
        const params = new URLSearchParams();
        if (searchInput && searchInput.value.trim() !== '') params.set('search', searchInput.value.trim());
        if (kategoriFilter && kategoriFilter.value !== '') params.set('kategori', kategoriFilter.value);
        if (sortFilter && sortFilter.value !== '') params.set('sort', sortFilter.value);
        const qs = params.toString();
        return urlIndex + (qs ? ('?' + qs) : '');
    }

    function attachPaginationHandler() {
        if (!paginationContainer) return;
        paginationContainer.querySelectorAll('a').forEach(a => {
            a.removeEventListener && a.removeEventListener('click', handlePaginationClick);
            a.addEventListener('click', handlePaginationClick);
        });
    }

    function handlePaginationClick(e) {
        const href = this.getAttribute('href');
        if (!href) return;
        e.preventDefault();
        fetchQc(href);
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchQc(buildUrl());
            }, 450);
        });
    }

    if (kategoriFilter) {
        kategoriFilter.addEventListener('change', function() { fetchQc(buildUrl()); });
    }
    if (sortFilter) {
        sortFilter.addEventListener('change', function() { fetchQc(buildUrl()); });
    }

    attachPaginationHandler();
});
</script>
@endpush

@section('content')

{{-- Filter dan Pencarian (Visual: HEAD, Logic: Main) --}}
<form action="{{ route('admin.quality-control.index') }}" method="GET" id="filterFormQc">
    <div class="card shadow-sm border-0 mb-4 qc-filter-card" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap qc-filter-body">

            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2 qc-filter-input">
                <span class="text-muted ms-2 me-3 qc-filter-icon">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       name="search"
                       id="search-input-qc"
                       class="form-control border-0 shadow-none bg-transparent"
                       placeholder="Cari nama item, serial number atau kode pembelian..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            {{-- Bagian Kanan: Dropdown --}}
            <div class="d-flex align-items-center gap-2 pe-2 qc-filter-controls">

                {{-- Filter Kategori --}}
                <select name="kategori" id="filter-kategori"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;">
                    <option value="" selected>Semua Kategori</option>
                    @foreach ($semua_kategori as $kat)
                        <option value="{{ $kat->id }}" {{ ($kategori_filter ?? '') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                {{-- Filter Sort --}}
                <select name="sort" id="filter-sort-qc"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;">
                    <option value="terbaru" {{ ($sort_filter ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ ($sort_filter ?? '') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                    {{-- Opsi tambahan dari HEAD jika logic backend mendukung --}}
                    {{-- <option>Progress Tertinggi</option> --}}
                </select>
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div id="qc-list-container">
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
        <div class="card-body p-0">
            <div class="table-responsive qc-table-responsive">
                <table class="table table-modern mb-0 qc-table-fixed">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th style="width: 150px;">Kode</th>
                            <th style="width: 25%; max-width:200px; min-width: 150px;">Nama Item</th>
                            <th>Serial Number</th>
                            <th>SN Lensa</th>
                            <th>Kategori</th>
                            <th style="width: 15%">Kelengkapan QC</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    {{-- ID qc-table-body PENTING untuk script AJAX --}}
                    <tbody id="qc-table-body">
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
                            <td class="col-text-wrap" style="max-width: 300px; min-width: 250px;">
                                    <span class="d-block text-wrap text-break" style="font-size: 0.95rem;">
                                    {{ $item->nama_item }}
                                </span>
                            </td>

                            {{-- Serial Number --}}
                            <td class="text-secondary font-monospace small">
                                {{ $item->serial_number ?? '-' }}
                            </td>

                            {{-- SN Lensa --}}
                            <td class="text-secondary font-monospace small">
                                {{ $item->serial_lens ?? '-' }}
                            </td>

                            {{-- Kategori --}}
                            <td class="text-dark">
                                {{ $item->kategori->nama_kategori ?? '-' }}
                            </td>

                            {{-- Progress QC --}}
                            <td>
                                @php $persen = round($item->persentase_lengkap); @endphp
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1 shadow-sm" style="height: 6px; border-radius: 10px; background-color: #f0f2f5;">
                                        <div class="progress-bar {{ $persen == 100 ? 'bg-success' : 'bg-primary' }}"
                                             role="progressbar"
                                             style="width: {{ $persen }}%; border-radius: 10px;"
                                             aria-valuenow="{{ $persen }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="small fw-bold text-muted" style="font-size: 0.8rem; min-width: 35px; text-align: right;">
                                        {{ $persen }}%
                                    </span>
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center no-row-navigation">
                                <a href="{{ route('admin.quality-control.edit', $item->id) }}"
                                   class="btn btn-sm btn-primary shadow-sm px-3 rounded-3 fw-medium qc-nowrap"
                                   style="font-size: 0.85rem;"
                                   title="Proses QC">
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
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Container ID Penting --}}
        <div id="qc-pagination-links-container">
            @if ($data_qc->hasPages())
            <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
                {{ $data_qc->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('input[name="search"]');
    if (input) {
        input.focus();
        const length = input.value.length;
        input.setSelectionRange(length, length); // kursor ke akhir
    }
});
</script>
