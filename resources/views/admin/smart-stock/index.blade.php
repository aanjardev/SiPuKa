@extends('layouts.admin')

@section('title', 'Smart Stock Analysis')

@push('page-actions')
<button onclick="refreshAnalysis()" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-sync-alt fa-fw"></i>
    <span>Refresh Analysis</span>
</button>
@endpush

@push('styles')
<style>
    
    .smart-inline-filters {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 16px;
    }
    .smart-inline-filters .filter-field {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
        flex: 0 0 200px;
    }
    .smart-inline-filters label {
        margin: 0;
        font-weight: 600;
        color: #475569;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .smart-inline-filters .form-select,
    .smart-inline-filters .form-control {
        padding: 6px 10px;
        border: 1px solid #e5e7eb;
        box-shadow: inset 0 1px 1px rgba(15,23,42,0.04);
        min-width: 0;
        width: 100%;
    }
    .smart-inline-filters #sort {
        min-width: 230px;
    }

    .form-control {
        min-width: 70px !important;
    }
    .smart-inline-filters .input-group-text {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        font-size: 0.85rem;
        color: #475569;
        padding-inline: 8px;
    }
    .smart-inline-filters .threshold-group {
        width: 100%;
        display: flex;
        align-items: center;
    }
    .smart-inline-filters .threshold-group .form-control {
        max-width: 110px;
        flex: 0 0 110px;
        padding-right: 10px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    .smart-inline-filters .threshold-group .input-group-text {
        min-width: 50px;
        border: 1px solid #e5e7eb;
        border-left: 0;
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        background: #f8fafc;
        padding: 0.45rem 0.65rem;
        color: #475569;
        font-weight: 600;
        pointer-events: none;
    }
    .smart-inline-filters .form-select:focus,
    .smart-inline-filters .form-control:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.14);
    }
    .smart-inline-filters small {
        color: #64748b;
    }

    .smart-table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding: 0;
    }
    .smart-table-responsive table {
        width: 100%;
        min-width: 100%;
    }
    .smart-table-responsive th,
    .smart-table-responsive td {
        white-space: nowrap;
    }

    @media (max-width: 992px) {
        .smart-inline-filters {
            width: 100%;
            gap: 12px;
        }
        .smart-inline-filters .filter-field {
            width: 100%;
            flex: 1 1 100%;
        }
        .smart-inline-filters .form-select,
        .smart-inline-filters .form-control {
            width: 100%;
            min-width: 0;
        }
        .smart-inline-filters label {
            font-size: 0.72rem;
        }
    }

    @media (max-width: 576px) {
        .smart-inline-filters {
            gap: 10px;
        }
        .smart-table-responsive {
            padding: 0 0.75rem;
        }
        .smart-table-responsive table {
            min-width: 1100px;
            table-layout: auto;
        }
        .smart-table-responsive th,
        .smart-table-responsive td {
            white-space: nowrap;
        }
        .smart-inline-filters .form-select,
        .smart-inline-filters .form-control {
            padding: 0.5rem 0.65rem;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@section('content')

{{-- Statistics Cards --}}
<div class="row g-3 g-lg-4 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Total Produk</p>
                        <h4 class="mb-0 fw-bold">{{ number_format($stats['total_products'] ?? 0) }}</h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-box text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Kritis (≤1 hari)</p>
                        <h4 class="mb-0 fw-bold text-danger">{{ number_format($stats['critical'] ?? 0) }}</h4>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Peringatan (≤{{ $threshold }} hari)</p>
                        <h4 class="mb-0 fw-bold text-warning">{{ number_format($stats['warning'] ?? 0) }}</h4>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-exclamation-circle text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small">Aman</p>
                        <h4 class="mb-0 fw-bold text-success">{{ number_format($stats['safe'] ?? 0) }}</h4>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
{{-- Analysis Table --}}
<div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
            <div>
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Analisis Prediksi Stok
                </h5>
                <p class="text-muted small mb-0 mt-1">
                    Prediksi berdasarkan rata-rata penjualan 30 hari terakhir
                </p>
            </div>
            <form method="GET" action="{{ route('admin.smart-stock.index') }}" id="filterForm" class="smart-inline-filters">
                <div class="filter-field">
                    <label for="filter">Filter</label>
                    <select name="filter" id="filter" class="form-select form-select-sm"
                            style="cursor: pointer;" onchange="submitFilterForm()">
                        <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="critical" {{ $filter == 'critical' ? 'selected' : '' }}>Kritis</option>
                        <option value="warning" {{ $filter == 'warning' ? 'selected' : '' }}>Peringatan</option>
                        <option value="safe" {{ $filter == 'safe' ? 'selected' : '' }}>Aman</option>
                    </select>
                </div>
                <div class="filter-field">
                    <label for="sort">Urutkan</label>
                    <select name="sort" id="sort" class="form-select form-select-sm"
                            style="cursor: pointer;" onchange="submitFilterForm()">
                        <option value="days_left" {{ $sortBy == 'days_left' ? 'selected' : '' }}>Hari Tersisa (Terendah)</option>
                        <option value="stock" {{ $sortBy == 'stock' ? 'selected' : '' }}>Stok (Tertinggi)</option>
                        <option value="name" {{ $sortBy == 'name' ? 'selected' : '' }}>Nama Produk (A-Z)</option>
                    </select>
                </div>
                <div class="filter-field">
                    <label for="threshold">Threshold</label>
                    <div class="input-group input-group-sm threshold-group">
                        <input type="number" name="threshold" id="threshold" value="{{ $threshold }}" min="1" max="30" step="1"
                               class="form-control form-control-sm"
                               inputmode="numeric"
                               onchange="submitFilterForm()">
                        <span class="input-group-text">hari</span>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive smart-table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 25%;">Nama Produk</th>
                        <th style="width: 10%;">SKU</th>
                        <th class="text-center" style="width: 10%;">Stok Saat Ini</th>
                        <th class="text-center" style="width: 12%;">Rata-rata Penjualan/Hari</th>
                        <th class="text-center" style="width: 12%;">Prediksi Hari Tersisa</th>
                        <th class="text-center" style="width: 10%;">Status</th>
                        <th class="text-center" style="width: 10%;">Harga Jual</th>
                        <th class="text-center" style="width: 6%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($analysisData as $index => $item)
                    <tr class="{{ $item['status'] == 'critical' ? 'table-danger' : ($item['status'] == 'warning' ? 'table-warning' : '') }}">
                        <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                        <td>
                            <span class="text-dark fw-semibold" style="font-size: 0.95rem;">
                                {{ $item['product_name'] }}
                            </span>
                        </td>
                        <td>
                            <span class="text-secondary font-monospace small">
                                {{ $item['sku'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ number_format($item['current_stock']) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="text-dark fw-medium">
                                {{ number_format($item['average_daily_usage'], 2) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($item['predicted_days_left'] >= 999)
                                <span class="badge bg-info">Tidak Terbatas</span>
                            @else
                                <span class="badge {{ $item['status'] == 'critical' ? 'bg-danger' : ($item['status'] == 'warning' ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ $item['predicted_days_left'] }} hari
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item['status'] == 'critical')
                                <span class="badge bg-danger">
                                    <i class="fas fa-exclamation-triangle"></i> Kritis
                                </span>
                            @elseif($item['status'] == 'warning')
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-exclamation-circle"></i> Peringatan
                                </span>
                            @elseif($item['status'] == 'infinite')
                                <span class="badge bg-info">
                                    <i class="fas fa-infinity"></i> Tidak Terbatas
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Aman
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="text-dark fw-medium">
                                Rp {{ number_format($item['harga_jual'], 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.products.edit', $item['product_id']) }}"
                               class="btn btn-sm btn-outline-primary"
                               title="Edit Produk">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                <p class="mb-0">Tidak ada data untuk ditampilkan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Notifications Section --}}
<div class="card shadow-sm border-0 mt-4" style="border-radius: 15px; overflow: hidden;">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 fw-bold">
            <i class="fas fa-bell text-primary me-2"></i>
            Notifikasi Stok Rendah
        </h5>
    </div>
    <div class="card-body">
        <div id="notifications-container">
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function refreshAnalysis() {
    window.location.reload();
}

function submitFilterForm() {
    const form = document.getElementById('filterForm');
    if (!form) return;

    const thresholdInput = document.getElementById('threshold');
    if (thresholdInput) clampThreshold(thresholdInput);

    if (typeof form.requestSubmit === 'function') form.requestSubmit();
    else form.submit();
}

function clampThreshold(inputEl) {
    if (!inputEl) return;

    const raw = (inputEl.value ?? '').toString().trim();
    if (raw === '') return;

    const parsed = Number(raw);
    if (!Number.isFinite(parsed)) {
        inputEl.value = '';
        return;
    }

    const value = Math.trunc(parsed);
    inputEl.value = Math.max(1, Math.min(30, value));
}

function loadNotifications() {
    fetch('{{ route("admin.smart-stock.notifications") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('notifications-container');

        if (data.success && data.data.length > 0) {
            let html = '<div class="list-group">';
            data.data.forEach(notification => {
                const statusClass = notification.predicted_days_left <= 1 ? 'danger' : 'warning';
                html += `
                    <div class="list-group-item list-group-item-${statusClass} d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">${notification.product_name}</div>
                            <small>${notification.message}</small>
                            <div class="mt-1">
                                <span class="badge bg-${statusClass}">Stok: ${notification.current_stock}</span>
                                <span class="badge bg-${statusClass}">Tersisa: ${notification.predicted_days_left} hari</span>
                            </div>
                        </div>
                        <div>
                            <small class="text-muted">${notification.created_at}</small>
                            <button class="btn btn-sm btn-outline-secondary ms-2"
                                    onclick="markAsRead('${notification.id}')"
                                    title="Tandai sudah dibaca">
                                <i class="fas fa-check"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        } else {
            container.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                    <p class="mb-0">Tidak ada notifikasi stok rendah</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading notifications:', error);
        document.getElementById('notifications-container').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Gagal memuat notifikasi
            </div>
        `;
    });
}

function markAsRead(notificationId) {
    fetch(`{{ route('admin.smart-stock.notifications.read', ':id') }}`.replace(':id', notificationId), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications(); // Reload notifications
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();

    const thresholdInput = document.getElementById('threshold');
    if (thresholdInput) {
        clampThreshold(thresholdInput);
        thresholdInput.addEventListener('input', () => clampThreshold(thresholdInput));

        const form = document.getElementById('filterForm');
        if (form) {
            form.addEventListener('submit', () => clampThreshold(thresholdInput));
        }
    }

    setInterval(loadNotifications, 30000);
});
</script>
@endpush
