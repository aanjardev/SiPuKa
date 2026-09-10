<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\QCController;
use App\Http\Controllers\CatalogSettingsController;
use App\Http\Controllers\SmartStockController;
use App\Http\Controllers\AccountActivationController;
use App\Http\Controllers\TimeoutController;
use Illuminate\Support\Facades\Route;

use Symfony\Component\HttpKernel\Profiler\Profile;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get("/", [PageController::class,"index"]);
Route::get("/about", [PageController::class,"about"]);
Route::get("/contact", [PageController::class,"contact"]);
Route::get("/admin", [PageController::class,"admin"]);

Route::get('/test-timeout', [TimeoutController::class, 'testTimeout'])->name('test.timeout');
Route::get('/test-queue', [TimeoutController::class, 'handleHeavyTask'])->name('test.queue');
Route::get('/check-job/{jobId}', [TimeoutController::class, 'checkJobStatus'])->name('check.job.status');
Route::get('/simulate-timeout', [TimeoutController::class, 'simulateTimeout'])->name('simulate.timeout');

Route::get('/test-404', function() {
    abort(404, 'Halaman testing 404 tidak ditemukan');
})->name('test.404');

Route::get('/test-403', function() {
    abort(403, 'Akses testing 403 ditolak');
})->name('test.403');

Route::get('/test-500', function() {

    return response()->view('errors.500', [], 500);
})->name('test.500');

Route::get('/test-real-500', function() {

    throw new \Exception('Simulasi error 500 untuk testing');
})->name('test.real.500');

Route::get('/test-429', function() {
    abort(429, 'Terlalu banyak permintaan testing');
})->name('test.429');

Route::get('/test-auth', function() {
    if (!auth()->check()) {
        abort(403, 'Anda harus login untuk mengakses halaman ini');
    }
    return 'Anda sudah login';
})->name('test.auth');


Route::get("/katalog", [ProductController::class, "index"])->name('product.index');
Route::get("/katalog/suggest", [ProductController::class, "suggest"])->name('product.suggest');
Route::get("/katalog/{id}", [ProductController::class, "show"])->name('product.show');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/activation', [AccountActivationController::class, 'showActivationForm'])->name('activation.form');
Route::post('/activation/verify-email', [AccountActivationController::class, 'verifyEmail'])->name('activation.verify-email');
Route::get('/activation/verify', [AccountActivationController::class, 'showVerificationForm'])->name('activation.verify-form');
Route::post('/activation/verify-code', [AccountActivationController::class, 'verifyCode'])->name('activation.verify-code');
Route::get('/activation/setup-password/{token}', [AccountActivationController::class, 'showPasswordSetupForm'])->name('activation.setup-password');
Route::post('/activation/setup-password/{token}', [AccountActivationController::class, 'setupPassword'])->name('activation.setup-password.post');
Route::post('/activation/resend-code', [AccountActivationController::class, 'resendCode'])->name('activation.resend-code');

Route::get('/forgot-password', [ProfileController::class, 'showPublicForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [ProfileController::class, 'publicForgotPassword'])->name('password.email');
Route::get('/verify-reset-code', [ProfileController::class, 'showPublicVerifyResetCodeForm'])->name('public.verify-reset-code');
Route::post('/verify-reset-code', [ProfileController::class, 'publicVerifyResetCode'])->name('public.verify-reset-code.post');
Route::get('/reset-password', [ProfileController::class, 'showPublicResetPasswordForm'])->name('public.reset-password');
Route::post('/reset-password', [ProfileController::class, 'publicResetPassword'])->name('public.reset-password.post');
Route::post('/resend-reset-code', [ProfileController::class, 'resendPublicResetCode'])->name('public.resend-reset-code');

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/products/photos', [AdminProductController::class, 'photos'])->name('products.photos');

    Route::get('/products/archived', [AdminProductController::class, 'archivedIndex'])->name('products.archived');
    Route::post('/products/{product}/archive', [AdminProductController::class, 'archive'])->name('products.archive');
    Route::post('/products/{id}/restore', [AdminProductController::class, 'restore'])->name('products.restore');

    Route::get('/products/{id}/photos-upload', [AdminProductController::class, 'photosUpload'])->name('products.photos.upload');
    Route::post('/products/{id}/photos-upload', [AdminProductController::class, 'photosUploadStore'])->name('products.photos.uploadStore');

    Route::post('/products/{id}/photo-upload', [AdminProductController::class, 'photosUploadSingle'])->name('products.photos.uploadSingle');

    Route::post('/products/{productId}/photos/{imageId}/set-main', [AdminProductController::class, 'setMainPhoto'])->name('products.photos.setMain');

    Route::post('/products/{productId}/photos/{imageId}/delete', [AdminProductController::class, 'deletePhoto'])->name('products.photos.delete');
    Route::resource('/products', AdminProductController::class)->names('products');

    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('/customers/{customer}/json', [CustomerController::class, 'showJson'])->name('customers.show-json');
    Route::resource('/customers', CustomerController::class)->names('customers');
    Route::resource('/categories'   , CategoryController::class)->names('categories');
    Route::patch('/branches/{branch}/status', [BranchController::class, 'updateStatus'])->name('branches.update-status');
    Route::resource('/branches', BranchController::class)->names('branches');

    Route::post('/sales/cart-sync', [PenjualanController::class, 'syncCart'])->name('sales.cart-sync');
    Route::resource('/sales', PenjualanController::class)->names('sales');
    Route::resource('/purchases', PembelianController::class)->names('purchases');
    Route::get('/sales/export/pdf', [PenjualanController::class, 'exportMonthlyPdf'])->name('sales.export.pdf');
    Route::get('/sales/export/excel', [PenjualanController::class, 'exportMonthlyExcel'])->name('sales.export.excel');
    Route::get('/purchases/export/pdf', [PembelianController::class, 'exportMonthlyPdf'])->name('purchases.export.pdf');
    Route::get('/purchases/export/excel', [PembelianController::class, 'exportMonthlyExcel'])->name('purchases.export.excel');
    Route::post('/purchases/store-item-draft', [PembelianController::class, 'ajaxStoreItemDraft'])->name('purchases.ajaxStoreItemDraft');
    Route::put('/purchases/update-item-draft/{item_id}', [PembelianController::class, 'ajaxUpdateItemDraft'])->name('purchases.ajaxUpdateItemDraft');
    Route::delete('/purchases/delete-item-draft/{item_id}', [PembelianController::class, 'ajaxDeleteItemDraft'])->name('purchases.ajaxDeleteItemDraft');



    Route::get('/quality-control/archived', [QCController::class, 'archived'])->name('quality-control.archived');
    Route::get('/quality-control/history', [QCController::class, 'history'])->name('quality-control.history');

    Route::post('/quality-control/{id}/restore', [QCController::class, 'restore'])->name('quality-control.restore');
    Route::resource('/quality-control', QCController::class)->names('quality-control');

    Route::get('/catalog-settings', [CatalogSettingsController::class, 'edit'])->name('catalog-settings.index');
    Route::post('/catalog-settings', [CatalogSettingsController::class, 'update'])->name('catalog-settings.update');
    Route::delete('/catalog-settings/banner/{id}', [CatalogSettingsController::class, 'destroyBanner'])->name('catalog-settings.banner.destroy');
    Route::delete('/catalog-settings/partner/{id}', [CatalogSettingsController::class, 'destroyPartner'])->name('catalog-settings.partner.destroy');
    Route::delete('/catalog-settings/gallery/{id}', [CatalogSettingsController::class, 'destroyGallery'])->name('catalog-settings.gallery.destroy');
    Route::get('/promotions', function () { return view('admin.promotions'); })->name('promotions');

    Route::get('/smart-stock', [SmartStockController::class, 'index'])->name('smart-stock.index');
    Route::get('/smart-stock/product/{productId}/prediction', [SmartStockController::class, 'getProductPrediction'])->name('smart-stock.product.prediction');
    Route::get('/smart-stock/notifications', [SmartStockController::class, 'getNotifications'])->name('smart-stock.notifications');
    Route::post('/smart-stock/notifications/{notificationId}/read', [SmartStockController::class, 'markAsRead'])->name('smart-stock.notifications.read');

    Route::middleware('role:manager')->group(function () {
        Route::resource('/employees', EmployeeController::class)->names('employees');
        Route::get('/permissions', [PermissionsController::class, 'index'])->name('permissions');
        Route::get('/permissions/create', [PermissionsController::class, 'create'])->name('permissions.create');
        Route::post('/permissions', [PermissionsController::class, 'store'])->name('permissions.store');
        Route::get('/permissions/{id}/edit', [PermissionsController::class, 'edit'])->name('permissions.edit');
        Route::put('/permissions/{id}', [PermissionsController::class, 'update'])->name('permissions.update');
        Route::delete('/permissions/{id}', [PermissionsController::class, 'destroy'])->name('permissions.destroy');
        Route::post('/permissions/{id}/regenerate-token', [PermissionsController::class, 'regenerateToken'])->name('permissions.regenerate-token');
        Route::patch('/permissions/{id}/status', [PermissionsController::class, 'updateStatus'])->name('permissions.update-status');
    });
    
    Route::get('purchases/{id}/print', [PembelianController::class, 'printNota'])->name('purchases.print');
    Route::get('sales/{id}/print', [PenjualanController::class, 'printNota'])->name('sales.print');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/resetPassword', [ProfileController::class, 'resetPassword'])->name('profile.resetPassword.show');
    Route::post('/profile/resetPassword', [ProfileController::class, 'update'])->name('profile.resetPassword.post');

    Route::get('/profile/forgot-password', [ProfileController::class, 'showForgotPasswordForm'])->name('profile.forgot-password.show');
    Route::post('/profile/forgot-password', [ProfileController::class, 'forgotPassword'])->name('profile.forgot-password');
    Route::get('/profile/verify-reset-code', [ProfileController::class, 'showVerifyResetCodeForm'])->name('profile.verify-reset-code');
    Route::post('/profile/verify-reset-code', [ProfileController::class, 'verifyResetCode'])->name('profile.verify-reset-code.post');
    Route::get('/profile/reset-forgotten-password', [ProfileController::class, 'showResetForgottenPasswordForm'])->name('profile.reset-forgotten-password');
    Route::post('/profile/reset-forgotten-password', [ProfileController::class, 'resetForgottenPassword'])->name('profile.reset-forgotten-password.post');
    Route::post('/profile/resend-reset-code', [ProfileController::class, 'resendResetCode'])->name('profile.resend-reset-code');

});
