@extends('layouts.admin')

@section('title', 'Reset Password')

@section('content')

<div class="row"> 
    <div class="col-12"> 
        
        <div class="card shadow-sm border-0" style="border-radius: 10px;">
            {{-- Card Header --}}
            <div class="card-header bg-white border-0 pt-4 ps-4 pe-4 pb-0">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fa-solid fa-key me-2 text-primary"></i>
                    Reset Password
                </h5>
                <p class="text-muted small mt-1">
                    Ubah password akun Anda dengan password baru yang lebih aman.
                </p>
            </div>

            <div class="card-body p-4">
                {{-- Alert Success --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Alert Error --}}
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.profile.resetPassword.post') }}" method="POST">

                    <div class="row">
                        <div class="col-md-7 col-lg-6">
                            
                            {{-- Password Lama --}}
                            <div class="mb-4">
                                <label class="form-label fw-medium text-secondary small">Password Lama</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password"
                                        name="current_password"
                                        id="current_password" 
                                        class="form-control border-start-0 border-end-0 ps-2 @error('current_password') is-invalid @enderror" 
                                        style="height: 50px;" 
                                        placeholder="Masukkan password lama Anda"
                                        required
                                        autofocus>
                                    
                                    <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePasswordVisibility('current_password', 'eyeIconCurrent')">
                                        <i class="fa-solid fa-eye" id="eyeIconCurrent"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password Baru --}}
                            <div class="mb-4">
                                <label class="form-label fw-medium text-secondary small">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                        <i class="fa-solid fa-key"></i>
                                    </span>
                                    <input type="password"
                                        name="new_password"
                                        id="new_password" 
                                        class="form-control border-start-0 border-end-0 ps-2 @error('new_password') is-invalid @enderror" 
                                        style="height: 50px;" 
                                        placeholder="Masukkan password baru"
                                        required>
                                    
                                    <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePasswordVisibility('new_password', 'eyeIconNew')">
                                        <i class="fa-solid fa-eye" id="eyeIconNew"></i>
                                    </button>
                                </div>
                                @error('new_password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text small text-muted mt-2" style="font-size: 0.8rem; line-height: 1.4;">
                                    <i class="fa-solid fa-info-circle me-1"></i> 
                                    Password minimal 6 karakter
                                </div>
                            </div>

                            {{-- Konfirmasi Password Baru --}}
                            <div class="mb-4">
                                <label class="form-label fw-medium text-secondary small">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted ps-3">
                                        <i class="fa-solid fa-check-double"></i>
                                    </span>
                                    <input type="password"
                                        name="new_password_confirmation"
                                        id="new_password_confirmation" 
                                        class="form-control border-start-0 border-end-0 ps-2" 
                                        style="height: 50px;" 
                                        placeholder="Ulangi password baru"
                                        required>
                                    
                                    <button class="btn btn-light border border-start-0 text-muted" type="button" onclick="togglePasswordVisibility('new_password_confirmation', 'eyeIconConfirm')">
                                        <i class="fa-solid fa-eye" id="eyeIconConfirm"></i>
                                    </button>
                                </div>
                                <div class="form-text small text-muted mt-2" style="font-size: 0.8rem; line-height: 1.4;">
                                    <i class="fa-solid fa-shield-halved me-1 text-success"></i> 
                                    Pastikan password konfirmasi sama dengan password baru
                                </div>
                            </div>

                        </div>
                    </div>

                    <hr class="my-4 opacity-25">

                    {{-- Tombol Aksi --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light border px-4 fw-medium text-secondary d-flex align-items-center gap-2">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-medium d-flex align-items-center gap-2">
                            <i class="fa-solid fa-save"></i> Update Password
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush