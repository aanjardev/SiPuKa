@extends('layouts.admin')

@section('title', isset($readOnly) && $readOnly ? 'Detail Data Karyawan' : (isset($employee) ? 'Edit Data Karyawan' : 'Tambah Data Karyawan'))
@section('disable_alerts', true)

@push('page-actions')
    <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Kembali</span>
    </a>
    @if(isset($readOnly) && $readOnly)
        <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-pen-to-square"></i>
            <span>Edit</span>
        </a>
    @else
        <button type="submit" form="employeeForm" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-save"></i>
            <span>{{ isset($employee) ? 'Simpan Perubahan' : 'Simpan Karyawan' }}</span>
        </button>
    @endif
@endpush

@section('content')
{{-- UBAHAN: Hapus justify-content-center agar layout mulai dari kiri --}}
<div class="row">
    {{-- UBAHAN: Ubah jadi col-12 agar Card memenuhi lebar layar --}}
    <div class="col-12">

        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            {{-- Card Header --}}
            <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-id-card-clip me-2 text-primary"></i>
                    @if(isset($readOnly) && $readOnly)
                        Detail Informasi Karyawan
                    @elseif(isset($employee))
                        Edit Informasi Karyawan
                    @else
                        Tambah Karyawan Baru
                    @endif
                </h5>
                <p class="text-muted small mt-1">
                    @if(isset($readOnly) && $readOnly)
                        {{-- Mode lihat data (Read-only). --}}
                    @else
                        Silakan isi data diri karyawan dengan lengkap dan benar.
                    @endif
                </p>
            </div>

            <div class="card-body p-4">

                {{-- Pembuka Form --}}
                @if(!(isset($readOnly) && $readOnly))
                    <form id="employeeForm" method="POST" action="{{ isset($employee) ? route('admin.employees.update', $employee->id) : route('admin.employees.store') }}" data-validate-form>
                    @csrf
                    @if(isset($employee))
                        @method('PUT')
                    @endif

                @endif
                    <input type="hidden" name="redirect_to" value="{{ request('from') }}">

                    {{-- Baris 1: Nama & NIK --}}
                    {{-- Karena Card sudah full width, col-md-6 ini akan membagi layar jadi 2 kolom yang proporsional --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-user"></i></span>
                                <input type="text"
                                    class="form-control border-start-0 ps-2 required-field @error('nama_lengkap') is-invalid @enderror"
                                    name="nama_lengkap"
                                    style="height: 45px;" maxlength="50"

                                    value="{{ old('nama_lengkap', $employee->nama_lengkap ?? '') }}"
                                    placeholder="Nama lengkap sesuai KTP"
                                    {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}
                                    data-error-message="Nama lengkap wajib diisi"
                                    {{ !isset($readOnly) || !$readOnly ? 'autofocus' : '' }}>

                            </div>
                            <div class="invalid-feedback">
                                @error('nama_lengkap')
                                    {{ $message }}
                                @else
                                    Nama lengkap wajib diisi
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">NIK <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-fingerprint"></i></span>
                                <input type="text"
                                    class="form-control border-start-0 ps-2 required-field @error('nik') is-invalid @enderror"
                                    name="nik"
                                    style="height: 45px;"
                                    value="{{ old('nik', $employee->nik ?? '') }}"
                                    placeholder="16 Digit NIK"
                                    {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}
                                    data-error-message="NIK wajib diisi" maxlength="16">
                            </div>
                            <div class="invalid-feedback">
                                @error('nik')
                                    {{ $message }}
                                @else
                                    NIK wajib diisi
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2: Jabatan & No Telp --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Jabatan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-briefcase"></i></span>
                                <input type="text"
                                    class="form-control border-start-0 ps-2 required-field @error('jabatan') is-invalid @enderror"
                                    name="jabatan"
                                    style="height: 45px;"
                                    value="{{ old('jabatan', $employee->jabatan ?? '') }}"
                                    placeholder="Masukkan jabatan"
                                    {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}
                                    data-error-message="Jabatan wajib diisi">
                            </div>
                            <div class="invalid-feedback">
                                @error('jabatan')
                                    {{ $message }}
                                @else
                                    Jabatan wajib diisi
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">No. Telepon <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-phone"></i></span>
                                {{-- Input tampilan (formatted) --}}
                                <input type="text"
                                    class="form-control border-start-0 ps-2 required-field @error('nomor_telepon') is-invalid @enderror"
                                    id="nomor_telepon_display"
                                    style="height: 45px;"
                                    value="{{ old('nomor_telepon', $employee->nomor_telepon ?? '') }}"
                                    placeholder="08xx-xxxx-xxxx"
                                    {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}
                                    data-error-message="Nomor telepon wajib diisi">
                                {{-- Input hidden untuk dikirim ke backend (hanya digit) --}}
                                <input type="hidden"
                                    name="nomor_telepon"
                                    id="nomor_telepon"
                                    value="{{ old('nomor_telepon', $employee->nomor_telepon ?? '') }}">
                            </div>
                            <div class="invalid-feedback">
                                @error('nomor_telepon')
                                    {{ $message }}
                                @else
                                    Nomor telepon harus berupa angka dan diawali dengan 0, 62, atau +62.
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Baris 3: Gaji & Tanggal Masuk --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Gaji Pokok</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3">Rp</span>
                                <input type="text"
                                    class="form-control border-start-0 ps-2 rupiah-mask numeric-only @error('gaji') is-invalid @enderror"
                                    name="gaji"
                                    style="height: 45px;"
                                    value="{{ old('gaji', $employee->gaji ?? '') }}"
                                    placeholder="0" maxlength="11"
                                    {{ isset($readOnly) && $readOnly ? 'readonly' : '' }} >
                            </div>
                            <div class="invalid-feedback">
                                @error('gaji')
                                    {{ $message }}
                                @else
                                    Gaji harus berupa angka yang valid
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Tanggal Masuk <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-regular fa-calendar"></i></span>
                                <input type="date"
                                    class="form-control border-start-0 ps-2 required-field @error('tanggal_masuk') is-invalid @enderror"
                                    name="tanggal_masuk"
                                    style="height: 45px;"
                                    value="{{ old('tanggal_masuk', isset($employee) && $employee->tanggal_masuk ? $employee->tanggal_masuk->format('Y-m-d') : '') }}"
                                    {{ isset($readOnly) && $readOnly ? 'readonly' : 'required' }}
                                    data-error-message="Tanggal masuk wajib diisi">
                            </div>
                            <div class="invalid-feedback">
                                @error('tanggal_masuk')
                                    {{ $message }}
                                @else
                                    Tanggal masuk wajib diisi
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Baris 4: Status, Tanggal Keluar & Alamat --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Status Karyawan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-toggle-on"></i></span>
                                @if(isset($readOnly) && $readOnly)
                                    <input type="text" class="form-control border-start-0 ps-2"
                                        value="{{ $employee->status == 'aktif' ? 'Aktif' : 'Non Aktif' }}"
                                        readonly style="height: 45px;">
                                @else
                                    <select class="form-select border-start-0 ps-2 required-field @error('status') is-invalid @enderror" name="status" style="height: 45px;" required data-error-message="Status karyawan wajib dipilih">
                                        <option value="aktif" {{ old('status', $employee->status ?? 'aktif') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                                        <option value="non-aktif" {{ old('status', $employee->status ?? '') === 'non-aktif' ? 'selected' : '' }}>Non Aktif</option>
                                    </select>
                                @endif
                            </div>
                            @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Tanggal Keluar</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-regular fa-calendar-xmark"></i></span>
                                <input type="date"
                                    class="form-control border-start-0 ps-2 @error('tanggal_keluar') is-invalid @enderror"
                                    name="tanggal_keluar"
                                    style="height: 45px;"
                                    value="{{ old('tanggal_keluar', isset($employee) && $employee->tanggal_keluar ? $employee->tanggal_keluar->format('Y-m-d') : '') }}"
                                    {{ isset($readOnly) && $readOnly ? 'readonly' : '' }}>
                            </div>
                            @error('tanggal_keluar') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label fw-medium text-secondary small">Alamat Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-map-location-dot"></i></span>
                                <textarea class="form-control border-start-0 ps-2 @error('alamat') is-invalid @enderror"
                                    name="alamat"
                                    rows="3"
                                    placeholder="Masukkan alamat domisili..."
                                    {{ isset($readOnly) && $readOnly ? 'readonly' : '' }}>{{ old('alamat', $employee->alamat ?? '') }}</textarea>
                            </div>
                            @error('alamat') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                @if(!(isset($readOnly) && $readOnly))
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    
    .form-control[readonly],
    .form-select[disabled] {
        background-color: #f9fafb; 
        color: #6c757d;
        cursor: not-allowed;
    }
    
    .input-group:focus-within .input-group-text {
        border-color: #86b7fe;
    }
    .input-group:focus-within .form-control,
    .input-group:focus-within .form-select {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tanggalKeluar = document.querySelector('input[name="tanggal_keluar"]');
        const statusSelect = document.querySelector('select[name="status"]');
        if (!tanggalKeluar || !statusSelect) return;

        function syncStatusToTanggalKeluar() {
            if (tanggalKeluar.value) {
                statusSelect.value = 'non-aktif';
            }
        }

        tanggalKeluar.addEventListener('change', syncStatusToTanggalKeluar);
        syncStatusToTanggalKeluar();
    });
</script>
@endpush
