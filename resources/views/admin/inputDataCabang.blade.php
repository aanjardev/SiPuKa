@extends('layouts.admin')

@section('title', isset($branch) ? (isset($isShow) && $isShow ? 'Detail Data Cabang' : 'Edit Data Cabang') : 'Tambah Data Cabang')

@push('page-actions')
    <a href="{{ route('admin.branches.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali</span>
    </a>
    @if(!isset($isShow) || !$isShow)
    <button type="submit" form="branchForm" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="fa-solid fa-save"></i>
        <span>{{ isset($branch) ? 'Simpan Perubahan' : 'Simpan Cabang' }}</span>
    </button>
    @endif
@endpush

@section('content')

<form
    id="branchForm"
    action="{{ isset($branch) && (!isset($isShow) || !$isShow) ? route('admin.branches.update', $branch->id) : route('admin.branches.store') }}"
    method="POST"
    {{ (!isset($isShow) || !$isShow) ? 'data-validate-form' : '' }}
>
    @csrf
    @if(isset($branch) && (!isset($isShow) || !$isShow))
    @method('PUT')
    @endif

    <div class="row">
        {{-- KOLOM KIRI: Informasi Utama --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-store me-2 text-primary"></i>
                        {{ isset($isShow) && $isShow ? 'Detail Cabang' : (isset($branch) ? 'Informasi Cabang' : 'Identitas Cabang Baru') }}
                    </h6>
                    <p class="text-muted small mt-1">
                        {{ isset($isShow) && $isShow ? 'Informasi detail cabang (mode tampil)' : 'Masukkan detail nama dan alamat fisik cabang.' }}
                    </p>
                </div>

                <div class="card-body p-4">
                    {{-- Nama Cabang --}}
                    <div class="mb-4">
                        <label for="namaCabang" class="form-label fw-medium text-secondary small">Nama Cabang <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-shop"></i>
                            </span>
                            <input type="text"
                                class="form-control border-start-0 ps-2 required-field @error('nama') is-invalid @enderror"
                                id="namaCabang"
                                name="nama"
                                style="height: 45px;"
                                value="{{ old('nama', $branch->nama ?? '') }}"
                                placeholder="Contoh: Dinoyo Kamera Pusat"
                                {{ isset($isShow) && $isShow ? 'readonly' : 'required' }}
                                @if(!isset($isShow) || !$isShow)
                                data-error-message="Nama cabang wajib diisi"
                                @endif
                                autofocus>
                        </div>
                        @error('nama')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @else
                        <div class="invalid-feedback">Nama cabang wajib diisi</div>
                        @enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="mb-3">
                        <label for="Alamat" class="form-label fw-medium text-secondary small">Alamat Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-map-location-dot"></i>
                            </span>
                            <textarea
                                class="form-control border-start-0 ps-2 required-field @error('alamat') is-invalid @enderror"
                                id="Alamat"
                                name="alamat"
                                rows="2" maxlength="150"
                                placeholder="Masukkan alamat lengkap cabang (Jalan, No, RT/RW, Kota)..."
                                {{ isset($isShow) && $isShow ? 'readonly' : 'required' }}
                                @if(!isset($isShow) || !$isShow)
                                data-error-message="Alamat lengkap wajib diisi"
                                @endif>{{ old('alamat', $branch->alamat ?? '') }}</textarea>
                        </div>
                        @error('alamat')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @else
                        <div class="invalid-feedback">Alamat lengkap wajib diisi</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium text-secondary small">Email Cabang</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-envelope"></i>
                            </span>
                            <input type="email"
                                class="form-control border-start-0 ps-2 @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                style="height: 45px;"
                                value="{{ old('email', $branch->email ?? '') }}"
                                placeholder="email@cabang.com"
                                {{ isset($isShow) && $isShow ? 'readonly' : '' }}>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-medium text-secondary small">Deskripsi Cabang</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                <i class="fa-solid fa-info-circle"></i>
                            </span>
                            <textarea
                                class="form-control border-start-0 ps-2 @error('deskripsi') is-invalid @enderror"
                                id="deskripsi"
                                name="deskripsi"
                                rows="3"
                                placeholder="Deskripsi singkat tentang cabang..."
                                {{ isset($isShow) && $isShow ? 'readonly' : '' }}>{{ old('deskripsi', $branch->deskripsi ?? '') }}</textarea>
                        </div>
                        @error('deskripsi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kontak & Lokasi --}}
                    <div class="card border-0 bg-light mt-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-4">
                                <i class="fa-solid fa-address-book me-2 text-warning"></i>Kontak & Lokasi
                            </h6>

                            <div class="row">
                                <div class="col-md-6">
                                    {{-- Status Aktif --}}
                                    <div class="mb-4">
                                        <label for="is_active" class="form-label fw-medium text-secondary small">Status Cabang</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $branch->is_active ?? 1) ? 'checked' : '' }} {{ isset($isShow) && $isShow ? 'disabled' : '' }}>
                                            <label class="form-check-label fw-medium" for="is_active">
                                                Cabang Aktif
                                            </label>
                                        </div>
                                        <div class="form-text small text-muted">Non-aktifkan jika cabang tidak beroperasi.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {{-- Nomor Telepon --}}
                                    <div class="mb-4">
                                        <label for="branch_nomor_telepon_display" class="form-label fw-medium text-secondary small">Nomor Telepon / WA <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                                <i class="fa-solid fa-phone"></i>
                                            </span>
                                            <input type="text"
                                                class="form-control border-start-0 ps-2 required-field @error('nomor_telepon') is-invalid @enderror"
                                                id="branch_nomor_telepon_display"
                                                style="height: 45px;"
                                                value="{{ old('nomor_telepon', $branch->nomor_telepon ?? '') }}"
                                                placeholder="08xx-xxxx-xxxx"
                                                {{ isset($isShow) && $isShow ? 'readonly' : 'required' }}
                                                @if(!isset($isShow) || !$isShow)
                                                    data-error-message="Nomor telepon wajib diisi"
                                                @endif>
                                            <input type="hidden"
                                                name="nomor_telepon"
                                                id="branch_nomor_telepon"
                                                value="{{ old('nomor_telepon', $branch->nomor_telepon ?? '') }}">
                                        </div>
                                        @error('nomor_telepon')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">Nomor telepon wajib diisi</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {{-- Link Maps --}}
                                    <div class="mb-4">
                                        <label for="LinkMaps" class="form-label fw-medium text-secondary small">Link Google Maps</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                                <i class="fa-solid fa-link"></i>
                                            </span>
                                            <input type="text"
                                                class="form-control border-start-0 ps-2 @error('link_maps') is-invalid @enderror"
                                                id="LinkMaps"
                                                name="link_maps"
                                                style="height: 45px;"
                                                value="{{ old('link_maps', $branch->link_maps ?? '') }}"
                                                placeholder="https://maps.google.com/..."
                                                {{ isset($isShow) && $isShow ? 'readonly' : '' }}>
                                        </div>
                                        @if(isset($isShow) && $isShow)
                                            <div class="form-text small">
                                                <a href="{{ $branch->link_maps ?? '#' }}" target="_blank" class="text-primary">
                                                    <i class="fa-solid fa-external-link-alt me-1"></i>Buka di Google Maps
                                                </a>
                                            </div>
                                        @else
                                            <div class="form-text small text-muted">Salin link lokasi dari Google Maps.</div>
                                        @endif
                                        @error('link_maps')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Jam Operasional --}}
        <div class="col-lg-4">

            {{-- Card Jam Operasional --}}
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-clock me-2 text-success"></i>Jam Operasional
                    </h6>
                    <p class="text-muted small mt-1">Atur jam buka untuk setiap hari.</p>

                    {{-- Tombol Aksi Cepat --}}
                    @if(!isset($isShow) || !$isShow)
                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="bukaSemuaHari()">
                            <i class="fa-solid fa-check-double me-1"></i>Buka Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="tutupSemuaHari()">
                            <i class="fa-solid fa-times me-1"></i>Tutup Semua
                        </button>
                    </div>
                    @endif
                </div>
                <div class="card-body p-4">
                    @php
                        $hariList = $hariList ?? ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                        $jamOperasionalData = [];
                        if (isset($branch) && isset($branch->jamOperasional)) {
                            foreach ($branch->jamOperasional as $jam) {
                                $jamOperasionalData[$jam->hari] = $jam;
                            }
                        }
                    @endphp

                    {{-- Input Jam Global --}}
                    @if(!isset($isShow) || !$isShow)
                    <div class="mb-3 p-3 bg-light rounded">
                        <label class="form-label fw-medium text-secondary small mb-2">Atur Jam untuk Semua Hari</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="time" id="global_jam_buka" class="form-control form-control-sm" placeholder="08:00">
                            </div>
                            <div class="col-6">
                                <input type="time" id="global_jam_tutup" class="form-control form-control-sm" placeholder="21:00">
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-success mt-2 w-100" onclick="samakanSemuaJam()">
                            <i class="fa-solid fa-copy me-1"></i>Samakan Jam Semua Hari
                        </button>
                    </div>
                    @endif

                    @foreach($hariList as $index => $hari)
                        <div class="mb-2 {{ $index > 0 ? 'border-top pt-2' : '' }}">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label fw-bold mb-0" style="font-size: 1rem; color: #212529;">
                                    {{ $hari }}
                                </label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="jam_operasional[{{ $hari }}][is_buka]" value="0">
                                    <input class="form-check-input jam-operasional-toggle" type="checkbox"
                                           id="jam_{{ strtolower($hari) }}_buka"
                                           name="jam_operasional[{{ $hari }}][is_buka]"
                                           value="1"
                                           data-hari="{{ $hari }}"
                                           {{ old("jam_operasional.{$hari}.is_buka", ($jamOperasionalData[$hari]->is_buka ?? false)) ? 'checked' : '' }}
                                           {{ isset($isShow) && $isShow ? 'disabled' : '' }}>
                                    <label class="form-check-label small" for="jam_{{ strtolower($hari) }}_buka">
                                        Buka
                                    </label>
                                </div>
                            </div>

                            <div class="row jam-operasional-fields" data-hari="{{ $hari }}">
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0 text-muted ps-2">
                                            <i class="fa-solid fa-clock fa-xs"></i>
                                        </span>
                                        <input type="time"
                                               class="form-control border-start-0 ps-2 jam-buka-input clickable-time-input"
                                               name="jam_operasional[{{ $hari }}][jam_buka]"
                                               data-hari="{{ $hari }}"
                                               value="{{ old("jam_operasional.{$hari}.jam_buka", $jamOperasionalData[$hari]->jam_buka ?? '') }}"
                                               data-default-buka="08:00"
                                               placeholder="08:00"
                                               {{ isset($isShow) && $isShow ? 'readonly' : '' }}>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0 text-muted ps-2">
                                            <i class="fa-solid fa-clock fa-xs"></i>
                                        </span>
                                        <input type="time"
                                               class="form-control border-start-0 ps-2 jam-tutup-input clickable-time-input"
                                               name="jam_operasional[{{ $hari }}][jam_tutup]"
                                               data-hari="{{ $hari }}"
                                               value="{{ old("jam_operasional.{$hari}.jam_tutup", $jamOperasionalData[$hari]->jam_tutup ?? '') }}"
                                               data-default-tutup="21:00"
                                               placeholder="21:00"
                                               {{ isset($isShow) && $isShow ? 'readonly' : '' }}>
                                    </div>
                                </div>
                            </div>

                            {{-- Catatan Khusus (Collapse) --}}
                            <div class="mt-1">
                                <button type="button"
                                        class="btn btn-link btn-sm p-0 text-muted small text-decoration-none catatan-toggle border-0 bg-transparent"
                                        style="font-size: 0.75rem; color: #6c757d !important; outline: none !important; box-shadow: none !important;"
                                        onclick="toggleCatatan('{{ strtolower($hari) }}')">
                                    <span id="catatan-text-{{ strtolower($hari) }}" style="color: #6c757d !important;">
                                        <i class="fa-solid fa-plus-circle me-1" style="color: #6c757d !important;"></i>
                                        Tambah catatan
                                    </span>
                                </button>
                                <div id="catatan-collapse-{{ strtolower($hari) }}" class="collapse mt-1">
                                    <input type="text"
                                        class="form-control form-control-sm"
                                        name="jam_operasional[{{ $hari }}][catatan]"
                                        value="{{ old("jam_operasional.{$hari}.catatan", $jamOperasionalData[$hari]->catatan ?? '') }}"
                                        placeholder="Catatan khusus (opsional)"
                                        {{ isset($isShow) && $isShow ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>
            </div>

        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const toggles = document.querySelectorAll('.jam-operasional-toggle');

        toggles.forEach(toggle => {

            toggle.addEventListener('change', function() {
                const hari = this.dataset.hari;
                const jamBukaInput = document.querySelector(`.jam-buka-input[data-hari="${hari}"]`);
                const jamTutupInput = document.querySelector(`.jam-tutup-input[data-hari="${hari}"]`);

                if (this.checked) {
                    jamBukaInput.disabled = false;
                    jamTutupInput.disabled = false;
                    jamBukaInput.classList.remove('bg-light');
                    jamTutupInput.classList.remove('bg-light');

                    if (!jamBukaInput.value) {
                        jamBukaInput.value = jamBukaInput.dataset.defaultBuka;
                    }
                    if (!jamTutupInput.value) {
                        jamTutupInput.value = jamTutupInput.dataset.defaultTutup;
                    }
                } else {
                    jamBukaInput.disabled = true;
                    jamTutupInput.disabled = true;
                    jamBukaInput.value = '';
                    jamTutupInput.value = '';
                    jamBukaInput.classList.add('bg-light');
                    jamTutupInput.classList.add('bg-light');
                }

            });

            toggle.dispatchEvent(new Event('change'));
        });

        const timeInputs = document.querySelectorAll('.clickable-time-input');
        timeInputs.forEach(input => {
            input.addEventListener('click', function() {
                if (!this.disabled && typeof this.showPicker === 'function') {
                    this.showPicker();
                }
            });
        });

        @foreach($hariList as $hari)
            @php
                $catatanValue = old("jam_operasional.{$hari}.catatan", $jamOperasionalData[$hari]->catatan ?? '');
                $hasCatatan = !empty($catatanValue);
            @endphp
            @if($hasCatatan)
                document.getElementById('catatan-collapse-{{ strtolower($hari) }}').classList.add('show');
                document.getElementById('catatan-text-{{ strtolower($hari) }}').innerHTML = '<i class="fa-solid fa-minus-circle me-1" style="color: #6c757d !important;"></i>Sembunyikan catatan';
            @endif
        @endforeach

        // Form validation sebelum submit
        const form = document.getElementById('branchForm');
        form.addEventListener('submit', function(e) {

            let hasError = false;
            let errorMessages = [];

            toggles.forEach(toggle => {
                if (toggle.checked) {
                    const hari = toggle.dataset.hari;
                    const jamBukaInput = document.querySelector(`.jam-buka-input[data-hari="${hari}"]`);
                    const jamTutupInput = document.querySelector(`.jam-tutup-input[data-hari="${hari}"]`);

                    if (!jamBukaInput.value || !jamTutupInput.value) {
                        hasError = true;
                        errorMessages.push(`Jam operasional untuk hari ${hari} belum lengkap`);
                        jamBukaInput.classList.add('is-invalid');
                        jamTutupInput.classList.add('is-invalid');
                    } else {

                        if (jamTutupInput.value <= jamBukaInput.value) {
                            hasError = true;
                            errorMessages.push(`Jam tutup harus lebih besar dari jam buka untuk hari ${hari}`);
                            jamTutupInput.classList.add('is-invalid');
                        } else {
                            jamBukaInput.classList.remove('is-invalid');
                            jamTutupInput.classList.remove('is-invalid');
                        }
                    }
                }
            });

            if (hasError) {
                e.preventDefault();

                let alertMessage = 'Terdapat kesalahan pada form:\n\n';
                errorMessages.forEach((msg, index) => {
                    alertMessage += `${index + 1}. ${msg}\n`;
                });

                alert(alertMessage);

                document.querySelector('.card-header h6 i.fa-clock').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    });

    function bukaSemuaHari() {
        const toggles = document.querySelectorAll('.jam-operasional-toggle');
        toggles.forEach(toggle => {
            toggle.checked = true;
            toggle.dispatchEvent(new Event('change'));
        });
    }

    function tutupSemuaHari() {
        const toggles = document.querySelectorAll('.jam-operasional-toggle');
        toggles.forEach(toggle => {
            toggle.checked = false;
            toggle.dispatchEvent(new Event('change'));
        });
    }

    function samakanSemuaJam() {
        const jamBuka = document.getElementById('global_jam_buka').value;
        const jamTutup = document.getElementById('global_jam_tutup').value;

        if (!jamBuka || !jamTutup) {
            alert('Silakan isi jam buka dan jam tutup terlebih dahulu');
            return;
        }

        if (jamTutup <= jamBuka) {
            alert('Jam tutup harus lebih besar dari jam buka');
            return;
        }

        const jamBukaInputs = document.querySelectorAll('.jam-buka-input');
        const jamTutupInputs = document.querySelectorAll('.jam-tutup-input');

        jamBukaInputs.forEach(input => {
            if (!input.disabled) {
                input.value = jamBuka;
                input.classList.remove('is-invalid');
            }
        });

        jamTutupInputs.forEach(input => {
            if (!input.disabled) {
                input.value = jamTutup;
                input.classList.remove('is-invalid');
            }
        });

        bukaSemuaHari();
    }

    function toggleCatatan(hari) {
        const collapse = document.getElementById(`catatan-collapse-${hari}`);
        const text = document.getElementById(`catatan-text-${hari}`);

        if (!collapse || !text) return;
        if (collapse.classList.contains('show')) {
            collapse.classList.remove('show');
            text.innerHTML = '<i class="fa-solid fa-plus-circle me-1" style="color: #6c757d !important;"></i>Tambah catatan';
        } else {
            collapse.classList.add('show');
            text.innerHTML = '<i class="fa-solid fa-minus-circle me-1" style="color: #6c757d !important;"></i>Sembunyikan catatan';

            setTimeout(() => {
                const input = collapse.querySelector('input');
                if (input) {
                    input.focus();
                }
            }, 150);
        }
    }
</script>
@endpush

@endsection
