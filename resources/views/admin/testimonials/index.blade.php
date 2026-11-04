@extends('layouts.admin')
@section('title', 'Testimoni')
@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div><h1 class="h3 mb-1">Testimoni</h1><p class="text-muted mb-0">Kelola ulasan pilihan pelanggan beserta sumber aslinya.</p></div>
        <div class="d-flex gap-2"><a href="{{ route('testimonials') }}" class="btn btn-outline-secondary" target="_blank" rel="noopener">Lihat Halaman</a><a href="{{ route('admin.testimonials.create') }}" class="btn btn-primary">Tambah Testimoni</a></div>
    </div>
    <div class="card"><div class="card-body">
        <div class="table-responsive"><table class="table align-middle">
            <thead><tr><th>Pelanggan / Ulasan</th><th>Sumber</th><th>Screenshot</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @forelse($testimonials as $testimonial)
                <tr>
                    <td><strong>{{ $testimonial->customer_name }}</strong><p class="small mb-0">{{ \Illuminate\Support\Str::limit($testimonial->review_text, 160) }}</p>@if($testimonial->rating !== null)<small>Rating {{ $testimonial->rating }}/5</small>@endif</td>
                    <td><a href="{{ $testimonial->source_url }}" target="_blank" rel="noopener noreferrer">{{ $testimonial->source_name }}</a>@if($testimonial->reviewed_at)<div class="small text-muted">{{ $testimonial->reviewed_at->format('d/m/Y') }}</div>@endif</td>
                    <td>@if($testimonial->screenshot_path)<img src="{{ $testimonial->screenshot_preview_url }}" alt="Screenshot ulasan {{ $testimonial->customer_name }}" width="100" height="90" style="object-fit:contain" loading="lazy">@else<span class="text-muted">Tanpa screenshot</span>@endif</td>
                    <td>{{ $testimonial->is_active ? 'Ditampilkan' : 'Disembunyikan' }}</td>
                    <td><div class="d-flex gap-2"><a href="{{ route('admin.testimonials.edit', $testimonial) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST" data-prevent-double-submit onsubmit="return confirm('Hapus testimoni ini?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button></form>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-4">Belum ada testimoni. Tambahkan ulasan pelanggan melalui tombol di atas.</td></tr>
            @endforelse
            </tbody>
        </table></div>
        {{ $testimonials->links('pagination::bootstrap-5') }}
    </div></div>
</div>
@endsection
