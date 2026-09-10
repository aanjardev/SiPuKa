@php
    $first = $products->firstItem();
@endphp

@forelse($products as $idx => $p)

    <tr class="clickable-row align-middle" data-detail-url="{{ route('admin.products.photos.upload', $p->id) }}">
        <td style="width:60px;" class="text-center fw-semibold text-muted">{{ $first + $idx }}</td>
        <td class="fw-semibold">
            <span class="sku-chip">#{{ $p->kode_sku }}</span>
        </td>
        <td class="align-middle">
            <div class="d-flex flex-column">
                <span>{{ $p->nama_produk }}</span>
                <!-- <small class="text-muted">ID: {{ $p->id }}</small> -->
            </div>
        </td>
        <td class="align-middle">
            <span class="text-secondary">
                {{ $p->kategori->nama_kategori ?? 'Tanpa Kategori' }}
            </span>
        </td>
        <td class="text-center no-row-navigation align-middle">
            <a href="{{ route('admin.products.photos.upload', $p->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" title="Unggah Foto">
                <i class="fas fa-upload"></i>
                <span class="d-none d-sm-inline">Upload</span>
            </a>
        </td>
    </tr>
@empty
    <tr class="tr-empty align-middle">
        <td colspan="5" class="text-center py-5">
            <div class="d-flex flex-column align-items-center opacity-50">
                <i class="fa-solid fa-image fa-2x text-muted mb-3 text-muted"></i>
                <h6 class="mb-1">Tidak Ada Produk</h6>
                <p class="text-muted small mb-0">Semua produk sudah memiliki foto atau belum ada produk tanpa gambar.</p>
            </div>
        </td>
    </tr>


@endforelse
