@extends('layouts.admin')

@section('title', 'Foto Produk')

@push('styles')
<style>
    

        vertical-align: middle !important;
    }

        vertical-align: middle !important;
    }

    
    .sku-chip {
        display: inline-block;
        font-family: "JetBrains Mono", "SFMono-Regular", Menlo, Consolas, monospace;
        font-size: 0.85rem;
        font-weight: 700;
        color: #0d6efd;
        background: rgba(13, 110, 253, 0.1);
        border-radius: 10px;
        padding: 0.35rem 0.65rem;
        line-height: 1.2;
        letter-spacing: -0.01em;
    }
    
    .photo-table {
        table-layout: fixed;
        width: 100%;
    }
    .photo-table th,
    .photo-table td {
        word-break: break-word;
    }

    .photo-table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        padding: 0;
    }
    .photo-table {
        width: 100%;
        min-width: 100%;
    }
    .photo-table th,
    .photo-table td {
        white-space: nowrap;
    }
    .photo-filter-input .form-control {
        min-width: 220px;
    }
    @media (max-width: 1200px) {
        .photo-filter-body {
            gap: 0.5rem;
        }
        .photo-filter-input {
            width: 100%;
            padding-left: 0.5rem !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.3rem 0.75rem;
        }
        .photo-filter-card .photo-filter-input .form-control {
            border: 0 !important;
            background-color: transparent !important;
            padding: 0.45rem 0;
            font-size: 0.85rem;
        }
        .photo-filter-icon {
            margin-left: 0 !important;
        }
        .photo-filter-controls {
            width: 100%;
            padding-right: 0 !important;
            justify-content: space-between;
        }
        .photo-filter-controls .form-select {
            flex: 1 1 0;
            min-width: 0;
        }
        .photo-filter-card .photo-filter-controls .form-select {
            border: 1px solid #dee2e6 !important;
            border-radius: 10px;
            background-color: #fff !important;
            padding: 0.55rem 0.75rem;
            font-size: 0.85rem;
        }
    }
    @media (max-width: 576px) {
        .photo-filter-body {
            padding: 0.75rem !important;
        }
        .photo-filter-input .form-control {
            font-size: 0.85rem;
        }
        .photo-filter-controls {
            flex-direction: column;
            align-items: stretch !important;
        }
        .photo-filter-controls .form-select {
            width: 100%;
        }
        .photo-table-card {
            border-radius: 12px;
        }
        .photo-table-responsive {
            padding: 0 0.75rem;
        }
        .photo-table {
            min-width: 900px;
        }
        .photo-table th,
        .photo-table td {
            white-space: nowrap;
        }
    }
</style>
@endpush

@section('content')

{{-- FILTER & SEARCH (diseragamkan dengan dataPembelian) --}}
<form action="{{ route('admin.products.photos') }}" method="GET" id="filterFormPhoto">
    <div class="card shadow-sm border-0 mb-4 photo-filter-card" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap photo-filter-body">

            {{-- Search SKU / Nama Produk --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2 photo-filter-input">
                <span class="text-muted ms-2 me-3 photo-filter-icon">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text"
                       name="search"
                       id="search-input-photo"
                       class="form-control border-0 shadow-none bg-transparent"
                       placeholder="Cari SKU atau Nama Produk..."
                       value="{{ $search_term ?? '' }}"
                       style="font-size: 0.95rem;">
            </div>

            {{-- Filter Kategori --}}
            <div class="d-flex align-items-center gap-2 pe-2 mt-2 mt-md-0 photo-filter-controls">
                <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                        name="kategori" id="filter-kategori" style="cursor: pointer;">
                    <option value="">Semua Kategori</option>
                    @foreach($semua_kategori as $kat)
                        <option value="{{ $kat->id }}" {{ (isset($selected_kategori) && $selected_kategori == $kat->id) ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</form>

{{-- LIST TABEL FOTO PRODUK (dibungkus card seperti dataPembelian) --}}
<div id="photo-list-container">
    <div class="card shadow-sm border-0 photo-table-card" style="border-radius: 15px; overflow: hidden; min-height: 700px;">
        <div class="card-body p-0 table-wrapper">
            <div class="table-responsive photo-table-responsive">
                <table class="table table-modern mb-0 table-product table-md photo-table">
                <thead>
                    <tr>
                        <th style="width:60px" class="text-center">No</th>
                        <th style="width: 150px;">Kode SKU</th>
                        <th>Nama Produk</th>
                        <th style="width: 160px;">Kategori</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="photo-products-tbody">
                    @include('admin.partials.photo_product_rows', ['products' => $products])
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div id="pagination-links-container">
        @if ($products->hasPages())
            <div class="card-footer bg-white border-0 d-flex justify-content-end py-3 px-4">
                {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
    <style>
        

        .table-product tbody tr.tr-empty td .empty-message {
            min-height: 250px;
            width: 100%;
        }

        .table-product tbody tr:not(.tr-empty) td {
            vertical-align: top !important;
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
        input.setSelectionRange(length, length); // kursor ke akhir
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterFormPhoto') || document.querySelector('form[action*="products/photos"]');
    const tbody = document.getElementById('photo-products-tbody');
    const paginationContainer = document.getElementById('pagination-links-container');

    function buildUrl(params) {
        const base = '{{ route('admin.products.photos') }}';
        const query = new URLSearchParams(params).toString();
        return base + (query ? ('?' + query) : '');
    }

    let timer = null;
    function fetchAndRender() {
        const formData = new FormData(form);
        const params = {};
        for (const [k,v] of formData.entries()) {
            if (v !== '') params[k] = v;
        }
        const url = buildUrl(params);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => {
                if (!r.ok) throw new Error('Network response was not ok. Status: ' + r.status);
                return r.json();
            })
            .then(data => {
                if (data.table_html !== undefined) {
                    tbody.innerHTML = data.table_html;
                } else {
                    tbody.innerHTML = '';
                }
                paginationContainer.innerHTML = data.pagination_html ?
                    `<div class="card-footer bg-white">${data.pagination_html}</div>` : '';
                attachPaginationLinks();
            }).catch(err => {
                console.error('Failed to fetch photo products', err);

                tbody.innerHTML = `
                    <tr class="tr-empty">
                        <td colspan="5" class="p-0">
                            <div class="d-flex flex-column align-items-center justify-content-center p-5 empty-message" style="min-height: 250px; width: 100%;">
                                <i class="fa-solid fa-exclamation-triangle fa-2x text-muted mb-3"></i>
                                <h5 class="mb-1">Gagal memuat data</h5>
                                <p class="text-muted mb-0">Silakan coba lagi atau cek log server.</p>
                            </div>
                        </td>
                    </tr>
                `;
            });
    }

    function debounceFetch() {
        if (timer) clearTimeout(timer);
        timer = setTimeout(fetchAndRender, 350);
    }

    form.addEventListener('input', debounceFetch);
    form.addEventListener('change', debounceFetch);

    function attachPaginationLinks() {
        const links = paginationContainer.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                if (!href) return;
                fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                    .then(r => {
                        if (!r.ok) throw new Error('Network response was not ok. Status: ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        tbody.innerHTML = data.table_html || '';
                        paginationContainer.innerHTML = data.pagination_html ?
                            `<div class="card-footer bg-white">${data.pagination_html}</div>` : '';
                        attachPaginationLinks();
                    }).catch(err => {
                        console.error(err);

                        tbody.innerHTML = `
                            <tr class="tr-empty">
                                <td colspan="5" class="p-0">
                                    <div class="d-flex flex-column align-items-center justify-content-center p-5 empty-message" style="min-height: 250px; width: 100%;">
                                        <i class="fa-solid fa-exclamation-triangle fa-2x text-muted mb-3"></i>
                                        <h5 class="mb-1">Gagal memuat data</h5>
                                        <p class="text-muted mb-0">Silakan coba lagi atau cek log server.</p>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
            });
        });
    }

    attachPaginationLinks();
});
</script>
@endpush
