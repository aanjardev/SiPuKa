@extends('layouts.admin')

@section('title', 'Data Cabang')

@push('page-actions')
<a href="{{ route('admin.branches.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Cabang</span>
</a>
@endpush

@section('content')

{{-- Filter dan Pencarian --}}
<form method="GET" action="{{ route('admin.branches.index') }}" id="searchForm">
    <div class="card shadow-sm border-0 mb-4 branch-filter-card" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap branch-filter-body">
            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2 branch-filter-input">
                <span class="text-muted ms-2 me-3 branch-filter-icon">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-0 shadow-none bg-transparent"
                       name="search"
                       placeholder="Cari cabang berdasarkan nama atau alamat..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            {{-- Bagian Kanan: Dropdown Sort --}}
            <div class="d-flex align-items-center gap-2 pe-2 branch-filter-controls">
                <select name="sort_by"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="updated_at" {{ ($sort_by ?? 'updated_at') == 'updated_at' ? 'selected' : '' }}>Urutkan: Terakhir diubah</option>
                    <option value="nama" {{ ($sort_by ?? 'updated_at') == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="nama_desc" {{ ($sort_by ?? 'updated_at') == 'nama_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                </select>
                <input type="hidden" name="sort_order" value="{{ $sort_order ?? 'desc' }}">
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div class="card shadow-sm border-0 branch-table-card" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive branch-table-responsive">
            <table class="table table-modern mb-0 branch-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 20%;">Nama Cabang</th>
                        <th style="width: 25%; min-width: 300px;">Alamat</th>
                        <th style="width: 15%;">Kontak</th>
                        <th style="width: 15%;">Jam Operasional</th>
                        <th class="text-center" style="width: 10%;">Status</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_cabang as $index => $cabang)
                    <tr class="clickable-row" data-detail-url="{{ route('admin.branches.show', $cabang->id) }}">
                        <td class="text-center text-muted fw-bold">{{ ($data_cabang->firstItem() ?? 0) + $index }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-grow-1">
                                    <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                        {{ $cabang->nama }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td>
                            @if ($cabang->link_maps)
                                <a href="{{ $cabang->link_maps }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="text-decoration-none text-muted small d-flex align-items-start gap-2"
                                   title="Lihat di Google Maps"
                                   style="max-width: 300px;">
                                    <i class="fa-solid fa-location-dot text-danger mt-1 flex-shrink-0"></i>
                                    <span style="word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                        {{ $cabang->alamat }}
                                    </span>
                                </a>
                            @else
                                <div class="text-muted small d-flex align-items-start gap-2" style="max-width: 300px;">
                                    <i class="fa-solid fa-location-dot text-secondary mt-1 flex-shrink-0"></i>
                                    <span style="word-wrap: break-word; overflow-wrap: break-word; white-space: normal;">
                                        {{ $cabang->alamat }}
                                    </span>
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="small">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <i class="fa-solid fa-phone text-muted"></i>
                                    <span class="text-nowrap phone-display" data-raw="{{ $cabang->nomor_telepon }}"></span>
                                </div>
                                @if($cabang->email)
                                <div class="d-flex align-items-center gap-1">
                                    <i class="fa-solid fa-envelope text-muted"></i>
                                    <span class="text-muted text-truncate" style="max-width: 150px;" title="{{ $cabang->email }}">
                                        {{ $cabang->email }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </td>

                        <td>
                            @php

                                $hariIni = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'][date('l')] ?? 'Senin';
                                $jamHariIni = $cabang->jamOperasional->where('hari', $hariIni)->first();
                            @endphp
                            @if($jamHariIni && $jamHariIni->is_buka)
                                <div class="d-flex align-items-center gap-1">
                                    <i class="fa-solid fa-clock text-success"></i>
                                    <span class="small text-success fw-medium">
                                        {{ $jamHariIni->jam_buka }} - {{ $jamHariIni->jam_tutup }}
                                    </span>
                                </div>
                                <div class="small text-muted">{{ $hariIni }}</div>
                            @else
                                <div class="d-flex align-items-center gap-1">
                                    <i class="fa-solid fa-clock text-danger"></i>
                                    <span class="small text-danger">Tutup</span>
                                </div>
                                <div class="small text-muted">{{ $hariIni }}</div>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($cabang->is_active)
                                <span class="badge bg-success text-white fw-medium">
                                    <i class="fa-solid fa-circle-check me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge bg-secondary text-white fw-medium">
                                    <i class="fa-solid fa-circle-xmark me-1"></i>Non-Aktif
                                </span>
                            @endif
                        </td>

                        <td class="text-center no-row-navigation">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.branches.edit', $cabang->id) }}"
                                    class="btn-action btn-action-edit"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                @if($cabang->is_active)
                                <form action="{{ route('admin.branches.update-status', $cabang->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="0">
                                    <button type="button"
                                        class="btn-action btn-action-delete"
                                        title="Nonaktifkan Cabang"
                                        data-message="Nonaktifkan cabang ini? Cabang yang non-aktif tidak bisa dipakai untuk transaksi baru.">
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.branches.update-status', $cabang->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_active" value="1">
                                    <button type="submit"
                                        class="btn-action btn-action-edit"
                                        title="Aktifkan Cabang">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-shop fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada data cabang</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($data_cabang->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $data_cabang->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
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

@endpush

@push('styles')
<style>
    .branch-table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 0;
    }
    .branch-table {
        width: 100%;
        min-width: 100%;
    }
    .branch-table th,
    .branch-table td {
        white-space: nowrap;
    }
    .branch-filter-input .form-control {
        min-width: 220px;
    }
    @media (max-width: 1200px) {
        .branch-filter-body {
            gap: 0.5rem;
        }
        .branch-filter-input {
            width: 100%;
            padding-left: 0.5rem !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.3rem 0.75rem;
        }
        .branch-filter-card .branch-filter-input .form-control {
            border: 0 !important;
            background-color: transparent !important;
            padding: 0.45rem 0;
            font-size: 0.85rem;
        }
        .branch-filter-icon {
            margin-left: 0 !important;
        }
        .branch-filter-controls {
            width: 100%;
            padding-right: 0 !important;
            justify-content: space-between;
        }
        .branch-filter-controls .form-select {
            flex: 1 1 0;
            min-width: 0;
        }
        .branch-filter-card .branch-filter-controls .form-select {
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.55rem 0.75rem;
            font-size: 0.85rem;
        }
    }
    @media (max-width: 576px) {
        .branch-filter-body {
            padding: 0.75rem !important;
        }
        .branch-filter-input .form-control {
            font-size: 0.85rem;
        }
        .branch-filter-controls {
            flex-direction: column;
            align-items: stretch !important;
        }
        .branch-filter-controls .form-select {
            width: 100%;
        }
        .branch-table-card {
            border-radius: 12px;
        }
        .branch-table-responsive {
            padding: 0 0.75rem;
        }
        .branch-table {
            min-width: 900px;
        }
        .branch-table th,
        .branch-table td {
            white-space: normal;
        }
    }
</style>
@endpush
