@extends('layouts.admin')
@section('title', 'Partner')
@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div><h1 class="h3 mb-1">Partner</h1><p class="text-muted mb-0">Kelola logo dan nama partner di beranda.</p></div>
        <a href="{{ route('admin.partners.create') }}" class="btn btn-primary">Tambah Partner</a>
    </div>
    <div class="card"><div class="card-body">
        <p class="text-muted small">Partner aktif ditampilkan sesuai urutan penambahan. Saat belum ada data, beranda menampilkan contoh partner fiktif. Contoh otomatis diganti setelah partner pertama ditambahkan.</p>
        <div class="table-responsive"><table class="table align-middle">
            <thead><tr><th>Logo</th><th>Nama Partner</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($partners as $partner)
                <tr>
                    <td><img src="{{ $partner->logo_url }}" alt="Logo {{ $partner->nama }}" width="100" height="60" style="object-fit:contain"></td>
                    <td>{{ $partner->nama }}</td>
                    <td>{{ $partner->is_active ? 'Aktif' : 'Disembunyikan' }}</td>
                    <td><div class="d-flex gap-2">
                        <a href="{{ route('admin.partners.edit', $partner) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" data-prevent-double-submit onsubmit="return confirm('Hapus partner ini?')">
                            @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                        </form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-4">Belum ada partner. Tambahkan nama dan logo untuk mengganti contoh di beranda.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        {{ $partners->links('pagination::bootstrap-5') }}
    </div></div>
</div>
@endsection
