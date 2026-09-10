@extends('layouts.admin')

@section('title', 'Data Kategori')

@push('page-actions')
<a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-plus fa-fw"></i>
    <span>Tambah Kategori</span>
</a>
@endpush

@push('styles')
<style>
    .category-table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 0;
    }
    .category-table {
        width: 100%;
        min-width: 100%;
    }
    .category-table th,
    .category-table td {
        white-space: nowrap;
    }
    .category-filter-input .form-control {
        min-width: 220px;
    }
    @media (max-width: 1200px) {
        .category-filter-body {
            gap: 0.5rem;
        }
        .category-filter-input {
            width: 100%;
            padding-left: 0.5rem !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.3rem 0.75rem;
        }
        .category-filter-card .category-filter-input .form-control {
            border: 0 !important;
            background-color: transparent !important;
            padding: 0.45rem 0;
            font-size: 0.85rem;
        }
        .category-filter-icon {
            margin-left: 0 !important;
        }
        .category-filter-controls {
            width: 100%;
            padding-right: 0 !important;
            justify-content: space-between;
        }
        .category-filter-controls .form-select {
            flex: 1 1 0;
            min-width: 0;
        }
        .category-filter-card .category-filter-controls .form-select {
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.55rem 0.75rem;
            font-size: 0.85rem;
        }
    }
    @media (max-width: 576px) {
        .category-filter-body {
            padding: 0.75rem !important;
        }
        .category-filter-input .form-control {
            font-size: 0.85rem;
        }
        .category-filter-controls {
            flex-direction: column;
            align-items: stretch !important;
        }
        .category-filter-controls .form-select {
            width: 100%;
        }
        .category-table-card {
            border-radius: 12px;
        }
        .category-table-responsive {
            padding: 0 0.75rem;
        }
        .category-table {
            min-width: 900px;
        }
        .category-table th,
        .category-table td {
            white-space: normal;
        }
    }
</style>
@endpush

@section('content')

{{-- Filter dan Pencarian --}}
<form method="GET" action="{{ route('admin.categories.index') }}" id="searchForm">
    <div class="card shadow-sm border-0 mb-4 category-filter-card" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap category-filter-body">
            {{-- Bagian Kiri: Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2 category-filter-input">
                <span class="text-muted ms-2 me-3 category-filter-icon">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       class="form-control border-0 shadow-none bg-transparent"
                       name="search"
                       placeholder="Cari kategori..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            {{-- Bagian Kanan: Dropdown --}}
            <div class="d-flex align-items-center gap-2 pe-2 category-filter-controls">
                <select name="sort_by"
                        class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        style="cursor: pointer;"
                        onchange="document.getElementById('searchForm').submit();">
                    <option value="nama" {{ ($sort_by ?? 'nama') == 'nama' ? 'selected' : '' }}>Urutkan: A-Z</option>
                    <option value="nama_desc" {{ ($sort_by ?? '') == 'nama_desc' ? 'selected' : '' }}>Urutkan: Z-A</option>
                    <option value="terbaru" {{ ($sort_by ?? '') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                </select>
            </div>
        </div>
    </div>
</form>

{{-- Table Card --}}
<div class="card shadow-sm border-0 category-table-card" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
    <div class="card-body p-0">
        <div class="table-responsive category-table-responsive">
            <table class="table table-modern mb-0 category-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 5%;">No</th>
                        <th style="width: 15%;">Gambar</th>
                        <th>Nama Kategori</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                    <tr class="clickable-row" data-detail-url="{{ route('admin.categories.edit', $category->id) }}">
                        <td class="text-center text-muted fw-bold">{{ ($categories->firstItem() ?? 0) + $index }}</td>

                        <td>
                            <img src="{{ $category->image_url ?? asset('images/placeholder.jpg') }}" alt="{{ $category->nama_kategori }}" class="img-fluid rounded border" style="max-height: 64px;">
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-grow-1">
                                    <span class="text-dark fw-semibold" style="font-size: 0.95rem;">
                                        {{ $category->nama_kategori }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="text-center no-row-navigation">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                    class="btn-action btn-action-edit"
                                    title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                @if($category->produk_count == 0)
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                        class="btn-action btn-action-delete"
                                        title="Hapus"
                                        data-message="Apakah Anda yakin ingin menghapus kategori ini?">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="btn-action text-muted"
                                      title="Kategori tidak dapat dihapus karena digunakan oleh {{ $category->produk_count }} produk"
                                      style="cursor: not-allowed; opacity: 0.5;">
                                    <i class="fa-solid fa-trash"></i>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center opacity-50">
                                <i class="fa-solid fa-folder-open fa-3x mb-3 text-muted"></i>
                                <h6 class="text-muted">Belum ada kategori</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (method_exists($categories, 'hasPages') && $categories->hasPages())
    <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
        {{ $categories->links('pagination::bootstrap-5') }}
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
        input.setSelectionRange(length, length);
    }
});
</script>
@endpush
