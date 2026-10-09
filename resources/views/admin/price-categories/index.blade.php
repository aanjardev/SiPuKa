@extends('layouts.admin')

@section('title', 'Kategori Harga')

@push('page-actions')
<a href="{{ route('admin.price-categories.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Tambah Rentang</a>
@endpush

@push('styles')
<style>
.price-order-handle { cursor: grab; touch-action: none; }
.price-order-handle:disabled { cursor: default; touch-action: auto; opacity: .35; }
.price-order-handle:active { cursor: grabbing; }
.price-order-dragging { opacity: .55; outline: 2px solid var(--admin-yellow, #f4c400); }
</style>
@endpush
@section('content')
<div class="d-flex flex-wrap gap-2 mb-3">
    <button type="button" id="price-order-edit" class="btn btn-outline-secondary btn-sm" aria-controls="price-category-order" aria-expanded="false" @disabled($categories->count() < 2)><i class="fas fa-sort me-1" aria-hidden="true"></i>Ubah Urutan</button>
    <button type="button" id="price-order-save" class="btn btn-primary btn-sm" hidden><i class="fas fa-save me-1" aria-hidden="true"></i>Simpan Urutan</button>
    <button type="button" id="price-order-cancel" class="btn btn-outline-secondary btn-sm" hidden>Batal</button>
</div>
<p class="text-muted mb-2" id="price-order-help">Klik Ubah Urutan untuk mengaktifkan drag and drop kategori harga.</p>
<p id="price-order-status" class="small text-muted" role="status" aria-live="polite"></p>
<div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th aria-label="Atur urutan"></th><th>Nama</th><th>Rentang Harga</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
                <tbody id="price-category-order" data-order-url="{{ route('admin.price-categories.order') }}">
                @forelse($categories as $category)
                    <tr data-category-id="{{ $category->id }}">
                        <td><button type="button" class="btn btn-sm btn-outline-secondary price-order-handle" disabled aria-label="Pindahkan {{ $category->nama }}" aria-describedby="price-order-help" title="Geser untuk mengatur urutan"><i class="fas fa-grip-vertical" aria-hidden="true"></i></button></td>
                        <td><strong>{{ $category->nama }}</strong><div class="small text-muted">{{ $category->slug }}</div></td>
                        <td>Rp {{ number_format($category->harga_minimum, 0, ',', '.') }} — {{ $category->harga_maksimum === null ? 'tanpa batas' : 'Rp ' . number_format($category->harga_maksimum, 0, ',', '.') }}</td>
                        <td><span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('admin.price-categories.edit', $category) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-pen"></i></a>
                            <form action="{{ route('admin.price-categories.destroy', $category) }}" method="POST" class="d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus kategori harga ini?')"><i class="fas fa-trash"></i></button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada kategori harga.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/price-category-order.js') }}?v=2"></script>
@endpush
