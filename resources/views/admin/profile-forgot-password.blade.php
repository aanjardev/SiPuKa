@extends('layouts.admin')

@section('title', 'Reset Password - SiDiKa')

@push('page-actions')
    <a href="{{ route('admin.profile') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
        <i class="fas fa-arrow-left me-1"></i>
        <span>Kembali ke Profile</span>
    </a>

@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-gradient rounded-circle p-3 me-3">
                            <i class="fas fa-lock text-white fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Keamanan Akun</h5>
                            <p class="text-muted mb-0">Reset password akun Anda melalui email verifikasi</p>
                        </div>
                    </div>

                    <!-- @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif -->

                    <form method="POST" action="{{ route('admin.profile.forgot-password') }}" id="forgotForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-10">
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-medium">
                                        Email Terdaftar
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="email" 
                                               class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ Auth::user()->email ?? old('email') }}"
                                               readonly
                                               required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                                            <i class="fas fa-paper-plane me-1"></i>
                                            <span>Kirim Kode Verifikasi</span>
                                        </button>
                                    </div>
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Email yang terdaftar pada akun Anda
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Proses Reset Password
                            </h6>
                            <ol class="mb-0 small">
                                <li class="mb-2">Klik tombol "Kirim Kode Verifikasi"</li>
                                <li class="mb-2">Periksa email Anda untuk kode 6 digit</li>
                                <li class="mb-2">Masukkan kode di halaman verifikasi</li>
                                <li class="mb-0">Buat password baru yang kuat</li>
                            </ol>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-shield-alt me-2 text-success"></i>
                        Tips Keamanan
                    </h6>
                    
                    <div class="mb-3">
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-medium">Password Kuat</small>
                                <p class="text-muted small mb-0">Gunakan kombinasi huruf, angka, dan simbol</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-medium">Jangan Berbagi</small>
                                <p class="text-muted small mb-0">Jangan berikan password kepada siapa pun</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start mb-2">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-medium">Update Berkala</small>
                                <p class="text-muted small mb-0">Ganti password secara teratur</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start">
                            <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                            <div>
                                <small class="fw-medium">Email Aman</small>
                                <p class="text-muted small mb-0">Pastikan email Anda aman dan dapat diakses</p>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="border-top pt-3">
                        <div class="text-center">
                            <div class="bg-light rounded-circle p-3 d-inline-block mb-2">
                                <i class="fas fa-lock text-primary fs-3"></i>
                            </div>
                            <p class="small text-muted mb-0">
                                Reset password akan logout semua session yang aktif
                            </p>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {

    const forgotForm = document.getElementById('forgotForm');
    if (forgotForm && window.FormValidator) {
        FormValidator.initForm(forgotForm);
    }

    const submitBtn = forgotForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    forgotForm.addEventListener('submit', function(e) {
        // Don't prevent default submission for now, let it submit normally

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';

        const form = this;

        function resetButton() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }

        setTimeout(resetButton, 5000);


        window.addEventListener('unload', resetButton);
    });
});
</script>
@endpush
