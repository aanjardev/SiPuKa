@extends('layouts.admin')

@section('title', 'Kategori Harga')

@push('page-actions')
<a href="{{ route('admin.price-categories.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Tambah Rentang</a>
@endpush

@section('content')
<div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead><tr><th>Urutan</th><th>Nama</th><th>Rentang Harga</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->urutan }}</td>
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
