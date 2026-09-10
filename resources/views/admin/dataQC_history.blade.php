@extends('layouts.admin')

@section('title', 'Riwayat QC (Lolos)')

@push('page-actions')
    <a href="{{ route('admin.quality-control.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left fa-fw"></i>
        <span>Kembali ke QC</span>
    </a>
@endpush

@push('styles')
<style>
    .qc-history-table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 0;
    }
    .qc-history-table {
        width: 100%;
        min-width: 100%;
    }
    .qc-history-table th,
    .qc-history-table td {
        white-space: nowrap;
    }
    .qc-history-filter-input .form-control {
        min-width: 220px;
    }
    @media (max-width: 1200px) {
        .qc-history-filter-body {
            gap: 0.5rem;
        }
        .qc-history-filter-input {
            width: 100%;
            padding-left: 0.5rem !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background: #fff !important;
            padding: 0.3rem 0.75rem;
        }
        .qc-history-filter-card .qc-history-filter-input .form-control {
            border: 0 !important;
            background: transparent !important;
            padding: 0.45rem 0;
            font-size: 0.85rem;
        }
        .qc-history-filter-icon {
            margin-left: 0 !important;
        }
        .qc-history-filter-controls {
            width: 100%;
            padding-right: 0 !important;
            justify-content: space-between;
        }
        .qc-history-filter-controls .form-select {
            flex: 1 1 0;
            min-width: 0;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23495057' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            padding-right: 2.25rem;
        }
        .qc-history-filter-card .qc-history-filter-controls .form-select {
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
        .qc-history-filter-body {
            padding: 0.75rem !important;
        }
        .qc-history-filter-input .form-control {
            font-size: 0.85rem;
        }
        .qc-history-filter-controls {
            flex-direction: column;
            align-items: stretch !important;
        }
        .qc-history-table-responsive {
            padding: 0 0.75rem;
        }
        .qc-history-table {
            min-width: 900px;
        }
    }
</style>
@endpush

@section('content')

<form action="{{ route('admin.quality-control.history') }}" method="GET" id="filterFormQcHistory">
    <div class="card shadow-sm border-0 mb-4 qc-history-filter-card" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap qc-history-filter-body">
            <div class="d-flex align-items-center flex-grow-1 ps-2 qc-history-filter-input">
                <span class="text-muted ms-2 me-3 qc-history-filter-icon">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       name="search"
                       class="form-control border-0 shadow-none bg-transparent"
                       placeholder="Cari nama item, serial number atau kode pembelian..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            <div class="d-flex align-items-center gap-2 pe-2 qc-history-filter-controls">
                <select name="kategori"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('filterFormQcHistory').submit();">
                    <option value="" selected>Semua Kategori</option>
                    @foreach ($semua_kategori as $kat)
                        <option value="{{ $kat->id }}" {{ ($kategori_filter ?? '') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                <select name="sort"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('filterFormQcHistory').submit();">
                    <option value="terbaru" {{ ($sort_filter ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="terlama" {{ ($sort_filter ?? '') == 'terlama' ? 'selected' : '' }}>Terlama</option>
                </select>
            </div>
        </div>
    </div>
</form>

<div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive qc-history-table-responsive">
            <table class="table table-modern mb-0 qc-history-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 150px;">Kode</th>
                        <th style="width: 25%; max-width:200px; min-width: 150px;">Nama Item</th>
                        <th>Serial Number</th>
                        <th>SN Lensa</th>
                        <th>Kategori</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data_qc as $index => $item)
                    <tr class="clickable-row" data-detail-url="{{ route('admin.quality-control.edit', $item->id) }}?readonly=1">
                        <td class="text-center text-muted fw-bold">{{ ($data_qc->firstItem() ?? 0) + $index }}</td>

                        <td>
                            @if($item->pembelian)
                                <a href="{{ route('admin.purchases.show', $item->pembelian->id) }}"
                                   class="fw-bold text-primary font-monospace bg-primary bg-opacity-10 px-2 py-1 rounded small text-decoration-none clickable-code"
                                   onclick="event.stopPropagation();">
                                    {{ $item->pembelian->kode_transaksi ?? ('#' . $item->pembelian->id) }}
                                </a>
                            @else
                                <span class="fw-bold text-secondary font-monospace bg-light px-2 py-1 rounded small">QC Manual</span>
                            @endif
                        </td>

                        <td class="col-text-wrap" style="max-width: 300px; min-width: 250px;">
                            <span class="d-block text-wrap text-break" style="font-size: 0.95rem;">
                                {{ $item->nama_item }}
                            </span>
                        </td>

                        <td class="text-secondary font-monospace small">
                            {{ $item->serial_number ?? '-' }}
                        </td>

                        <td class="text-secondary font-monospace small">
                            {{ $item->serial_lens ?? '-' }}
                        </td>

                        <td class="text-dark">
                            {{ $item->kategori->nama_kategori ?? '-' }}
                        </td>

                        <td class="text-center no-row-navigation">
                            <a href="{{ route('admin.quality-control.edit', $item->id) }}?readonly=1"
                               class="btn btn-sm btn-outline-primary shadow-sm px-3 rounded-3 fw-medium"
                               style="font-size: 0.85rem;"
                               title="Lihat Detail QC">
                                <i class="fa-solid fa-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-clipboard-check fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum Ada Riwayat QC Lolos</h6>
                                <p class="small text-muted">Item yang lolos QC akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_qc->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $data_qc->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
