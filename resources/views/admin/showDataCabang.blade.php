@extends('layouts.admin')

@section('title', 'Detail Cabang')

@push('page-actions')
<a href="{{ route('admin.branches.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-arrow-left"></i>
    <span>Kembali</span>
</a>
<a href="{{ route('admin.branches.edit', $branch->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
    <i class="fas fa-pen"></i>
    <span>Edit</span>
</a>
@endpush

@section('content')
@php
    $formatCurrency = fn ($value) => 'Rp' . number_format($value ?? 0, 0, ',', '.');
@endphp

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-store me-2 text-primary"></i>
                    Informasi Cabang
                </h6>

                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light mb-3" style="width: 120px; height: 120px;">
                        <i class="fa-solid fa-store fa-3x text-muted"></i>
                    </div>
                    <h4 class="fw-bold mb-2">{{ $branch->nama }}</h4>
                    <div class="d-flex justify-content-center gap-2">
                        @if($branch->is_active)
                            <span class="badge bg-success text-white">
                                <i class="fa-solid fa-circle-check me-1"></i>Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary text-white">
                                <i class="fa-solid fa-circle-xmark me-1"></i>Non-Aktif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="small text-muted">
                    <div class="d-flex justify-content-between">
                        <span>Status Cabang</span>
                        <span class="fw-semibold">{{ $branch->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Terakhir Diperbarui</span>
                        <span class="fw-semibold">{{ $branch->updated_at?->format('d M Y H:i') ?? '-' }}</span>
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <p class="text-muted small mb-1">Kode Cabang</p>
                    <div class="bg-light rounded p-2 fw-bold fs-5 text-center">BR-{{ str_pad($branch->id, 3, '0', STR_PAD_LEFT) }}</div>
                </div>

                <div class="mb-3">
                    <p class="text-muted small mb-1">Alamat Lengkap</p>
                    <div class="bg-light rounded p-2 fw-semibold">{{ $branch->alamat ?? '-' }}</div>
                </div>

                <div class="mb-3">
                    <p class="text-muted small mb-1">Nomor Telepon</p>
                    <div class="bg-light rounded p-2 fw-semibold">
                        <i class="fa-solid fa-phone me-1 text-muted"></i>{{ $branch->nomor_telepon ?? '-' }}
                    </div>
                </div>

                <div class="mb-3">
                    <p class="text-muted small mb-1">Email</p>
                    <div class="bg-light rounded p-2 fw-semibold">
                        <i class="fa-solid fa-envelope me-1 text-muted"></i>{{ $branch->email ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-map-location-dot me-2 text-warning"></i>
                    Lokasi & Deskripsi
                </h6>

                @if($branch->link_maps)
                    <div class="mb-4">
                        <p class="text-muted small mb-2">Lokasi Google Maps</p>
                        <a href="{{ $branch->link_maps }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fa-solid fa-external-link-alt me-1"></i>
                            Buka di Google Maps
                        </a>
                    </div>
                @endif

                <div>
                    <p class="text-muted small mb-2">Deskripsi Cabang</p>
                    @if($branch->deskripsi)
                        <div class="bg-light rounded p-3 text-muted" style="white-space: pre-line;">
                            {{ $branch->deskripsi }}
                        </div>
                    @else
                        <div class="bg-light rounded p-3 text-muted">
                            <i class="fa-solid fa-info-circle me-1"></i>Belum ada deskripsi yang ditambahkan.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="fw-bold text-dark mb-3">
                    <i class="fa-solid fa-clock me-2 text-success"></i>
                    Jam Operasional
                </h6>

                <div class="list-group list-group-flush">
                    @php
                        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                        $jamOperasionalData = [];
                        if (isset($branch->jamOperasional)) {
                            foreach ($branch->jamOperasional as $jam) {
                                $jamOperasionalData[$jam->hari] = $jam;
                            }
                        }
                    @endphp

                    @foreach($hariList as $hari)
                        <div class="list-group-item px-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($jamOperasionalData[$hari]->is_buka ?? true)
                                            <i class="fa-solid fa-circle-check text-success"></i>
                                        @else
                                            <i class="fa-solid fa-circle-xmark text-danger"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-medium">{{ $hari }}</div>
                                        @if(!empty($jamOperasionalData[$hari]->catatan))
                                            <div class="small text-muted mt-1">
                                                <i class="fa-solid fa-info-circle me-1"></i>{{ $jamOperasionalData[$hari]->catatan }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    @if($jamOperasionalData[$hari]->is_buka ?? true)
                                        <span class="badge bg-light text-dark fw-semibold">
                                            {{ \Carbon\Carbon::parse($jamOperasionalData[$hari]->jam_buka ?? '08:00')->format('H:i') . ' - ' . \Carbon\Carbon::parse($jamOperasionalData[$hari]->jam_tutup ?? '21:00')->format('H:i') }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary text-white">Tutup</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
