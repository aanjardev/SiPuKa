{{-- ini yg lama --}}
@php
$setting = \App\Models\CatalogSettings::first();
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Dinoyo Kamera</title>
    <link rel="shortcut icon" href="{{ $setting?->logo_url ?? asset('mainIMG/logoDK.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>

<body>
    @include('partials.headerAdmin')

    <main>
        <div class="container">
            <!-- Dashboard Header -->
            <div class="dashboard-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3">
                            <h1>Dashboard Admin</h1>
                        </div>
                        <p class="lead text-muted">Kelola produk anda, tambahkan item, dan update inventaris anda
                            disini.</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge bg-primary p-2">
                            <i class="bi bi-clock me-1"></i>
                            {{ now()->format('d M Y') }}
                        </span>

                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row g-4 mb-4">
                <div class="col-md-4 col-12">
                    <div class="admin-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                                <i class="bi bi-box-seam text-primary fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Total Produk</h6>
                                <h3 class="mb-0">{{ $totalProduk }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="admin-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                                <i class="bi bi-exclamation-circle text-warning fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Stok Menipis</h6>
                                <h3 class="mb-0">{{ $stokMenipis }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="admin-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                                <i class="bi bi-x-circle text-danger fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Produk Sold Out</h6>
                                <h3 class="mb-0">{{ $soldOutProducts }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add/Edit Product Form -->
            <section class="mb-4">
                <div class="admin-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">
                            <i class="bi bi-plus-circle me-2"></i>
                            {{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}
                        </h2>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ isset($product) ? route('admin.update', $product->id) : route('store') }}"
                            class="needs-validation" enctype="multipart/form-data" novalidate>
                            @csrf
                            @if(isset($product)) @method('PUT') @endif

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="kode_sku" class="form-label">Kode SKU</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-upc"></i></span>
                                        <input type="text" class="form-control" id="kode_sku" name="kode_sku"
                                            value="{{ old('kode_sku', isset($product) ? $product->kode_sku : '') }}" required autofocus >
                                    </div>
                                    @error('kode_sku')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="nama_produk" class="form-label">Nama Produk</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-camera"></i></span>
                                        <input type="text" class="form-control" id="nama_produk" name="nama_produk"
                                            value="{{ old('nama_produk', isset($product) ? $product->nama_produk : '') }}" required>
                                    </div>
                                    @error('nama_produk')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="harga_jual" class="form-label">Harga</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="harga_jual" name="harga_jual"
                                            value="{{ old('harga_jual', isset($product) ? $product->harga_jual : '') }}" required>
                                    </div>
                                    @error('harga_jual')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="id_kategori" class="form-label">Kategori</label>
                                    <select class="form-select" id="id_kategori" name="id_kategori" required>
                                        <option value="">Pilih Kategori...</option>
                                        @foreach($kategori as $kat)
                                            <option value="{{ $kat->id }}"
                                                {{ old('id_kategori', isset($product) ? $product->id_kategori : '') == $kat->id ? 'selected' : '' }}>
                                                {{ $kat->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_kategori')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="stok_produk" class="form-label">Stok</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-boxes"></i></span>
                                        <input type="number" class="form-control" id="stok_produk" name="stok_produk"
                                            value="{{ old('stok_produk', isset($product) ? $product->stok_produk : '') }}" required>
                                    </div>
                                    @error('stok_produk')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Second" {{ old('status', isset($product) ? $product->status : '') == 'Second' ? 'selected' : '' }}>Second</option>
                                        <option value="Baru" {{ old('status', isset($product) ? $product->status : '') == 'Baru' ? 'selected' : '' }}>Baru</option>
                                    </select>
                                    @error('status')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="grade" class="form-label">Grade</label>
                                    <select class="form-select" id="grade" name="grade" required>
                                        <option value="Unggulan" {{ old('grade', isset($product) ? $product->grade : '') == 'Unggulan' ? 'selected' : '' }}>Unggulan</option>
                                        <option value="Standar" {{ old('grade', isset($product) ? $product->grade : 'Standar') == 'Standar' ? 'selected' : '' }}>Standar</option>
                                        <option value="Minus" {{ old('grade', isset($product) ? $product->grade : '') == 'Minus' ? 'selected' : '' }}>Minus</option>
                                    </select>
                                    @error('grade')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="gambar_produk" class="form-label">Gambar Produk</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text"><i class="bi bi-images"></i></span>
                                        <input type="file" class="form-control" id="gambar_produk" name="gambar_produk[]" multiple accept="image/*"
                                            onchange="previewGambar(event)">
                                    </div>
                                    <div id="preview-container" class="row g-2">
                                        @if(isset($product) && $product->gambar)
                                            @foreach($product->gambar as $index => $gambar)
                                                <div class="col-4 image-container" data-image-id="{{ $gambar->id }}">
                                                    <div class="card">
                                                        <img src="{{ asset('storage/' . $gambar->path_gambar) }}" class="card-img-top" style="height: 150px; object-fit: cover;">
                                                        <div class="card-body text-center">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="main_image_existing" value="{{ $gambar->id }}"
                                                                    id="mainImage{{ $gambar->id }}" {{ $gambar->is_main ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="mainImage{{ $gambar->id }}">
                                                                    Jadikan Gambar Utama
                                                                </label>
                                                            </div>
                                                            <button type="button" class="btn btn-danger btn-sm mt-2 remove-image" data-image-id="{{ $gambar->id }}">Hapus</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <small class="text-muted">Pilih salah satu gambar sebagai gambar utama.</small>
                                </div>
                                <div class="col-12">
                                    <label for="deskripsi_produk" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi_produk" name="deskripsi_produk" rows="3" required>{{ old('deskripsi_produk', isset($product) ? $product->deskripsi_produk : '') }}</textarea>
                                    @error('deskripsi_produk')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-plus-circle me-2"></i>
                                        {{ isset($product) ? 'Update Produk' : 'Tambah Produk' }}
                                    </button>
                                    @if(isset($product))
                                        <a href="{{ route('index') }}" class="btn btn-secondary">Batal</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Product Management -->
            <section>
                <div class="admin-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">
                            <i class="bi bi-grid me-2"></i>
                            Kelola Produk
                        </h2>
                        <div class="d-flex gap-2">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari produk..." id="searchProduct">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Thumbnail</th>
                                        <th>Kode SKU</th>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th>Kategori</th>
                                        <th>Stok</th>
                                        <th>Status</th>
                                        <th>Grade</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr>
                                            <td>
                                                @php
                                                    $mainImage = $product->gambarUtama ? $product->gambarUtama->path_gambar : ($product->gambar && count($product->gambar) > 0 ? $product->gambar[0]->path_gambar : null);
                                                @endphp
                                                @if($mainImage)
                                                    <img src="{{ asset('storage/' . $mainImage) }}" alt="Thumbnail" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $product->kode_sku }}</td>
                                            <td>{{ $product->nama_produk }}</td>
                                            <td>Rp {{ number_format($product->harga_jual, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $product->kategori->nama_kategori }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($product->stok_produk < 5)
                                                    <span class="badge bg-danger">{{ $product->stok_produk }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ $product->stok_produk }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($product->status == 'Baru')
                                                    <span class="badge bg-success">Baru</span>
                                                @elseif($product->status == 'Second')
                                                    <span class="badge bg-secondary">Second</span>
                                                @else
                                                    <span class="badge bg-light text-dark">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($product->grade == 'Unggulan')
                                                    <span class="badge bg-success">Unggulan</span>
                                                @elseif($product->grade == 'Standar')
                                                    <span class="badge bg-primary">Standar</span>
                                                @elseif($product->grade == 'Minus')
                                                    <span class="badge bg-warning text-dark">Minus</span>
                                                @else
                                                    <span class="badge bg-light text-dark">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('index', ['edit' => $product->id]) }}"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('destroy', $product->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="confirmDeleteProduct(this)">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    Belum ada produk
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    @include('partials.footerAdmin')

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        (function() {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()

        function previewGambar(event) {
            const previewContainer = document.getElementById('preview-container');
            const files = Array.from(event.target.files);
            previewContainer.innerHTML = '';
            files.forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4 image-container';
                    col.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;">
                            <div class="card-body text-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="main_image_new" value="${i}"
                                        id="mainImageNew${i}" ${i === 0 ? 'checked' : ''}>
                                    <label class="form-check-label" for="mainImageNew${i}">
                                        Jadikan Gambar Utama
                                    </label>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm mt-2 remove-image-new" data-index="${i}">Hapus</button>
                            </div>
                        </div>
                    `;
                    previewContainer.appendChild(col);
                }
                reader.readAsDataURL(file);
            });

            setTimeout(() => {
                document.querySelectorAll('.remove-image-new').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const idx = parseInt(this.getAttribute('data-index'));
                        files.splice(idx, 1);

                        const dataTransfer = new DataTransfer();
                        files.forEach(f => dataTransfer.items.add(f));
                        event.target.files = dataTransfer.files;
                        previewGambar({ target: event.target });
                    });
                });
            }, 100);
        }

        document.getElementById('preview-container').addEventListener('click', function(event) {

            if (event.target.classList.contains('remove-image')) {
                event.preventDefault();
                event.stopPropagation();

                const button = event.target;
                const imageId = button.dataset.imageId;
                const container = button.closest('.image-container');

                window.confirmDelete('Apakah Anda yakin ingin menghapus gambar ini?', 'Konfirmasi Hapus')
                    .then((result) => {
                        if (!result.isConfirmed) return;

                        button.disabled = true;
                        const url = '{{ route("admin.delete.image", ["id" => ":imageId"]) }}';
                        const finalUrl = url.replace(':imageId', imageId);

                        fetch(finalUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                            .then(response => response.json())
                            .then(data => {
                                button.disabled = false;

                                if (data.success) {
                                    container.remove();

                                    const mainImageRadio = document.querySelector('input[name="main_image_existing"]:checked');
                                    if (!mainImageRadio && document.querySelectorAll('.image-container').length > 0) {

                                        document.querySelector('.image-container input[type="radio"]').checked = true;
                                    }
                                    window.showSuccess('Gambar berhasil dihapus');
                                } else {
                                    window.showError('Gagal menghapus gambar: ' + (data.message || 'Unknown error'));
                                }
                            })
                            .catch(error => {
                                button.disabled = false;
                                console.error('Error during fetch:', error);
                                window.showError('Terjadi kesalahan jaringan atau server.');
                            });
                    });
            }

            if (event.target.name === 'main_image_existing') {

                const newImageRadio = document.querySelector('input[name="main_image_new"]:checked');
                if (newImageRadio) {
                    newImageRadio.checked = false;
                }
            }
        });

        document.getElementById('searchProduct').addEventListener('keyup', function() {
            let searchQuery = this.value.toLowerCase();
            let tableRows = document.querySelectorAll('tbody tr');

            tableRows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchQuery) ? '' : 'none';
            });
        });

        function confirmDeleteProduct(button) {
            window.confirmDelete('Apakah Anda yakin ingin menghapus produk ini?', 'Konfirmasi Hapus')
                .then((result) => {
                    if (result.isConfirmed) {
                        button.form.submit();
                    }
                });
        }
    </script>
</body>

</html>
