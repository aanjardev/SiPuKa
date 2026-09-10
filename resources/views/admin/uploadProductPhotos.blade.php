@extends('layouts.admin')

@section('title', 'Upload Foto Produk')

@push('page-actions')
    <a href="{{ route('admin.products.photos') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" id="btnKembali">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
@endpush

@section('content')

{{-- Custom CSS for modern form styling --}}
@push('styles')
<style>

    .card-modern { border: 1px solid #f0f0f0; border-radius: 16px; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04); transition: all 0.3s ease; }
    .card-header-modern { background-color: #fff; border-bottom: 1px solid #f0f0f0; padding: 20px 24px; border-radius: 16px 16px 0 0 !important; }


    .upload-box { width: 160px; height: 160px; border: 2px dashed #dee2e6; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; background: #fafafa; position: relative; }
    .upload-box:hover { border-color: #86b7fe; background: #f8f9ff; }
    .upload-box.has-image { border: 2px solid #86b7fe; background: #fff; }
    .upload-box .empty-state { text-align: center; color: #6c757d; }
    .upload-box .preview { width: 100%; height: 100%; position: absolute; top: 0; left: 0; border-radius: 10px; overflow: hidden; }
    .upload-box .preview img { width: 100%; height: 100%; object-fit: cover; }
    .upload-box .controls { position: absolute; top: 8px; right: 8px; }
    .upload-box .main-choice { position: absolute; bottom: 8px; left: 8px; background: rgba(255,255,255,0.9); padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; }


    .upload-status { padding: 10px 15px; border-radius: 8px; margin-top: 15px; font-weight: 500; }
    .upload-status.success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    .upload-status.error { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
</style>
@endpush

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-modern mb-4">
            <div class="card-header-modern d-flex align-items-center gap-3">
                <i class="fa-solid fa-images fa-lg text-primary"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Upload Foto Produk</h6>
                    <p class="text-muted small mb-0">Tambahkan gambar untuk produk <strong class="text-dark">{{ $product->nama_produk }}</strong> &bull; SKU: <span class="badge bg-light text-dark border">{{ $product->kode_sku }}</span></p>
                </div>
            </div>

            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger mb-4 border-0 shadow-sm">
                        <h5 class="alert-heading small fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Ada Kesalahan!</h5>
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="upload-photos-form" action="{{ route('admin.products.photos.uploadStore', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="images[]" id="hidden-images-input" class="d-none" multiple>
                    <input type="hidden" name="main_image" id="hidden-main-image">

                    <div class="mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fa-solid fa-circle-info text-primary"></i>
                            <span class="small text-muted">Maksimal <strong>10 gambar</strong> (Max 5MB/file). Klik kotak di bawah untuk memilih gambar.</span>
                        </div>
                    </div>

                    <div id="upload-grid" class="d-flex flex-wrap gap-3 mb-4">
                        {{-- Upload boxes akan ditambahkan via JavaScript --}}
                    </div>

                    <div id="upload-status" class="upload-status" style="display: none;"></div>
                </form>

                {{-- Gambar Saat Ini (Komentar Asli) --}}
                {{--
                <hr class="my-5 opacity-25">
                <h6 class="fw-bold text-dark mb-3">Gambar Saat Ini</h6>
                <div id="current-images" class="d-flex flex-wrap gap-3">
                    @forelse ($product->photos as $photo)
                    <div class="card border position-relative" style="width: 160px;" data-image-id="{{ $photo->id }}">
                        <img src="{{ asset('storage/' . $photo->path) }}" class="card-img-top" style="height: 160px; object-fit: cover;" alt="Gambar produk">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if ($photo->is_main)
                                    <span class="badge bg-primary">Utama</span>
                                    @else
                                    <span class="badge bg-secondary">Tambahan</span>
                                    @endif
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info btn-set-main" data-image-id="{{ $photo->id }}" title="Set sebagai Gambar Utama">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    <button class="btn btn-outline-danger remove-image" data-image-id="{{ $photo->id }}" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="w-100 text-center py-4 bg-light rounded border border-dashed">
                        <i class="fas fa-image fa-2x text-muted mb-2"></i>
                        <p class="text-muted small mb-0">Belum ada gambar yang diunggah.</p>
                    </div>
                    @endforelse
                </div>
                --}}

            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-modern position-sticky" style="top: 20px; z-index: 10;">
            <div class="card-header-modern bg-primary bg-opacity-10 border-primary border-opacity-10">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-camera"></i> Aksi Upload
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <p class="small text-muted mb-3">Pilih gambar yang ingin diunggah, lalu tentukan gambar utama (opsional).</p>
                </div>

                <div class="d-grid gap-2">
                    <button id="save-uploads" type="button" class="btn btn-primary w-100 py-2 rounded-3 fw-medium shadow-sm" disabled>
                        <i class="fas fa-save me-2"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.products.photos') }}" class="btn btn-outline-secondary w-100 py-2 rounded-3 fw-medium">
                        <i class="fas fa-times me-2"></i> Batal
                    </a>
                </div>

                <div class="mt-3 pt-3 border-top">
                    <div class="small text-muted">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fa-solid fa-lightbulb text-warning"></i>
                            <span><strong>Tip:</strong> Gambar utama akan ditampilkan pertama kali di halaman produk.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
    <link rel="stylesheet" href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/upload-photo.css') }}">
@endpush

@push('scripts')
<script>
    window.productImageConfigs = window.productImageConfigs || [];
    window.productImageConfigs.push({
        gridId: 'upload-grid',
        formId: 'upload-photos-form',
        hiddenInputId: 'hidden-images-input',
        hiddenMainInputId: 'hidden-main-image',
        saveButtonId: 'save-uploads',
        statusId: 'upload-status',
        maxBoxes: 10,
        maxFileSize: 5242880,
        allowMainChoice: true,
        requireFilesOnSubmit: true,
        existingImages: [],
        initialEmptyBoxes: 2
    });
</script>
<script src="{{ asset('js/productImages.js') }}"></script>
@endpush
