@extends('layouts.admin')

@php
    $isCreate = $isCreate ?? false;
    $isReadOnly = $isReadOnly ?? false;
@endphp

@section('title', $isCreate ? 'Tambah QC Manual' : ('Proses QC - ' . ($item->nama_item ?? 'Item')))

@push('page-actions')
    <a href="{{ $isReadOnly ? route('admin.quality-control.history') : route('admin.quality-control.index') }}" class="btn btn-light border btn-sm d-flex align-items-center gap-2 text-secondary fw-medium">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali</span>
    </a>
@endpush

@section('content')

{{-- Error Alert (Style HEAD) --}}
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert" style="border-left: 5px solid #dc3545 !important;">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-danger"></i>
            <strong class="text-danger">Ada Kesalahan Input!</strong>
        </div>
        <ul class="mb-0 mt-1 small text-secondary">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ $isCreate ? route('admin.quality-control.store') : route('admin.quality-control.update', $item->id) }}"
      method="POST"
      id="qcForm"
      data-validate-form>
    @csrf
    @if(!$isCreate)
    @method('PUT')
    @endif
    @if($isReadOnly)
        @php $readonlyAttr = 'readonly'; $disabledAttr = 'disabled'; @endphp
    @else
        @php $readonlyAttr = ''; $disabledAttr = ''; @endphp
    @endif

    <div class="row">
        @if($isReadOnly)
        <div class="col-12 mb-3">
            <div class="alert alert-light border d-flex align-items-center gap-2 mb-0">
                <i class="fa-solid fa-circle-info text-secondary"></i>
                <span class="small text-secondary">
                    Riwayat QC (read-only). Finalisasi: {{ $item->updated_at?->format('d M Y, H:i') ?? '-' }}
                </span>
            </div>
        </div>
        @endif

        {{-- KOLOM KIRI: Detail & Kondisi (Style HEAD) --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-box-open me-2 text-primary"></i>Detail Item & Fisik
                    </h6>
                </div>

<div class="card-body p-4">

    {{-- Identitas Item --}}
    <div class="mb-3">
        <label class="form-label fw-medium text-secondary small dynamic-label" data-field="nama_item">
            Nama Item <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-tag"></i></span>
            <input type="text" name="nama_item"
                   class="form-control border-start-0 ps-2 dynamic-field"
                   data-field="nama_item"
                   data-lolos-qc-required="true"
                   {{ $readonlyAttr }}
                   value="{{ old('nama_item', $item->nama_item) }}"
                   autofocus>
        </div>
        <div class="invalid-feedback dynamic-error" data-field="nama_item" style="display: none;">
            Nama Item wajib diisi
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-medium text-secondary small dynamic-label" data-field="kategori_id">
                Kategori <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-layer-group"></i></span>
                <select name="kategori_id"
                        class="form-select border-start-0 ps-2 dynamic-field"
                        data-field="kategori_id"
                        data-lolos-qc-required="true"
                        {{ $disabledAttr }}>
                    <option value="">Pilih Kategori</option>
                    @foreach($semua_kategori as $kat)
                        <option value="{{ $kat->id }}" {{ (old('kategori_id', $item->kategori_id) == $kat->id) ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="invalid-feedback dynamic-error" data-field="kategori_id" style="display: none;">
                Kategori wajib dipilih
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-medium text-secondary small dynamic-label" data-field="kode_sku">
                Kode SKU <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-barcode"></i></span>
                <input type="text" name="kode_sku"
                       class="form-control border-start-0 ps-2 dynamic-field"
                       data-field="kode_sku"
                       data-lolos-qc-required="true"
                       {{ $readonlyAttr }}
                       value="{{ old('kode_sku', $item->kode_sku) }}">
            </div>
            <div class="invalid-feedback dynamic-error" data-field="kode_sku" style="display: none;">
                Kode SKU wajib diisi
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <label class="form-label fw-medium text-secondary small">Serial Number (Body)</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-fingerprint"></i></span>
                <input type="text" name="serial_number"
                       class="form-control border-start-0 ps-2 font-monospace"
                       {{ $readonlyAttr }}
                       value="{{ old('serial_number', $item->serial_number) }}">
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label fw-medium text-secondary small">Serial Number (Lensa)</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-fingerprint"></i></span>
                <input type="text" name="serial_lens"
                       class="form-control border-start-0 ps-2 font-monospace"
                       {{ $readonlyAttr }}
                       value="{{ old('serial_lens', $item->serial_lens) }}">
            </div>
        </div>
    </div>

    <hr class="my-4 opacity-25">

    <h6 class="fw-bold text-dark mb-3">
        <i class="fa-solid fa-clipboard-check me-2 text-success"></i>Pengecekan Kondisi
    </h6>

    <div class="accordion shadow-sm" id="qcConditionAccordion" style="border-radius: 8px; overflow: hidden;">

        {{-- Accordion 1: Fisik --}}
        <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed fw-medium bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                    <i class="fa-solid fa-camera me-2 text-secondary"></i> Kondisi Fisik Unit
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#qcConditionAccordion">
                <div class="accordion-body bg-light">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Kondisi Fisik</label>
                            <input type="text" name="kondisi_fisik" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_fisik', $item->kondisi_fisik) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Kondisi Baut</label>
                            <input type="text" name="kondisi_baut" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_baut', $item->kondisi_baut) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Tutup USB</label>
                            <input type="text" name="kondisi_tutup_usb" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_tutup_usb', $item->kondisi_tutup_usb) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Karet Grip</label>
                            <input type="text" name="kondisi_grip" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_grip', $item->kondisi_grip) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Accordion 2: Lensa --}}
        <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed fw-medium bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                    <i class="fa-solid fa-bullseye me-2 text-secondary"></i> Kondisi Lensa & Sensor
                </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#qcConditionAccordion">
                <div class="accordion-body bg-light">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Jamur Lensa</label>
                            <input type="text" name="kondisi_jamur_lensa" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_jamur_lensa', $item->kondisi_jamur_lensa) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Jamur Sensor</label>
                            <input type="text" name="kondisi_jamur_sensor" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_jamur_sensor', $item->kondisi_jamur_sensor) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Auto Fokus (AF)</label>
                            <input type="text" name="kondisi_af_lensa" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_af_lensa', $item->kondisi_af_lensa) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Diafragma (Aperture)</label>
                            <input type="text" name="kondisi_diafragma_lensa" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_diafragma_lensa', $item->kondisi_diafragma_lensa) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Zooming</label>
                            <input type="text" name="kondisi_zoom_lensa" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_zoom_lensa', $item->kondisi_zoom_lensa) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">View Finder</label>
                            <input type="text" name="kondisi_view_finder" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_view_finder', $item->kondisi_view_finder) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Accordion 3: Lainnya --}}
        <div class="accordion-item border-0">
            <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed fw-medium bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                    <i class="fa-solid fa-list-check me-2 text-secondary"></i> Fungsi Lain & Kelengkapan
                </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#qcConditionAccordion">
                <div class="accordion-body bg-light">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Mounting</label>
                            <input type="text" name="kondisi_mounting" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_mounting', $item->kondisi_mounting) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Slot Memori</label>
                            <input type="text" name="kondisi_slot_memori" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_slot_memori', $item->kondisi_slot_memori) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">LCD</label>
                            <input type="text" name="kondisi_lcd" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_lcd', $item->kondisi_lcd) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Tombol</label>
                            <input type="text" name="kondisi_tombol" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_tombol', $item->kondisi_tombol) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Flash</label>
                            <input type="text" name="kondisi_flash" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_flash', $item->kondisi_flash) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small text-muted">Sound / Mic</label>
                            <input type="text" name="kondisi_sound_mic" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_sound_mic', $item->kondisi_sound_mic) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">Kondisi Lain-lain</label>
                        <input type="text" name="kondisi_lain_lain" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kondisi_lain_lain', $item->kondisi_lain_lain) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Kelengkapan</label>
                        <input type="text" name="kelengkapan" class="form-control form-control-sm" {{ $readonlyAttr }} value="{{ old('kelengkapan', $item->kelengkapan) }}" placeholder="Box, Charger, dll">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-0 mt-4">
            <label class="form-label fw-medium text-secondary small dynamic-label" data-field="deskripsi_produk">
                Deskripsi Produk (Final) <span class="text-danger">*</span>
            </label>
        <textarea name="deskripsi_produk"
                  class="form-control dynamic-field"
                  data-field="deskripsi_produk"
                  data-lolos-qc-required="true"
                  {{ $readonlyAttr }}
                  rows="4">{{ old('deskripsi_produk', $item->deskripsi_produk) }}</textarea>
        <div class="invalid-feedback dynamic-error" data-field="deskripsi_produk" style="display: none;">
            Deskripsi Produk wajib diisi
        </div>
    </div>

</div>
            </div>
        </div>

        {{-- KOLOM KANAN: Harga & Status (Style HEAD) --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 10px;">
                <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-calculator me-2 text-warning"></i>Harga & Status
                    </h6>
                </div>

                <div class="card-body p-4">
                    {{-- Harga Modal --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Harga Modal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">Rp</span>
                            <input type="text" name="harga_beli" id="harga_beli" class="form-control border-start-0 ps-2 rupiah-mask" {{ $readonlyAttr }} value="{{ old('harga_beli', $item->harga_beli) }}">
                        </div>
                    </div>

                    {{-- Harga Servis --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Biaya Servis</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">Rp</span>
                            <input type="text" name="harga_servis" id="harga_servis" class="form-control border-start-0 ps-2 rupiah-mask" {{ $readonlyAttr }} value="{{ old('harga_servis', $item->harga_servis) }}">
                        </div>
                    </div>

                    {{-- Harga Jual --}}
                    <div class="mb-3">
                            <label class="form-label fw-medium text-secondary small dynamic-label" data-field="harga_jual">
                                Harga Jual <span class="text-danger">*</span>
                            </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3">Rp</span>
                            <input type="text" name="harga_jual" id="harga_jual"
                                class="form-control border-start-0 ps-2 rupiah-mask dynamic-field"
                                data-field="harga_jual"
                                data-lolos-qc-required="true"
                                {{ $readonlyAttr }}
                                value="{{ old('harga_jual', $item->harga_jual) }}">
                        </div>
                        <div class="invalid-feedback dynamic-error" data-field="harga_jual" style="display: none;">
                            Harga Jual wajib diisi dan harus berupa angka lebih dari 0
                        </div>
                    </div>

                    {{-- Qty --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium text-secondary small">Qty</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fa-solid fa-cubes"></i></span>
                            <input type="number" name="qty" class="form-control border-start-0 ps-2" {{ $readonlyAttr }} value="{{ old('qty', $item->qty) }}">
                        </div>
                    </div>

                    {{-- Grade & Status --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Grade</label>
                            <select name="grade" class="form-select" {{ $disabledAttr }}>
                                <option value="Unggulan" {{ (old('grade', $item->grade) == 'Unggulan') ? 'selected' : '' }}>Unggulan</option>
                                <option value="Standar" {{ (old('grade', $item->grade) == 'Standar') ? 'selected' : '' }}>Standar</option>
                                <option value="Minus" {{ (old('grade', $item->grade) == 'Minus') ? 'selected' : '' }}>Minus</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium text-secondary small">Status Barang</label>
                            <select name="status" class="form-select" {{ $disabledAttr }}>
                                <option value="Second" {{ (old('status', $item->status) == 'Second') ? 'selected' : '' }}>Second</option>
                                <option value="Baru" {{ (old('status', $item->status) == 'Baru') ? 'selected' : '' }}>Baru</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    {{-- Status QC --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">STATUS AKHIR QC</label>
                        <select name="status_qc" class="form-select form-select-lg fw-bold {{ $item->status_qc == 'lolos_qc' ? 'text-success border-success' : 'text-secondary' }}" {{ $disabledAttr }}>
                            <option value="menunggu_qc" {{ (old('status_qc', $item->status_qc) == 'menunggu_qc') ? 'selected' : '' }}>Menunggu QC</option>
                            <option value="lolos_qc" {{ (old('status_qc', $item->status_qc) == 'lolos_qc') ? 'selected' : '' }}>Lolos QC</option>
                            <option value="gagal_qc" {{ (old('status_qc', $item->status_qc) == 'gagal_qc') ? 'selected' : '' }}>Gagal QC</option>
                            {{-- <option value="diarsipkan" {{ (old('status_qc', $item->status_qc) == 'diarsipkan') ? 'selected' : '' }}>Diarsipkan</option> --}}
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium text-secondary small">Catatan QC Internal</label>
                        <textarea name="catatan_qc" class="form-control" rows="2" placeholder="Catatan untuk tim internal..." {{ $readonlyAttr }}>{{ old('catatan_qc', $item->catatan_qc) }}</textarea>
                    </div>

                    {{-- Action Button (Smart - Auto maps status to action) --}}
                    @if(!$isReadOnly)
                    <div class="d-grid gap-2">
                        <button type="submit" id="smartActionBtn" class="btn fw-medium py-2" data-status="{{ $item->status_qc }}">
                            <i id="smartActionIcon" class="fa-solid fa-save me-2"></i>
                            <span id="smartActionText">Simpan Perubahan</span>
                        </button>
                    </div>
                    <small class="d-block text-muted mt-2 text-center" id="smartActionHint">
                        Tombol akan menyesuaikan dengan status QC yang dipilih
                    </small>
                    @endif

                </div>
            </div>
        </div>
    </div>

</form>

<!-- Unsaved changes confirmation modal (simple copy) -->
<div class="modal fade" id="unsavedChangesModal" tabindex="-1" aria-labelledby="unsavedChangesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-3">
                <strong>Perubahan belum disimpan</strong>
                <div class="mt-2">Ada perubahan yang belum tersimpan. Tinggalkan halaman?</div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tetap</button>
                <button type="button" id="unsavedConfirmBtn" class="btn btn-primary">Tinggalkan</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .dynamic-field.lolos-qc-mode {
        border-left: 3px solid #198754 !important;
        background-color: rgba(25, 135, 84, 0.05) !important;
    }

    .dynamic-field.lolos-qc-mode:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
    }

    .dynamic-label.lolos-qc-required {
        color: #198754 !important;
        font-weight: 600 !important;
    }

    .dynamic-label.lolos-qc-required .text-danger {
        display: inline !important;
    }

    select[name="status_qc"].status-highlight {
        border-color: #198754 !important;
        background-color: rgba(25, 135, 84, 0.1) !important;
    }
</style>
@endpush

@push('scripts')
@if(!$isReadOnly)
<script>

    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.querySelector('select[name="status_qc"]');
        const dynamicFields = document.querySelectorAll('.dynamic-required[data-lolos-qc-required="true"]');

        if (statusSelect && dynamicFields.length > 0) {

            function updateFieldMode(isLolosQc) {
                dynamicFields.forEach(field => {
                    if (isLolosQc) {
                        field.classList.add('lolos-qc-mode');
                        field.setAttribute('required', 'required');
                    } else {
                        field.classList.remove('lolos-qc-mode');
                        field.removeAttribute('required');
                    }
                });
            }

            updateFieldMode(statusSelect.value === 'lolos_qc');

            statusSelect.addEventListener('change', function() {
                updateFieldMode(this.value === 'lolos_qc');
            });
        }
    });
</script>
@endif


@if(!$isReadOnly)
@vite('resources/js/qualityControl/data-qc.js')
@endif
@endpush
