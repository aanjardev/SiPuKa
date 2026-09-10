@extends('layouts.admin')

@section('title', 'Arsip Produk')

@push('page-actions')
<a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-arrow-left"></i>
    <span>Kembali ke Produk Aktif</span>
</a>
@endpush

@section('content')
<form method="GET" action="{{ route('admin.products.archived') }}" id="searchForm">
    <div class="card shadow-sm border-0 mb-4 product-filter-card" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap product-filter-body">
            <div class="d-flex align-items-center flex-grow-1 ps-2 product-filter-input">
                <span class="text-muted ms-2 me-3 product-filter-icon">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-0 shadow-none bg-transparent"
                       name="search"
                       placeholder="Cari produk diarsipkan"
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;"
                       autofocus>
            </div>

            <div class="d-flex align-items-center gap-2 pe-2 product-filter-controls">
                <select name="kategori"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="all" {{ ($selected_kategori ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach($semua_kategori ?? [] as $kat)
                        <option value="{{ $kat->id }}" {{ ($selected_kategori ?? 'all') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

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

<div class="card shadow-sm border-0 product-table-card" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive product-table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 35%;">Produk</th>
                        <th>SKU</th>
                        <th>Harga</th>
                        <th class="text-center">Stok</th>
                        <th>Last Update</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $index => $product)
                    <tr class="clickable-row" data-detail-url="{{ route('admin.products.show', $product->id) }}">
                        <td class="text-center text-muted fw-bold">{{ ($products->firstItem() ?? 0) + $index }}</td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-shrink-0 position-relative">
                                    @if ($product->gambarUtama)
                                    <img src="{{ $product->gambarUtama->url }}" loading="lazy"
                                         alt="Img"
                                         class="rounded-3 shadow-sm"
                                         style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                    <div class="rounded-3 bg-light d-flex align-items-center justify-content-center text-secondary fw-bold"
                                         style="width: 45px; height: 45px; font-size: 0.7rem;">
                                        Img
                                    </div>
                                    @endif
                                </div>

                                <div class="flex-grow-1" style="min-width: 200px; max-width: 320px;">
                                    <span class="text-dark fw-semibold d-block"
                                        style="font-size: 0.95rem !important;
                                            line-height: 1.4 !important;
                                            display: -webkit-box !important;
                                            -webkit-line-clamp: 2 !important;
                                            -webkit-box-orient: vertical !important;
                                            overflow: hidden !important;
                                            text-overflow: ellipsis !important;
                                            word-wrap: break-word !important;">
                                        {{ $product->nama_produk }}
                                    </span>
                                    <span class="badge bg-light border text-secondary mt-1">Diarsipkan</span>
                                    @if(!$product->is_visible)
                                    <span class="badge bg-light text-secondary border mt-1">Hidden</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="text-nowrap">
                            <a href="{{ route('admin.products.show', $product->id) }}"
                               class="fw-medium text-primary text-decoration-none clickable-code"
                               onclick="event.stopPropagation();">
                                {{ $product->kode_sku ?? '-' }}
                            </a>
                        </td>

                        <td class="fw-bold text-dark text-nowrap">
                            Rp{{ number_format($product->harga_jual, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
                            @if($product->stok_produk > 5)
                            <span class="badge rounded-pill bg-success bg-opacity-10 text-success" style="font-size: 0.9rem; line-height: 1">
                                {{ $product->stok_produk }}
                            </span>
                            @elseif($product->stok_produk > 0)
                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning" style="font-size: 0.9rem; line-height: 1">
                                {{ $product->stok_produk }}
                            </span>
                            @else
                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger" style="font-size: 0.9rem; line-height: 1">
                                Habis
                            </span>
                            @endif
                        </td>

                        <td class="text-muted small text-nowrap">
                            {{ $product->updated_at->format('d M Y') }}
                            <span class="opacity-75">{{ $product->updated_at->format('H:i') }}</span>
                        </td>

                        <td class="text-center no-row-navigation">
                            <div class="d-flex justify-content-center gap-2">
                                <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-action btn-action-edit" title="Restore" onclick="event.stopPropagation();">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                </form>

                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="btn-action btn-action-delete"
                                            title="Hapus"
                                            onclick="event.stopPropagation(); handleDeleteProduk(this);">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-box-open fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada produk diarsipkan</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($products->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .clickable-code {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .clickable-code:hover {
        text-decoration: underline !important;
        opacity: 0.8;
    }
    .product-table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 0 0.75rem;
    }
    .product-table-responsive .table {
        min-width: 860px;
        width: 100%;
    }
    .product-filter-input .form-control {
        min-width: 220px;
    }
    @media (max-width: 1200px) {
        .product-filter-body {
            gap: 0.5rem;
        }
        .product-filter-input {
            width: 100%;
            padding-left: 0.5rem !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.3rem 0.75rem;
        }
        .product-filter-card .product-filter-input .form-control {
            border: 0 !important;
            background-color: transparent !important;
            padding: 0.45rem 0;
            font-size: 0.85rem;
        }
        .product-filter-icon {
            margin-left: 0 !important;
        }
        .product-filter-controls {
            width: 100%;
            padding-right: 0 !important;
            justify-content: space-between;
        }
        .product-filter-controls .form-select {
            flex: 1 1 0;
            min-width: 0;
        }
        .product-filter-card .product-filter-controls .form-select {
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.55rem 0.75rem;
            font-size: 0.85rem;
        }
    }
    @media (max-width: 576px) {
        .product-filter-body {
            padding: 0.75rem !important;
        }
        .product-filter-input .form-control {
            font-size: 0.85rem;
        }
        .product-filter-controls {
            flex-direction: column;
            align-items: stretch !important;
        }
        .product-filter-controls .form-select {
            width: 100%;
        }
        .product-table-card {
            border-radius: 12px;
        }
        .product-table-responsive {
            padding: 0 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('input[name="search"]');
    if (input) {
        input.focus();
        const length = input.value.length;
        input.setSelectionRange(length, length);
    }
});
</script>

<script>
    function handleDeleteProduk(button) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete('Apakah Anda yakin ingin menghapus produk ini secara permanen?', 'Konfirmasi Hapus')
                .then((result) => {
                    if (result.isConfirmed) {
                        button.form.submit();
                    }
                });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini secara permanen?')) {
                button.form.submit();
            }
        }
    }

    window.handleDeleteProduk = handleDeleteProduk;
</script>
@endpush
