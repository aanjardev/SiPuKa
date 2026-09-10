@extends('layouts.admin')

@section('title', 'Manajemen Hak Akses')

@push('page-actions')
<a href="{{ route('admin.permissions.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah User</span>
</a>
@endpush

@push('styles')
<style>
.permission-table-responsive {
    overflow-x: auto;
    overflow-y: hidden;
    -webkit-overflow-scrolling: touch;
    padding: 0;
}
/* Biarkan scroll aktif di layar <1400px agar kolom tidak saling tumpuk */
@media (min-width: 1400px) {
    .permission-table-responsive {
        overflow-x: visible;
    }
}
.permission-table-fixed {
    width: 100%;
    min-width: 100%;
    table-layout: fixed;
}
.permission-table-fixed th,
.permission-table-fixed td {
    white-space: nowrap;
}

/* Aktifkan lebar minimum & scroll di layar <1400px supaya kolom tidak dipaksa menyempit */
@media (max-width: 1399.98px) {
    .permission-table-fixed {
        min-width: 1100px;
        table-layout: auto;
    }
}

.permission-filter-input .form-control {
    min-width: 220px;
}
.permission-filter-card .permission-filter-controls .form-select {
    background-image: var(--bs-form-select-bg-img);
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    padding-right: 2.5rem;
}
@media (max-width: 1200px) {
    .permission-filter-body {
        gap: 0.5rem;
    }
    .permission-filter-input {
        width: 100%;
        padding-left: 0.5rem !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 10px;
        background: #fff !important;
        padding: 0.3rem 0.75rem;
    }
    .permission-filter-card .permission-filter-input .form-control {
        border: 0 !important;
        background: transparent !important;
        padding: 0.45rem 0;
        font-size: 0.9rem;
    }
    .permission-filter-icon {
        margin-left: 0 !important;
    }
    .permission-filter-controls {
        width: 100%;
        padding-right: 0 !important;
        justify-content: space-between;
    }
    .permission-filter-controls .form-select {
        flex: 1 1 0;
        min-width: 0;
    }
    .permission-filter-card .permission-filter-controls .form-select {
        border: 1px solid #dee2e6 !important;
        border-radius: 10px;
        background: #fff !important;
        padding: 0.55rem 0.75rem;
        font-size: 0.9rem;
        background-image: var(--bs-form-select-bg-img) !important;
        background-repeat: no-repeat !important;
        background-position: right 0.75rem center !important;
        background-size: 16px 12px !important;
        padding-right: 2.5rem !important;
    }
}
@media (max-width: 576px) {
    .permission-filter-body {
        padding: 0.75rem !important;
    }
    .permission-filter-input .form-control {
        font-size: 0.9rem;
    }
    .permission-filter-controls {
        flex-direction: column;
        align-items: stretch !important;
    }
    .permission-table-responsive {
        padding: 0 0.75rem;
    }
    .permission-table-fixed {
        min-width: 1000px;
        table-layout: auto;
    }
    .permission-table-fixed th,
    .permission-table-fixed td {
        white-space: nowrap;
    }
    .info-token-card {
        font-size: 0.9rem;
    }
    .info-token-title {
        font-size: 0.95rem;
    }
    .info-token-list {
        font-size: 0.8rem !important;
    }
}
</style>
@endpush

@section('content')

{{-- Edukasi Token Expiry --}}
<div class="alert alert-info mb-4 info-token-card" style="border-radius: 10px;">
    <div class="d-flex align-items-start">
        <i class="fa-solid fa-info-circle me-3 mt-1"></i>
        <div>
            <strong class="info-token-title">Informasi Token Aktivasi</strong><br>
            <div class="row mt-2">
                <div class="col-md-12">
                    <ul class="mb-0 ps-3 info-token-list" style="font-size: 0.85rem;">
                        <li>Token aktivasi berlaku <strong>3 hari (72 jam)</strong></li>
                        <li>User dengan status <span class="badge bg-warning">Pending</span> perlu segera aktivasi</li>
                        <li>Jika token kadaluarsa, gunakan tombol 🔄 untuk generate ulang</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter dan Pencarian --}}
<form method="GET" action="{{ route('admin.permissions') }}" id="searchForm">
    <div class="card shadow-sm border-0 mb-4 permission-filter-card" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap permission-filter-body">
            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2 permission-filter-input">
                <span class="text-muted ms-2 me-3 permission-filter-icon">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-0 shadow-none bg-transparent"
                       name="search"
                       placeholder="Cari user berdasarkan nama atau email..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            {{-- Bagian Kanan: Dropdown --}}
            <div class="d-flex align-items-center gap-2 pe-2 permission-filter-controls">
                <select name="role"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="all" {{ ($selected_role ?? 'all') == 'all' ? 'selected' : '' }}>Semua Jabatan</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ ($selected_role ?? 'all') == $role ? 'selected' : '' }}>
                            {{ ucfirst($role) }}
                        </option>
                    @endforeach
                </select>

                <select name="status_filter"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="all" {{ ($selected_status ?? 'all') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="active" {{ ($selected_status ?? 'all') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="pending" {{ ($selected_status ?? 'all') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="inactive" {{ ($selected_status ?? 'all') == 'inactive' ? 'selected' : '' }}>Non Aktif</option>
                </select>
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div class="card shadow-sm border-0" style="border-radius: 10px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive permission-table-responsive">
            <table class="table table-modern mb-0 permission-table-fixed">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 25%;">Nama Lengkap</th>
                        <th style="width: 20%;">Email</th>
                        <th>Jenis Akses</th>
                        <th class="text-center">Status User</th>
                        <th class="text-center">Status Karyawan</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($user_data as $index => $k)
                    <tr class="clickable-row" data-detail-url="{{ route('admin.permissions.edit', $k->id) }}">
                        <td class="text-center text-muted fw-bold">{{ ($user_data->firstItem() ?? 0) + $index }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                {{-- Avatar Placeholder --}}
                                <div class="flex-shrink-0">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center text-secondary"
                                        style="width: 40px; height: 40px;">
                                        <i class="fa-solid fa-user-shield"></i>
                                    </div>
                                </div>

                                <div class="flex-grow-1">
                                    <span class="text-dark fw-semibold d-block" style="font-size: 0.95rem;">
                                        {{ $k->name }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="fw-medium text-secondary">
                            {{ $k->email }}
                        </td>

                        <td>
                            <span class="fw-medium text-dark">{{ $k->role }}</span>
                        </td>

                        <td class="text-center">
                            @if($k->status == 'active')
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-check-circle me-1"></i>Aktif
                                </span>
                            @elseif($k->status == 'pending')
                                <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-clock me-1"></i>Pending
                                </span>
                            @elseif($k->status == 'inactive')
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-times-circle me-1"></i>Non Aktif
                                </span>
                            @else
                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-question-circle me-1"></i>Unknown
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($k->karyawan_status == 'aktif')
                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-user-check me-1"></i>Aktif
                                </span>
                            @elseif($k->karyawan_status == 'non-aktif')
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-user-times me-1"></i>Non Aktif
                                </span>
                            @else
                                <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.85rem;">
                                    <i class="fa-solid fa-question-circle me-1"></i>Unknown
                                </span>
                            @endif
                        </td>

                        <td class="text-center no-row-navigation">
                            @php
                                $isSelf = Auth::check() && (int) Auth::id() === (int) $k->id;
                            @endphp
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.permissions.edit', $k->id) }}"
                                    class="btn-action btn-action-edit"
                                    title="Edit"
                                    onclick="event.stopPropagation();">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                {{-- Regenerate Token Button - Only for pending users --}}
                                @if($k->status == 'pending')
                                <form action="{{ route('admin.permissions.regenerate-token', $k->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="button"
                                        class="btn-action btn-action-warning"
                                        title="Generate Ulang Token (3 hari)"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        onclick="event.stopPropagation(); handleRegenerateToken(this)">
                                        <i class="fa-solid fa-rotate"></i>
                                    </button>
                                </form>
                                @endif

                                @if(!$isSelf)
                                <form action="{{ route('admin.permissions.update-status', $k->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $k->status === 'inactive' ? 'active' : 'inactive' }}">
                                    @if($k->status === 'inactive')
                                    <button type="submit"
                                        class="btn-action btn-action-edit"
                                        title="Aktifkan User"
                                        onclick="event.stopPropagation();">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                    @else
                                    <button type="button"
                                        class="btn-action btn-action-delete"
                                        title="Nonaktifkan User"
                                        data-message="Nonaktifkan user ini? User non-aktif tidak dapat login."
                                        data-title="Nonaktifkan User"
                                        data-confirm-text="Nonaktifkan"
                                        data-cancel-text="Batal"
                                        onclick="event.stopPropagation(); handleDeletePermission(this)">
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>
                                    @endif
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-user-slash fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Tidak ada user yang ditemukan</h6>
                                @if($search_term || ($selected_role ?? 'all') != 'all' || ($selected_status ?? 'all') != 'all')
                                    <a href="{{ route('admin.permissions') }}" class="btn btn-sm btn-outline-secondary mt-2">
                                        Reset Filter
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if (method_exists($user_data, 'hasPages') && $user_data->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $user_data->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.querySelector('input[name="search"]');
        if (input) {
            input.focus();
            const length = input.value.length;
            input.setSelectionRange(length, length); // kursor ke akhir
        }
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });

    function handleDeletePermission(button) {
        if (typeof window.confirmDelete === 'function') {
            const message = button.dataset.message || 'Lanjutkan tindakan ini?';
            const title = button.dataset.title || 'Konfirmasi Tindakan';
            const confirmText = button.dataset.confirmText || 'Lanjutkan';
            const cancelText = button.dataset.cancelText || 'Batal';
            window.confirmDelete(message, title, confirmText, cancelText)
                .then((result) => {
                    if (result.isConfirmed) {
                        button.form.submit();
                    }
                });
        } else {
            if (confirm('Yakin mau hapus data ini?')) {
                button.form.submit();
            }
        }
    }

    function handleRegenerateToken(button) {
        if (typeof window.confirmRegenerateToken === 'function') {
            window.confirmRegenerateToken('Generate ulang token aktivasi? Token lama akan tidak berlaku.')
                .then((result) => {
                    if (result.isConfirmed) {
                        button.form.submit();
                    }
                });
        } else {
            if (confirm('Generate ulang token aktivasi? Token lama akan tidak berlaku.')) {
                button.form.submit();
            }
        }
    }

    window.handleDeletePermission = handleDeletePermission;
    window.handleRegenerateToken = handleRegenerateToken;
</script>
@endpush
