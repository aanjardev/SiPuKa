@extends('layouts.admin')

@section('title', 'Katalog Penjualan')

@push('styles')
@vite('resources/css/admin/pages/listProdukJual.css')
@endpush

@push('page-actions')
    @php
        $backRoute = route('admin.sales.index');
        if(isset($penjualan)) {
            $backRoute = route('admin.sales.show', $penjualan->id);
        }
    @endphp

    <a href="{{ $backRoute }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="btnKembali">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
@endpush

@section('content')

{{-- ======= Search & Filter ======= --}}
<form method="GET" action="{{ route('admin.sales.create') }}" id="searchForm">
    <div class="card shadow-sm border-0 mb-4 catalog-filter-card" style="border-radius: 10px;">
        <div class="card-body p-2 d-flex align-items-center flex-wrap catalog-filter-body">
            {{-- Input Search --}}
            <div class="d-flex align-items-center flex-grow-1 ps-2 catalog-filter-input">
                <span class="text-muted ms-2 me-3 catalog-filter-icon">
                    <i class="fa-solid fa-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-0 shadow-none bg-transparent"
                    name="search"
                    value="{{ $search_term ?? '' }}"
                    placeholder="Cari produk berdasarkan nama atau SKU..."
                    style="font-size: 0.95rem;"
                    autofocus>
            </div>

            {{-- Dropdown Kategori --}}
            <div class="d-flex align-items-center gap-2 pe-2 catalog-filter-controls">
                <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                    name="kategori"
                    onchange="document.getElementById('searchForm').submit();"
                    style="cursor: pointer;">
                    <option value="all" {{ ($selected_kategori ?? 'all') == 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach ($kategori as $kat)
                        <option value="{{ $kat->id }}" {{ ($selected_kategori ?? 'all') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>

                {{-- Dropdown Sort --}}
                <select class="form-select border-0 shadow-none bg-transparent text-secondary w-auto fw-medium"
                    name="sort_by"
                    onchange="document.getElementById('searchForm').submit();"
                    style="cursor: pointer;">
                    <option value="terbaru" {{ ($sort_by ?? 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    <option value="nama_asc" {{ ($sort_by ?? 'terbaru') == 'nama_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="harga_asc" {{ ($sort_by ?? 'terbaru') == 'harga_asc' ? 'selected' : '' }}>Harga Terendah</option>
                </select>
            </div>
        </div>
    </div>
</form>

{{-- ======= Grid Produk + Pagination ======= --}}
<div id="catalogWrapper" class="position-relative">
    <div id="gridView" class="catalog-grid mb-5">
        @forelse ($products as $product)
            <div class="catalog-grid-item">
                <div class="card h-100 shadow-sm border-0 product-card"
                     style="border-radius: 12px; overflow: hidden;"
                     data-product-id="{{ $product->id }}"
                     data-price="{{ $product->harga_jual ?? 0 }}"
                     data-stock="{{ $product->stok_produk ?? 0 }}"
                     data-name="{{ $product->nama_produk }}"
                     data-sku="{{ $product->kode_sku }}">

                    {{-- Gambar Produk (Rasio 4:3 agar tidak terlalu tinggi) --}}
                    <div class="position-relative" style="padding-top: 75%; background-color: #f8f9fa;">
                        @php
                            $image = $product->gambarUtama ? $product->gambarUtama->url : asset('images/placeholder.jpg');
                        @endphp
                        <img src="{{ $image }}"
                             class="position-absolute top-0 start-0 w-100 h-100 object-fit-cover"
                             alt="{{ $product->nama_produk }}" loading="lazy">

                        {{-- Badge Stok (Opsional, jika ingin overlay) --}}
                        @if(($product->stok_produk ?? 0) <= 0)
                            <div class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex align-items-center justify-content-center">
                                <span class="badge bg-dark text-white px-3 py-2">Stok Habis</span>
                            </div>
                        @endif
                    </div>

                    <div class="card-body p-3 d-flex flex-column">
                        {{-- Meta: SKU & Kategori --}}
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted font-monospace meta-sku" style="font-size: 0.75rem;">{{ $product->kode_sku }}</small>
                            <span class="badge bg-light text-secondary border border-light-subtle text-warp text-end meta-category" style="font-size: 0.7rem;">
                                {{ $product->kategori->nama_kategori ?? 'Umum' }}
                            </span>
                        </div>

                        {{-- Nama Produk --}}
                        <h6 class="card-title fw-semibold text-dark mb-1 text-truncate-2" style="font-size: 0.95rem; min-height: 2.4em; line-height: 1.2em;">
                            {{ $product->nama_produk }}
                        </h6>

                        {{-- Harga --}}
                        <p class="card-text fw-bold text-primary mb-3" style="font-size: 1.1rem;">
                            Rp{{ number_format($product->harga_jual, 0, ',', '.') }}
                        </p>

                        {{-- Aksi & Stok --}}
                        <div class="mt-auto d-flex align-items-center gap-2">
                            @if (($product->stok_produk ?? 0) > 0)
                                {{-- Tombol Tambah --}}
                                <button class="btn btn-primary btn-sm flex-grow-1 fw-medium btn-add-product py-1" type="button" style="border-radius: 8px; font-size: 0.85rem;">
                                    <i class="fa-solid fa-plus me-1"></i>
                                    <span class="btn-label">Tambah</span>
                                </button>

                                {{-- Kontrol Qty (Hidden Awal) --}}
                                <div class="qty-control d-none flex-grow-1 justify-content-between align-items-center px-1 bg-primary bg-opacity-10 rounded-3" style="height: 32px;">
                                    <button class="btn btn-sm p-0 text-primary btn-dec" style="width: 24px;"><i class="fa-solid fa-minus" style="font-size: 0.8rem;"></i></button>
                                    <span class="qty-value fw-bold text-primary small">0</span>
                                    <button class="btn btn-sm p-0 text-primary btn-inc" style="width: 24px;"><i class="fa-solid fa-plus" style="font-size: 0.8rem;"></i></button>
                                </div>

                                {{-- Info Stok --}}
                                <small class="text-muted" style="font-size: 0.75rem; white-space: nowrap;">
                                    Stok: <strong>{{ $product->stok_produk }}</strong>
                                </small>
                            @else
                                <button class="btn btn-light text-muted w-100 btn-sm py-1 border" disabled style="border-radius: 8px; font-size: 0.85rem;">
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="d-inline-flex align-items-center justify-content-center bg-light rounded-circle mb-3" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-box-open fa-2x text-secondary opacity-50"></i>
                    </div>
                    <h6 class="text-muted">Tidak ada produk ditemukan</h6>
                </div>
            </div>
        @endforelse
    </div>

    {{-- ======= Pagination ======= --}}
    @if ($products->hasPages())
        <div class="d-flex justify-content-center mt-4 mb-5">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

{{-- Floating Cart Summary --}}
<div id="cartSummary" class="position-fixed start-50 translate-middle-x bottom-0 mb-4 p-2 bg-dark text-white rounded-pill shadow-lg d-none align-items-center gap-3 z-3 cart-summary-clickable"
     style="min-width: 300px; max-width: 90%; border: 1px solid rgba(255,255,255,0.1);">
    <div class="rounded-circle bg-white text-dark d-flex align-items-center justify-content-center ms-1" style="width: 32px; height: 32px;">
        <i class="fa-solid fa-bag-shopping" style="font-size: 0.9rem;"></i>
    </div>
    <div class="d-flex flex-column lh-1 cart-summary-meta">
        <span class="fw-bold" id="summaryItems" style="font-size: 0.9rem;">0 Item</span>
        {{-- <small class="text-white-50" style="font-size: 0.7rem;">Total Estimasi</small> --}}
        <button type="button" class="btn btn-link btn-sm text-decoration-none text-white-50 p-0 mt-1"
                id="btnToggleCartDetail" style="font-size: 0.7rem;">
            Lihat ringkasan
        </button>
    </div>
    <div class="ms-auto d-flex align-items-center gap-2 cart-summary-price">
        <span class="fw-bold me-2" id="summaryPrice">Rp0</span>
        <button id="btnCheckout" class="btn btn-primary btn-sm rounded-pill px-3 fw-medium btn-checkout">
            <span class="btn-label">Checkout</span>
            <i class="fa-solid fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
        </button>
    </div>
</div>

{{-- Overlay gelap saat ringkasan keranjang dibuka --}}
<div id="cartOverlay" class="cart-overlay-backdrop d-none"></div>


{{-- Panel ringkasan keranjang (pop-out center) --}}
<div id="cartDetail" class="position-fixed top-50 start-50 translate-middle bg-white rounded-4 shadow-lg border d-none cart-detail-panel" style="min-width: 360px;">
    <div class="d-flex align-items-center justify-content-between px-3 pt-3 pb-2 border-bottom">
        <div>
            <div class="small text-muted text-uppercase fw-semibold">Ringkasan Keranjang</div>
            <div class="fw-semibold text-dark" id="cartDetailTitle">List Item</div>
        </div>
        <button type="button" class="btn btn-light btn-sm d-inline-flex align-items-center justify-content-center rounded-circle border" style="width: 34px; height: 34px;" id="btnCloseCartDetail" aria-label="Tutup ringkasan">
            <i class="fa-solid fa-xmark fw-bold"></i>
        </button>
    </div>
    <div class="px-3 py-2 cart-detail-body" style="background: #f8f9fb; min-height: 50vh; max-height: 50vh; overflow-y: auto;">
        <ul class="list-unstyled mb-0 small" id="cartDetailList">
            {{-- Diisi via JavaScript --}}
        </ul>
    </div>
    <div class="px-3 py-3 border-top bg-white rounded-bottom-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="text-muted small d-block">Total Estimasi</span>
                <span class="fw-bold text-primary fs-6" id="cartDetailTotal">Rp0</span>
            </div>
            <button type="button" class="btn btn-primary btn-sm btn-checkout rounded-pill px-3">
                Checkout <i class="fa-solid fa-arrow-right ms-1" style="font-size: 0.7rem;"></i>
            </button>
        </div>
        <div class="progress mt-2" style="height: 6px; background-color: #e9ecef;">
            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%; opacity: 0.2;"></div>
        </div>
    </div>
</div>


<form id="checkoutForm" action="{{ route('admin.sales.store') }}" method="POST" class="d-none">

    @csrf
    <input type="hidden" name="items" id="checkoutItems">
    <input type="hidden" name="from_checkout" value="1">
</form>

<script type="application/json" id="cart-selections-json">
    @json($cartSelections ?? [])
</script>

@endsection

@push('styles')
<style>
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-card {
        transition: all 0.2s ease-in-out;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important;
    }
    .qty-control.shake { animation: shake 0.3s; }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25%, 75% { transform: translateX(-2px); }
        50% { transform: translateX(2px); }
    }
    .cart-overlay-backdrop {
        position: fixed;
        inset: 0;
        background-color: rgba(15, 23, 42, 0.4);
        z-index: 1040;
        pointer-events: auto;
    }
    .cart-summary-clickable {
        cursor: pointer;
    }
    .cart-detail-panel {
        width: 100%;
        max-width: 720px;
        max-height: 55vh;
        z-index: 1050;
    }
    .cart-detail-body {
        max-height: 35vh;
        overflow-y: auto;
    }
    .cart-detail-item-name {
        font-size: 0.9rem;
    }
    .cart-detail-item-sku {
        font-size: 0.75rem;
    }
    .cart-detail-qty-btn {
        width: 28px;
        height: 28px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .cart-detail-qty-btn.cart-inc-btn {
        background-color: #f8f9fa;
        color: #0d6efd;
        border: 1px solid rgba(13,110,253,.15);
    }
    .cart-detail-qty-btn.cart-dec-btn {
        background-color: #f8f9fa;
        color: #0d6efd;
        border: 1px solid rgba(13,110,253,.15);
    }
    .cart-detail-qty-value {
        min-width: 24px;
        text-align: center;
    }
</style>
@endpush

@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;
    let lastValue = searchInput ? searchInput.value : '';

    if (searchInput && searchForm) {
        // Pastikan caret tetap di akhir teks ketika halaman reload
        const len = searchInput.value.length;
        searchInput.focus({ preventScroll: true });
        searchInput.setSelectionRange(len, len);

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                if (searchInput.value !== lastValue) {
                    lastValue = searchInput.value;
                    searchForm.submit();
                }
            }, 1200); // debounce utama
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                if (searchInput.value !== lastValue) {
                    lastValue = searchInput.value;
                    searchForm.submit();
                }
            }
        });

        // Tambahkan debounce khusus saat penghapusan cepat agar tidak auto-submit saat masih menghapus
        ['keydown', 'keyup'].forEach(evt => {
            searchInput.addEventListener(evt, (e) => {
                const isDelete =
                    e.key === 'Backspace' ||
                    e.key === 'Delete' ||
                    (e.ctrlKey && e.key?.toLowerCase() === 'z');
                if (isDelete) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        if (searchInput.value !== lastValue) {
                            lastValue = searchInput.value;
                            searchForm.submit();
                        }
                    }, 1500); // jeda lebih lama khusus hapus
                }
            });
        });
    }
});
document.addEventListener('DOMContentLoaded', () => {
    const formatRupiah = (number) => {
        return 'Rp' + Number(number || 0).toLocaleString('id-ID');
    };

    const initialSelectionsEl = document.getElementById('cart-selections-json');
    const initialSelections = initialSelectionsEl ? JSON.parse(initialSelectionsEl.textContent || '[]') : [];

    const syncUrl = "{{ route('admin.sales.cart-sync') }}";
    const csrfToken = document.querySelector('meta[name=\"csrf-token\"]')?.getAttribute('content') || '';
    let syncTimeout = null;

    const sendCart = (immediate = false) => {
        if (!syncUrl) return;

        const payload = Object.entries(selections).map(([id, data]) => ({
            id,
            qty: data.qty || 0,
            price: data.price || 0,
        }));
        const body = JSON.stringify({ items: payload, _token: csrfToken });

        if (navigator.sendBeacon && immediate) {
            const blob = new Blob([body], { type: 'application/json' });
            navigator.sendBeacon(syncUrl, blob);
            return;
        }

        fetch(syncUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        keepalive: true,
        body,
        }).catch(() => {});
    };

    const scheduleSync = () => {
        clearTimeout(syncTimeout);
        syncTimeout = setTimeout(() => sendCart(false), 300);
    };

    const summaryEl = document.getElementById('cartSummary');
    const summaryItemsEl = document.getElementById('summaryItems');
    const summaryPriceEl = document.getElementById('summaryPrice');
    const checkoutBtns = document.querySelectorAll('.btn-checkout');
    const cartDetailEl = document.getElementById('cartDetail');
    const cartDetailListEl = document.getElementById('cartDetailList');
    const cartDetailTotalEl = document.getElementById('cartDetailTotal');
    const btnToggleCartDetail = document.getElementById('btnToggleCartDetail');
    const btnCloseCartDetail = document.getElementById('btnCloseCartDetail');
    const cartOverlayEl = document.getElementById('cartOverlay');
    const searchFormEl = document.getElementById('searchForm');

    const productMeta = {};
    const selections = initialSelections.reduce((acc, item) => {
        const id = String(item.id || '');
        const qty = Math.max(0, Number(item.qty || 0));
        const price = Number(item.price || 0);
        if (id && qty > 0) {
            acc[id] = { qty, price };
        }
        return acc;
    }, {});

    if (searchFormEl) {
        searchFormEl.addEventListener('submit', () => sendCart(true));
    }

    function adjustQtyFromDetail(productId, delta) {
        if (!productId) return;

        const current = selections[productId]?.qty || 0;
        let next = current + delta;

        const card = document.querySelector(`.product-card[data-product-id="${productId}"]`);
        let stock = Infinity;
        if (card) {
            const stockAttr = Number(card.dataset.stock || 0);
            if (!isNaN(stockAttr) && stockAttr > 0) {
                stock = stockAttr;
            }
        }

        if (delta > 0 && stock !== Infinity) {
            next = Math.min(next, stock);
        }

        if (next <= 0) {
            delete selections[productId];

            if (card) {
                const qtyControl = card.querySelector('.qty-control');
                const btnAdd = card.querySelector('.btn-add-product');
                const qtyValue = card.querySelector('.qty-value');
                if (qtyControl && btnAdd && qtyValue) {
                    qtyValue.textContent = 0;
                    qtyControl.classList.add('d-none');
                    qtyControl.classList.remove('d-flex');
                    btnAdd.classList.remove('d-none');
                }
            }
        } else {
            const meta = productMeta[productId] || {};
            const price = selections[productId]?.price ?? meta.price ?? 0;
            selections[productId] = { qty: next, price };

            if (card) {
                const qtyControl = card.querySelector('.qty-control');
                const btnAdd = card.querySelector('.btn-add-product');
                const qtyValue = card.querySelector('.qty-value');
                if (qtyControl && btnAdd && qtyValue) {
                    qtyValue.textContent = next;
                    btnAdd.classList.add('d-none');
                    qtyControl.classList.remove('d-none');
                    qtyControl.classList.add('d-flex');
                }
            }
        }

        updateSummary();
        buildCartDetail();
    }

    const buildCartDetail = () => {
        if (!cartDetailListEl || !cartDetailTotalEl) return;

        cartDetailListEl.innerHTML = '';

        const entries = Object.entries(selections);
        if (entries.length === 0) {
            cartDetailListEl.innerHTML = '<li class="text-muted text-center py-2">Belum ada item di keranjang.</li>';
            cartDetailTotalEl.textContent = formatRupiah(0);
            return;
        }

        let totalPrice = 0;

        entries.forEach(([id, data]) => {
            const meta = productMeta[id] || {};
            const name = meta.name || `Produk #${id}`;
            const sku = meta.sku ? ` (${meta.sku})` : '';
            const stock = typeof meta.stock === 'number' ? meta.stock : null;
            const remaining = stock !== null ? Math.max(stock - (data.qty || 0), 0) : null;
            const lineTotal = (data.qty || 0) * (data.price || 0);
            totalPrice += lineTotal;

            const li = document.createElement('li');
            li.className = 'd-flex justify-content-between align-items-start py-2 border-bottom';
            li.dataset.productId = id;
            li.innerHTML = `
                <div class="me-3">
                    <div class="fw-semibold text-dark cart-detail-item-name">${name}</div>
                    <div class="text-muted cart-detail-item-sku">${sku || '&nbsp;'}</div>
                    ${stock !== null ? `<div class="text-warning small">Sisa stok: ${remaining}</div>` : ''}
                </div>
                <div class="text-end">
                    <div class="d-flex align-items-center justify-content-end gap-2 mb-1">
                        <button type="button"
                                class="btn btn-sm cart-detail-qty-btn cart-dec-btn cart-dec"
                                data-product-id="${id}">
                            <i class="fa-solid fa-minus"></i>
                        </button>
                        <span class="fw-semibold small cart-detail-qty-value cart-qty" data-product-id="${id}">${data.qty}</span>
                        <button type="button"
                                class="btn btn-sm cart-detail-qty-btn cart-inc-btn cart-inc"
                                data-product-id="${id}">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                    <div class="fw-semibold">${formatRupiah(lineTotal)}</div>
                </div>
            `;
            cartDetailListEl.appendChild(li);

            const btnIncDetail = li.querySelector('.cart-inc');
            const btnDecDetail = li.querySelector('.cart-dec');

            btnIncDetail?.addEventListener('click', () => {
                adjustQtyFromDetail(id, 1);
            });

            btnDecDetail?.addEventListener('click', () => {
                adjustQtyFromDetail(id, -1);
            });
        });

        cartDetailTotalEl.textContent = formatRupiah(totalPrice);
    };

    const updateSummary = () => {
        const totalItems = Object.values(selections).reduce((sum, item) => sum + item.qty, 0);
        const totalPrice = Object.values(selections).reduce((sum, item) => sum + (item.qty * item.price), 0);

        if (totalItems > 0) {
            summaryItemsEl.textContent = `${totalItems} Item${totalItems > 1 ? 's' : ''}`;
            summaryPriceEl.textContent = formatRupiah(totalPrice);
            summaryEl.classList.remove('d-none');
            summaryEl.classList.add('d-flex');

            if (cartDetailEl && !cartDetailEl.classList.contains('d-none')) {
                buildCartDetail();
            }
        } else {
            summaryEl.classList.add('d-none');
            summaryEl.classList.remove('d-flex');

            if (cartDetailEl) {
                cartDetailEl.classList.add('d-none');
            }
        }

        scheduleSync();
    };

    document.querySelectorAll('.product-card').forEach(card => {
        const productId = card.dataset.productId;
        const price = Number(card.dataset.price || 0);
        const stock = Number(card.dataset.stock || 0);
        const name = (card.dataset.name || '').trim();
        const sku = (card.dataset.sku || '').trim();
        const btnAdd = card.querySelector('.btn-add-product');
        const qtyControl = card.querySelector('.qty-control');
        const btnInc = card.querySelector('.btn-inc');
        const btnDec = card.querySelector('.btn-dec');
        const qtyValue = card.querySelector('.qty-value');

        if (!btnAdd || !qtyControl) return;

        if (productId) {
            productMeta[productId] = {
                name: name || (card.querySelector('.card-title')?.textContent || '').trim(),
                sku: sku || (card.querySelector('.font-monospace')?.textContent || '').trim(),
                price,
                stock: isNaN(stock) ? null : stock,
            };
        }

        const selected = selections[productId];
        if (selected && selected.qty > 0) {
            const safeQty = stock > 0 ? Math.min(selected.qty, stock) : selected.qty;
            selections[productId].qty = safeQty;
            selections[productId].price = selected.price || price;
            qtyValue.textContent = safeQty;
            btnAdd.classList.add('d-none');
            qtyControl.classList.remove('d-none');
            qtyControl.classList.add('d-flex');
        }

        btnAdd.addEventListener('click', () => {
            selections[productId] = { qty: 1, price };
            qtyValue.textContent = 1;
            btnAdd.classList.add('d-none');
            qtyControl.classList.remove('d-none');
            qtyControl.classList.add('d-flex'); // Flex enable
            updateSummary();
        });

        btnInc.addEventListener('click', () => {
            const current = selections[productId]?.qty || 0;
            if (current >= stock) {
                qtyControl.classList.add('shake');
                setTimeout(() => qtyControl.classList.remove('shake'), 300);
                return;
            }
            const next = current + 1;
            selections[productId] = { qty: next, price };
            qtyValue.textContent = next;
            updateSummary();
        });

        btnDec.addEventListener('click', () => {
            const current = selections[productId]?.qty || 0;
            const next = Math.max(current - 1, 0);

            if (next === 0) {
                delete selections[productId];
                qtyValue.textContent = 0;
                qtyControl.classList.add('d-none');
                qtyControl.classList.remove('d-flex');
                btnAdd.classList.remove('d-none');
            } else {
                selections[productId].qty = next;
                qtyValue.textContent = next;
            }
            updateSummary();
        });
    });

    updateSummary();

    const toggleCartDetail = () => {
        if (!cartDetailEl) return;

        const willOpen = cartDetailEl.classList.contains('d-none');

        if (willOpen) {
            buildCartDetail();
            cartDetailEl.classList.remove('d-none');
        } else {
            cartDetailEl.classList.add('d-none');
        }

        if (cartOverlayEl) {
            if (willOpen) {
                cartOverlayEl.classList.remove('d-none');
            } else {
                cartOverlayEl.classList.add('d-none');
            }
        }
    };

    btnToggleCartDetail?.addEventListener('click', (e) => {
        e.preventDefault();
        toggleCartDetail();
    });

    btnCloseCartDetail?.addEventListener('click', () => {
        if (!cartDetailEl) return;
        cartDetailEl.classList.add('d-none');
        if (cartOverlayEl) {
            cartOverlayEl.classList.add('d-none');
        }
    });

    cartOverlayEl?.addEventListener('click', () => {
        if (cartDetailEl && !cartDetailEl.classList.contains('d-none')) {
            cartDetailEl.classList.add('d-none');
        }
        cartOverlayEl.classList.add('d-none');
    });

    summaryEl?.addEventListener('click', (e) => {
        const isCheckout = e.target.closest('.btn-checkout');
        if (isCheckout) {
            return;
        }
        e.preventDefault();
        toggleCartDetail();
    });

    const handleCheckout = (e) => {
        e.preventDefault();

        const totalItems = Object.values(selections).reduce((sum, item) => sum + item.qty, 0);
        if (totalItems === 0) return;

        for (const [productId, data] of Object.entries(selections)) {
            const stock = typeof productMeta[productId]?.stock === 'number' ? productMeta[productId].stock : Infinity;
            if (stock !== Infinity && data.qty > stock) {
                showToast(`Stok ${productMeta[productId]?.name || 'produk'} tidak mencukupi`, 'warning');
                return;
            }
        }

        const payload = Object.entries(selections).map(([id, data]) => ({
            id,
            qty: data.qty
        }));

        document.getElementById('checkoutItems').value = JSON.stringify(payload);

        const form = document.getElementById('checkoutForm');

        // Create a submit button and click it
        const submitButton = document.createElement('input');
        submitButton.type = 'submit';
        submitButton.style.display = 'none';
        form.appendChild(submitButton);

        submitButton.click();

        setTimeout(() => {
            if (submitButton.parentNode) {
                submitButton.parentNode.removeChild(submitButton);
            }
        }, 100);
    };

    checkoutBtns.forEach((btn) => {
        btn.addEventListener('click', handleCheckout);
    });
});
</script>
@endpush
