<div class="card shadow-sm border-0 h-100 position-relative overflow-hidden" style="border-radius: 12px;">
    {{-- Hiasan Header (Opsional untuk estetika) --}}
    <div class="position-absolute top-0 start-0 w-100 bg-primary" style="height: 4px;"></div>

    <div class="card-header bg-white border-bottom-0 pt-4 ps-4 pe-4 pb-2">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-user"></i>
                </span>
                {{ isset($readOnly) && $readOnly ? 'Profil Pelanggan' : 'Edit Pelanggan' }}
            </h6>
            @if(isset($readOnly) && $readOnly)
                <span class="badge bg-light text-secondary border">Kode: {{ $pelanggan->kode_customer ?? '-' }}</span>
            @endif
        </div>
    </div>

    <div class="card-body p-4">
        @if(!isset($readOnly) || !$readOnly)
        <form id="customerForm" method="POST" action="{{ route('admin.customers.update', $pelanggan->id) }}" data-validate-form>
            @csrf
            @method('PUT')
        @endif

        {{-- LOGIKA GRID: Jika ReadOnly (di sidebar), pakai col-12. Jika Edit (full), pakai col-md-6 --}}
        @php
            $colClass = (isset($readOnly) && $readOnly) ? 'col-12' : 'col-md-6';
        @endphp

        <div class="row g-3">
            {{-- Nama --}}
            <div class="{{ $colClass }}">
                <label class="form-label fw-semibold text-secondary small">Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" class="form-control" maxlength="50" name="nama" value="{{ old('nama', $pelanggan->nama ?? '') }}"
                    {{ isset($readOnly) && $readOnly ? 'readonly style=background-color:#f8f9fa;' : 'required' }}>
            </div>

            {{-- No Telp --}}
            <div class="{{ $colClass }}">
                <label class="form-label fw-semibold text-secondary small">No. Telepon <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="no_telp" value="{{ old('no_telp', $pelanggan->no_telp ?? '') }}"
                    {{ isset($readOnly) && $readOnly ? 'readonly style=background-color:#f8f9fa;' : 'required' }}>
            </div>

            {{-- Jenis Kelamin --}}
            <div class="{{ $colClass }}">
                <label class="form-label fw-semibold text-secondary small">Jenis Kelamin <span class="text-danger">*</span></label>
                @if(isset($readOnly) && $readOnly)
                    <input type="text" class="form-control" value="{{ $pelanggan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}" readonly style="background-color:#f8f9fa;">
                @else
                    <select class="form-select" name="jenis_kelamin">
                        <option value="L" {{ $pelanggan->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $pelanggan->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                @endif
            </div>

            {{-- NIK --}}
            <div class="{{ $colClass }}">
                <label class="form-label fw-semibold text-secondary small">NIK</label>
                <input type="text" class="form-control" name="identitas" value="{{ old('identitas', $pelanggan->identitas ?? '') }}"
                    {{ isset($readOnly) && $readOnly ? 'readonly style=background-color:#f8f9fa;' : '' }}>
            </div>

            {{-- Email (Full Width) --}}
            {{-- <div class="col-12">
                <label class="form-label fw-semibold text-secondary small">Email</label>
                <input type="email" class="form-control" name="email" value="{{ old('email', $pelanggan->email ?? '') }}"
                    {{ isset($readOnly) && $readOnly ? 'readonly style=background-color:#f8f9fa;' : '' }}>
            </div> --}}

            {{-- Alamat (Full Width) --}}
            <div class="col-12">
                <label class="form-label fw-semibold text-secondary small">Alamat Lengkap</label>
                <textarea class="form-control" name="alamat" rows="3" maxlength="100" {{ isset($readOnly) && $readOnly ? 'readonly style=background-color:#f8f9fa;' : '' }}>{{ old('alamat', $pelanggan->alamat ?? '') }}</textarea>
            </div>

            {{-- Keterangan (Full Width) --}}
            <div class="col-12">
                <label class="form-label fw-semibold text-secondary small">Keterangan</label>
                <textarea class="form-control" name="keterangan" rows="3" {{ isset($readOnly) && $readOnly ? 'readonly style=background-color:#f8f9fa;' : '' }}>{{ old('keterangan', $pelanggan->keterangan ?? '') }}</textarea>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        {{-- <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
            @if(isset($readOnly) && $readOnly)
                <a href="{{ route('admin.customers.index') }}" class="btn btn-light border text-secondary px-4">Kembali</a>
                <a href="{{ route('admin.customers.edit', $pelanggan->id) }}" class="btn btn-primary px-4"><i class="fa-solid fa-pen-to-square me-1"></i> Edit</a>
            @else
                <a href="{{ route('admin.customers.index') }}" class="btn btn-light border text-secondary px-4">Batal</a>
                <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-save me-1"></i> Simpan</button>
            @endif
        </div> --}}

        @if(!isset($readOnly) || !$readOnly)
        </form>
        @endif
    </div>
</div>
