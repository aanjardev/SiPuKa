@extends('layouts.admin')

@section('title', 'Dashboard')

@push('page-actions')
    <a href="{{ route('admin.sales.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2 px-3">
        <i class="fas fa-plus fa-fw"></i>
        <span>Penjualan</span>
    </a>
    @if(Route::has('admin.purchases.create'))
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-success btn-sm d-flex align-items-center gap-2 px-3">
            <i class="fas fa-plus fa-fw"></i>
            <span>Pembelian</span>
        </a>
    @else
        <button class="btn btn-success btn-sm d-flex align-items-center gap-2 px-3" disabled>
            <i class="fas fa-plus fa-fw"></i>
            <span>Pembelian</span>
        </button>
    @endif
@endpush

@push('styles')
    @vite('resources/css/admin/pages/dashboard.css')
@endpush

@section('content')

{{-- ======================================================= --}}
{{-- FILTER SECTION --}}
{{-- ======================================================= --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-0 rounded-xl">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('admin.dashboard') }}" id="filterForm" class="row g-3 align-items-center">
                    <div class="col-md-3">
                        <label for="year" class="form-label mb-1 text-muted small fw-medium">Tahun</label>
                        <select name="year" id="year" class="form-select form-select-sm ">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="month" class="form-label mb-1 text-muted small fw-medium">Bulan</label>
                        <select name="month" id="month" class="form-select form-select-sm ">
                            <option value="">Semua Bulan</option>
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $bulan)
                                <option value="{{ $index + 1 }}" {{ $selectedMonth == ($index + 1) ? 'selected' : '' }}>{{ $bulan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="branch_id" class="form-label mb-1 text-muted small fw-medium">Cabang</label>
                        <select name="branch_id" id="branch_id" class="form-select form-select-sm ">
                            <option value="">Semua Cabang</option>
                            @foreach($allBranches as $branchOption)
                            <option value="{{ $branchOption->id }}" {{ (int)$selectedBranch === (int)$branchOption->id ? 'selected' : '' }}>
                                    {{ $branchOption->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- SUMMARY CARDS: Flexible Layout --}}
{{-- ======================================================= --}}
<div class="row mb-3">
    @php
        $cardCount = $showBestBranch ? 4 : 3;
        $colClass = $cardCount == 4 ? 'col-xl-3 col-md-6' : 'col-xl-4 col-md-4';
    @endphp

    {{-- Card 1: Total Pendapatan --}}
    <div class="{{ $colClass }} mb-1 carddashboard">
        <div class="card shadow-sm border-0 h-100 rounded-xl" style="border-left: 4px solid #4E6BFF;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-1 fw-medium">Total Pendapatan</p>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-chart-line text-primary fa-lg"></i>
                    </div>
                </div>
                @if($growthPercentage != 0)
                    <div class="d-flex align-items-center gap-2">
                        @if($growthPercentage > 0)
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">
                                <i class="fas fa-arrow-up fa-xs me-1"></i>{{ abs($growthPercentage) }}%
                            </span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1">
                                <i class="fas fa-arrow-down fa-xs me-1"></i>{{ abs($growthPercentage) }}%
                            </span>
                        @endif
                        <span class="text-muted small">vs periode sebelumnya</span>
                    </div>
                @else
                    <div class="text-muted small">Tidak ada data sebelumnya</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Card 2: Gross Profit --}}
    <div class="{{ $colClass }} mb-1 carddashboard">
        <div class="card shadow-sm border-0 h-100 rounded-xl" style="border-left: 4px solid #198754;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-1 fw-medium">Gross Profit</p>
                        <h3 class="mb-0 fw-bold">Rp {{ number_format($totalLabaBersih, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-coins text-success fa-lg"></i>
                    </div>
                </div>
                @php
                    $hppPercent = $totalPendapatan > 0 ? round(($totalHPP / $totalPendapatan) * 100, 1) : 0;
                    $grossMargin = $totalPendapatan > 0 ? round(($totalLabaBersih / $totalPendapatan) * 100, 1) : 0;
                @endphp
                <div class="d-flex flex-column gap-1">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">HPP</span>
                        <span class="text-muted small">Rp {{ number_format($totalHPP, 0, ',', '.') }}</span>
                        <span class="badge bg-light text-secondary border border-secondary-subtle rounded-pill">{{ $hppPercent }}%</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Gross Margin</span>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill">{{ $grossMargin }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Total Transaksi --}}
    <div class="{{ $colClass }} mb-1 carddashboard">
        <div class="card shadow-sm border-0 h-100 rounded-xl" style="border-left: 4px solid #0dcaf0;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-muted small mb-1 fw-medium">Total Transaksi</p>
                        <h3 class="mb-0 fw-bold">{{ number_format($totalTransaksi, 0, ',', '.') }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-shopping-cart text-info fa-lg"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center flex-wrap  gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1">
                        Penjualan: {{ $dataTransaksiChart[0] ?? 0 }}
                    </span>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1">
                        Pembelian: {{ $dataTransaksiChart[1] ?? 0 }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Cabang Terbaik --}}
    @if($showBestBranch)
        <div class="{{ $colClass }} mb-1 carddashboard">
            <div class="card shadow-sm border-0 h-100 rounded-xl" style="border-left: 4px solid #ffc107;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <p class="text-muted small mb-1 fw-medium">Cabang Terbaik</p>
                            <h5 class="mb-1 fw-bold text-truncate" title="{{ $namaCabangTerbaik }}">
                                {{ $namaCabangTerbaik }}
                            </h5>
                            @if($bestBranchGrowthPercent != 0)
                                <h6 class="mb-0 text-muted">{{ $bestBranchGrowthPercent }}%</h6>
                            @else
                                <h6 class="mb-0 text-muted">-</h6>
                            @endif
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-trophy text-warning fa-lg"></i>
                        </div>
                    </div>
                    @php
                        $periodFrom = $bestBranchGrowthPeriod['from'] ?? null;
                        $periodTo = $bestBranchGrowthPeriod['to'] ?? null;
                        $periodLabel = null;
                        if ($periodFrom && $periodTo) {
                            $fromLabel = $labelBulan[($periodFrom['month'] ?? 1) - 1] ?? '';
                            $toLabel = $labelBulan[($periodTo['month'] ?? 1) - 1] ?? '';
                            $periodLabel = trim($fromLabel . ' → ' . $toLabel);
                        }
                    @endphp
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">Pertumbuhan omset tertinggi{{ $periodLabel ? ' (' . $periodLabel . ')' : '' }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- ======================================================= --}}
{{-- CHARTS SECTION --}}
{{-- ======================================================= --}}
<div class="row annualcard mb-3">
    {{-- Main Chart: Annual --}}
    <div class="col-xl-7 col-lg-12 mb-1 carddashboard">
        <div class="card shadow-sm border-0 h-100 overflow-hidden rounded-xl">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="card-title fw-bold mb-0 text-dark">Grafik Annual ({{ $selectedYear }})</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div id="chartPendapatanBulanan" style="min-height: 320px;"></div>
            </div>
        </div>
    </div>

   {{-- Donut Chart: Total Transaksi --}}
    <div class="col-xl-5 col-lg-12 mb-1">
        <div class="card shadow-sm border-0 h-100 overflow-hidden rounded-xl transaksi-card">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="card-title fw-bold mb-0 text-dark">Transaksi</h5>
            </div>
            <div class="card-body p-4 cardtransaksi-body">
                <div class="d-flex flex-row flex-nowrap align-items-center transaksi-wrapper h-100">

                    <div class="flex-grow-1 flex-shrink-1 transaksi-chart-wrapper d-flex align-items-center justify-content-center">
                        <div id="chartTotalTransaksi" class="w-100 h-100"></div>
                    </div>

                    <div class="flex-shrink-0 transaksi-legend-wrapper">
                        @php
                            $totalTransaksi = array_sum($dataTransaksiChart ?? []);
                            $penjualan = $dataTransaksiChart[0] ?? 0;
                            $pembelian = $dataTransaksiChart[1] ?? 0;
                            $penjualanPct = $totalTransaksi > 0 ? round(($penjualan / $totalTransaksi) * 100, 1) : 0;
                            $pembelianPct = $totalTransaksi > 0 ? round(($pembelian / $totalTransaksi) * 100, 1) : 0;
                        @endphp
                        <div class="transaksi-legend">
                            <div class="transaksi-pill mb-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="transaksi-dot bg-primary bg-opacity-10">
                                        <i class="fas fa-bag-shopping text-primary transaksi-dot-icon"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted small">Penjualan</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <h4 class="fw-bold mb-0 text-primary">{{ number_format($penjualan) }}</h4>
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold">{{ $penjualanPct }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="transaksi-pill">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="transaksi-dot bg-success bg-opacity-10">
                                        <i class="fas fa-cart-arrow-down text-success transaksi-dot-icon"></i>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <span class="text-muted small">Pembelian</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <h4 class="fw-bold mb-0 text-success">{{ number_format($pembelian) }}</h4>
                                            <span class="badge bg-success bg-opacity-10 text-success fw-semibold">{{ $pembelianPct }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- WIDGET SECTION: Top Products --}}
{{-- ======================================================= --}}
<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow-sm border-0 overflow-hidden rounded-xl">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fas fa-star text-warning me-2"></i>Top 5 Produk Terlaris
                </h5>
            </div>
            <div class="card-body p-0">
                @if($topProducts->count() > 0)
                    <div class="table-modern-container">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center ps-4" style="width: 5%;">No</th>
                                    <th>Produk</th>
                                    <th>Kode SKU</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-center pe-4">Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $index => $product)
                                    <tr class="table-row-hover clickable-row" data-detail-url="{{ route('admin.products.show', $product['id']) }}">
                                        <td class="text-center align-middle ps-4">
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center gap-3">
                                                @if($product['gambar'] && $product['gambar']->url)
                                                    <img src="{{ $product['gambar']->url }}" alt="{{ $product['nama_produk'] }}"
                                                         class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="text-dark fw-semibold d-block" style="font-size: 0.9rem;">
                                                        {{ $product['nama_produk'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <span class="font-monospace text-secondary bg-light rounded px-2 py-1">{{ $product['kode_sku'] }}</span>
                                        </td>
                                        <td class="text-end align-middle">
                                            <span class="fw-bold text-dark">Rp {{ number_format($product['harga_jual'], 0, ',', '.') }}</span>
                                        </td>
                                        <td class="text-center align-middle pe-4">
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold rounded-pill px-3 py-2">
                                                {{ $product['total_qty'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Belum ada data produk</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ======================================================= --}}
{{-- TRANSACTION PURCHASES TABLE --}}
{{-- ======================================================= --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 overflow-hidden rounded-xl">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="fas fa-shopping-bag text-success me-2"></i>Transaksi Pembelian Terakhir
                </h5>
            </div>
            <div class="card-body p-0">
                @if(isset($recentPurchases) && $recentPurchases->count() > 0)
                    <div class="table-modern-container">
                        <table class="table table-modern mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center ps-4" style="width: 5%;">No</th>
                                    <th>Kode Transaksi</th>
                                    <th>Customer</th>
                                    <th>Cabang</th>
                                    <th style="width: 25%; min-width:200px; max-width: 300px;">Item Dibeli</th>
                                    <th class="text-end">Harga Deal</th>
                                    <th class="text-center">Status</th>
                                    <th class="pe-4">Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPurchases as $index => $purchase)
                                    <tr class="table-row-hover clickable-row" data-detail-url="{{ route('admin.purchases.show', $purchase['id']) }}">
                                        <td class="text-center align-middle ps-4">
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                {{ $index + 1 }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="font-monospace text-primary fw-semibold bg-opacity-5 rounded px-2 py-1">
                                                {{ $purchase['kode_transaksi'] }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-dark fw-semibold d-block" style="font-size: 0.9rem;">
                                                {{ $purchase['customer'] }}
                                            </span>
                                        </td>
                                        <td class="align-middle">
                                            <span class="fw-medium text-secondary rounded px-2 py-1">
                                                {{ $purchase['cabang'] }}
                                            </span>
                                        </td>
                                        <td class="align-middle" style="min-width:200px; max-width: 300px;">
                                            <div class="d-flex flex-column gap-1 text-wrap">
                                                @foreach($purchase['items'] as $item)
                                                    <div class="rounded px-2 py-1">
                                                        <span class="text-dark">{{ $item['nama_produk'] }}</span>
                                                        <span class="text-muted small">(x{{ $item['qty'] }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="text-end align-middle">
                                            @if($purchase['harga_deal'] > 0)
                                                <span class="fw-bold text-dark">Rp {{ number_format($purchase['harga_deal'], 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($purchase['status'] == 'deal')
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">Deal</span>
                                            @elseif($purchase['status'] == 'tidak_deal')
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2">Tidak Deal</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">Draft</span>
                                            @endif
                                        </td>
                                        <td class="align-middle pe-4">
                                            <span class="text-muted small">{{ $purchase['waktu'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <p class="text-muted mb-0">Belum ada transaksi pembelian</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>



@endsection


@push('scripts')
    <script>
        @php
            $seriesPendapatan = [
                ['name' => 'Pendapatan', 'data' => $dataPendapatanChart],
                ['name' => 'HPP', 'data' => $dataHppChart],
            ];
        @endphp
        window.dashboardData = {
            seriesPendapatan: @json($seriesPendapatan),
            seriesTransaksi: @json($dataTransaksiChart),
            labelBulan: @json($labelBulan)
        };
    </script>
    @vite('resources/js/admin/pages/dashboard.js')
@endpush
