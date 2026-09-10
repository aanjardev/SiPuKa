@php
    $setting = \App\Models\CatalogSettings::first();
@endphp
<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Admin Panel') | SiDiKa</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/admin/admin-layout.css',
        'resources/js/admin/admin-layout.js',
        'resources/js/utils/phone-format-display.js',
    ])

    <link rel="shortcut icon" href="{{ $setting?->logo_url ?? asset('mainIMG/logoDK.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome CSS (loaded early to prevent icon rendering issues) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/8794378048.js" crossorigin="anonymous"></script>

    <!-- Admin CSS (fallback assets) -->
    <link href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/adminsidebar.css') }}" rel="stylesheet">
    <link href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/adminpage.css') }}" rel="stylesheet">
    <link href="{{ \App\Helpers\CssAssetHelper::css('css/legacy/form-validation.css') }}" rel="stylesheet">

    @stack('styles')

</head>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/alert.js') }}"></script>

<body class="  ">
    @unless ($__env->hasSection('disable_alerts'))
        @include('partials.alerts')
    @endunless
    <!-- loader Start -->
    <div id="loading">
        <div class="loader"></div>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <img src="{{ $setting->logo_url }}"
                alt="{{ $setting->nama_website }} Logo"
                class="sidebar-logo">
            <div class="sidebar-brand">
                <h4 class="sidebar-brand-text">{{ $setting->nama_website}}</h4>
                <span class="sidebar-brand-subtitle">Admin Panel</span>
            </div>
            <button class="sidebar-close" type="button" aria-label="Close Sidebar">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Sidebar Menu -->
        <div class="sidebar-menu" id="sidebarMenu">
            <!-- Dashboard (direct link) -->
            <div class="menu-section">
                <a href="{{ route('admin.dashboard') }}" class="menu-link d-flex align-items-center">
                    <i class="fas fa-th-large menu-icon"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Master Data Section -->
            <div class="menu-section">
                <button class="menu-link menu-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#masterDataMenu" aria-expanded="false" aria-controls="masterDataMenu">
                    <i class="fas fa-database menu-icon"></i>
                    <span>Master Data</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </button>

                <div class="collapse submenu" id="masterDataMenu">
                    <div class="submenu-item">
                        <a href="{{ route('admin.products.index') }}" class="submenu-link">
                            <i class="fas fa-box submenu-icon"></i>
                            <span>Data Produk</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.customers.index') }}" class="submenu-link">
                            <i class="fas fa-users submenu-icon"></i>
                            <span>Data Pelanggan</span>
                        </a>
                    </div>

                    @if (Auth::user()->role == 'manager')
                    <div class="submenu-item">
                        <a href="{{ route('admin.employees.index') }}" class="submenu-link">
                            <i class="fas fa-user-tie submenu-icon"></i>
                            <span>Data Karyawan</span>
                        </a>
                    </div>
                    @endif

                    <div class="submenu-item">
                        <a href="{{ route('admin.categories.index') }}" class="submenu-link">
                            <i class="fas fa-list submenu-icon"></i>
                            <span>Daftar Kategori</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.branches.index') }}" class="submenu-link">
                            <i class="fas fa-store submenu-icon"></i>
                            <span>Data Cabang</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Transaksi Section -->
            <div class="menu-section">
                <button class="menu-link menu-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#transaksiMenu" aria-expanded="false" aria-controls="transaksiMenu">
                    <i class="fas fa-exchange-alt menu-icon"></i>
                    <span>Operasional</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </button>

                <div class="collapse submenu" id="transaksiMenu">
                    <div class="submenu-item">
                        <a href="{{ route('admin.sales.index') }}" class="submenu-link">
                            <i class="fas fa-shopping-cart submenu-icon"></i>
                            <span>Penjualan</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.purchases.index') }}" class="submenu-link">
                            <i class="fas fa-shopping-bag submenu-icon"></i>
                            <span>Pembelian</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.quality-control.index') }}" class="submenu-link">
                            <i class="fas fa-check-circle submenu-icon"></i>
                            <span>Quality Control</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.products.photos') }}" class="submenu-link">
                            <i class="fas fa-image submenu-icon"></i>
                            <span>Foto Produk</span>
                        </a>
                    </div>

                    <div class="submenu-item">
                        <a href="{{ route('admin.smart-stock.index') }}" class="submenu-link">
                            <i class="fas fa-chart-line submenu-icon"></i>
                            <span>Smart Stock Analysis</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Manajemen Section -->
            <div class="menu-section">
                <button class="menu-link menu-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#manajemenMenu" aria-expanded="false" aria-controls="manajemenMenu">
                    <i class="fas fa-briefcase menu-icon"></i>
                    <span>Manajemen</span>
                    <i class="fas fa-chevron-right menu-arrow"></i>
                </button>

                <div class="collapse submenu" id="manajemenMenu">
                    <div class="submenu-item">
                        <a href="{{ route('admin.catalog-settings.index') }}" class="submenu-link">
                            <i class="fas fa-cog submenu-icon"></i>
                            <span>Setting Web Katalog</span>
                        </a>
                    </div>

                    @if (Auth::user()->role == 'manager')
                    <div class="submenu-item">
                        <a href="{{ route('admin.permissions') }}" class="submenu-link">
                            <i class="fas fa-user-shield submenu-icon"></i>
                            <span>Manajemen Akses</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Footer - User Profile -->
        <div class="sidebar-footer">
            <div class="dropdown dropup">
                <button class="user-profile dropdown-toggle sidebar-profile-toggle text-start" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <p class="user-name">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="user-role">{{ isset(Auth::user()->role) ? ucfirst(Auth::user()->role) : '' }}</p>
                    </div>
                    <i class="fas fa-chevron-down" style="color: var(--text-muted); font-size: 0.8rem;"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end sidebar-profile-menu" style="margin: 0.35rem 0;">
                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="fas fa-user-circle me-2"></i> Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button type="button"
                                class="dropdown-item text-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#logoutConfirmModal">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </aside>

    <!-- Page Header -->
    <div class="admin-topbar">
        <div class="d-flex justify-content-between align-items-center admin-page-header admin-page-header-sticky">
            <h1 class="page-title mb-0">@yield('title', 'Dashboard')</h1>
            <div class="d-flex align-items-center gap-2 admin-page-actions">
                @stack('page-actions')
                <button class="btn btn-outline-secondary btn-sm admin-sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content pt-5 mb-0 mt-0 d-flex flex-column pb-0">
        <div class="content-wrapper pt-0 pb-3">
            @yield('content')
        </div>

        <footer class="mt-auto p-2 border-top">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted mb-0">
                        &copy; <script>document.write(new Date().getFullYear())</script>
                        <strong>Dinoyo Kamera</strong> - Sistem Informasi Dinoyo Kamera
                    </p>
                </div>
            </div>
        </footer>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/form-validation.js') }}"></script>

    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="logoutConfirmLabel">
                        <i class="fa-solid fa-right-from-bracket me-2 text-warning"></i>Konfirmasi Logout
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 d-flex align-items-center justify-content-center text-warning mx-auto mb-3"
                             style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-right-from-bracket fa-2x"></i>
                        </div>
                        <h6 class="fw-bold mb-2">Apakah Anda yakin ingin logout?</h6>
                        <p class="text-muted small mb-0">
                            Anda akan keluar dari sistem dan perlu login kembali untuk mengakses halaman admin.
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times me-2"></i>Batal
                    </button>
                    <form id="logoutFormLayout" action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fa-solid fa-right-from-bracket me-2"></i>Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const currentPath = window.location.pathname;        // Deteksi jika sedang di halaman upload foto produk individu
        const isProductPhotoUpload = /^\/admin\/products\/\d+\/photos-upload\/?$/.test(currentPath);

        if (isProductPhotoUpload) {
            const photoMenuLink = document.querySelector('a[href="{{ route('admin.products.photos') }}"]');

            if (photoMenuLink) {
                photoMenuLink.classList.add('active');

                const parentCollapse = photoMenuLink.closest('.collapse');
                if (parentCollapse) {
                    parentCollapse.classList.add('show');

                    const parentToggle = parentCollapse.previousElementSibling;
                    if (parentToggle && parentToggle.classList.contains('menu-toggle')) {
                        parentToggle.classList.remove('collapsed');
                        parentToggle.setAttribute('aria-expanded', 'true');
                        parentToggle.classList.add('active');
                    }
                }
            }
        }
    });
    </script>
</body>

</html>
