<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\KepsekController;
use App\Http\Controllers\GelombangController;

Route::get('/', function () {
    $gelombang = \App\Models\Gelombang::orderBy('tgl_mulai', 'asc')->get();
    return view('beranda', compact('gelombang'));
})->name('beranda');

// Dashboard redirect berdasarkan role
Route::get('/dashboard', [App\Http\Controllers\RoleRedirectController::class, 'redirectToDashboard'])
    ->middleware('auth')
    ->name('dashboard');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/verify-otp', [AuthController::class, 'showOtpVerify'])->name('otp.verify');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes (OTP-based for pendaftar only)
Route::get('/forgot-password', [App\Http\Controllers\ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\ForgotPasswordController::class, 'sendOtp'])->name('password.send-otp');
Route::get('/verify-password-otp', [App\Http\Controllers\ForgotPasswordController::class, 'showVerifyOtpForm'])->name('password.verify-otp');
Route::post('/verify-password-otp', [App\Http\Controllers\ForgotPasswordController::class, 'verifyOtp'])->name('password.verify-otp');
Route::get('/reset-password', [App\Http\Controllers\ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [App\Http\Controllers\ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Role-based Dashboard Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/dashboard-enhanced', function() {
        return app(AdminController::class)->dashboard();
    })->name('admin.dashboard.enhanced');
    
    // User Management
    Route::resource('admin/users', App\Http\Controllers\Admin\UserController::class, [
        'names' => [
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy'
        ]
    ]);
    Route::patch('/admin/users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    
    // Gelombang Management
    Route::resource('admin/gelombang', GelombangController::class, [
        'names' => [
            'index' => 'admin.gelombang.index',
            'create' => 'admin.gelombang.create',
            'store' => 'admin.gelombang.store',
            'edit' => 'admin.gelombang.edit',
            'update' => 'admin.gelombang.update',
            'destroy' => 'admin.gelombang.destroy'
        ]
    ]);
    Route::patch('/admin/gelombang/{gelombang}/toggle-status', [GelombangController::class, 'toggleStatus'])->name('admin.gelombang.toggle-status');
    
    // System Settings
    Route::get('/admin/system-settings', [App\Http\Controllers\SystemSettingController::class, 'index'])->name('admin.system-settings');
    Route::put('/admin/system-settings', [App\Http\Controllers\SystemSettingController::class, 'update'])->name('admin.system-settings.update');
});

Route::middleware(['auth', 'role:keuangan'])->group(function () {
    Route::get('/keuangan/dashboard', [KeuanganController::class, 'dashboard'])->name('keuangan.dashboard');
    Route::get('/keuangan/dashboard-enhanced', function() {
        return app(KeuanganController::class)->dashboard();
    })->name('keuangan.dashboard.enhanced');
    Route::get('/keuangan/verifikasi-pembayaran', [KeuanganController::class, 'verifikasiPembayaran'])->name('keuangan.verifikasi');
    Route::post('/keuangan/pembayaran/{id}', [KeuanganController::class, 'prosesPembayaran'])->name('keuangan.proses');
    Route::get('/keuangan/rekap', [KeuanganController::class, 'rekapKeuangan'])->name('keuangan.rekap');
    Route::get('/keuangan/export/excel', [KeuanganController::class, 'exportExcel'])->name('keuangan.export.excel');
    Route::get('/keuangan/export/pdf', [KeuanganController::class, 'exportPdf'])->name('keuangan.export.pdf');
});

Route::middleware(['auth', 'role:kepsek'])->group(function () {
    Route::get('/kepsek/dashboard', [KepsekController::class, 'dashboard'])->name('kepsek.dashboard');
    Route::get('/kepsek/laporan', [KepsekController::class, 'laporanEksekutif'])->name('kepsek.laporan');
    Route::get('/kepsek/peta-sebaran', [App\Http\Controllers\MapController::class, 'index'])->name('kepsek.peta-sebaran');
    Route::get('/kepsek/dashboard-executive', [App\Http\Controllers\DashboardController::class, 'executiveDashboard'])->name('kepsek.dashboard.executive');
    Route::get('/kepsek/export-executive-pdf', [App\Http\Controllers\DashboardController::class, 'exportExecutivePdf'])->name('kepsek.export-executive-pdf');
    Route::get('/kepsek/laporan/export/excel', [KepsekController::class, 'exportLaporanExcel'])->name('kepsek.laporan.export.excel');
    Route::get('/kepsek/laporan/export/pdf', [KepsekController::class, 'exportLaporanPdf'])->name('kepsek.laporan.export.pdf');
});

Route::middleware(['auth', 'role:verifikator_adm'])->group(function () {
    Route::get('/verifikator/dashboard', [App\Http\Controllers\VerifikatorController::class, 'dashboard'])->name('verifikator.dashboard');
    Route::get('/verifikator/administrasi', [App\Http\Controllers\VerifikatorController::class, 'administrasi'])->name('verifikator.administrasi');
    Route::get('/verifikator/verifikasi', [App\Http\Controllers\VerifikatorController::class, 'verifikasi'])->name('verifikator.verifikasi');
    Route::get('/verifikator/detail/{id}', [App\Http\Controllers\VerifikatorController::class, 'detailPendaftar'])->name('verifikator.detail');
    Route::post('/verifikator/verifikasi/{id}', [App\Http\Controllers\VerifikatorController::class, 'prosesVerifikasi'])->name('verifikator.proses');
});

// Siswa routes with CSRF excluded
Route::middleware(['auth', 'role:pendaftar'])->group(function () {
    Route::get('/siswa/dashboard', [App\Http\Controllers\SiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/siswa/profile', [App\Http\Controllers\SiswaController::class, 'profile'])->name('siswa.profile');
    Route::get('/siswa/berkas', [App\Http\Controllers\SiswaController::class, 'berkas'])->name('siswa.berkas');
    Route::get('/siswa/status', [App\Http\Controllers\SiswaController::class, 'status'])->name('siswa.status');
    Route::get('/siswa/status/ajax', [App\Http\Controllers\SiswaController::class, 'statusAjax'])->name('siswa.status.ajax');
    Route::get('/siswa/pembayaran', [App\Http\Controllers\SiswaController::class, 'pembayaran'])->name('siswa.pembayaran');
    Route::get('/siswa/bayar', [App\Http\Controllers\SiswaController::class, 'bayar'])->name('siswa.bayar');
    Route::get('/siswa/cetak-kartu', [App\Http\Controllers\SiswaController::class, 'cetakKartu'])->name('siswa.cetak-kartu');
    Route::get('/siswa/cetak-kartu-pdf', [App\Http\Controllers\SiswaController::class, 'cetakKartuPdf'])->name('siswa.cetak-kartu.pdf');
    Route::get('/siswa/cetak-bukti-pdf', [App\Http\Controllers\SiswaController::class, 'cetakBuktiPdf'])->name('siswa.cetak-bukti.pdf');
    Route::get('/siswa/cetak-pengumuman-pdf', [App\Http\Controllers\SiswaController::class, 'cetakPengumumanPdf'])->name('siswa.cetak-pengumuman.pdf');
});

// Siswa form routes with proper CSRF protection
Route::middleware(['auth', 'role:pendaftar'])->group(function () {
    Route::get('/siswa/pendaftaran', [App\Http\Controllers\SiswaController::class, 'pendaftaran'])->name('siswa.pendaftaran');
    Route::post('/siswa/pendaftaran', [App\Http\Controllers\SiswaController::class, 'storePendaftaran'])->name('siswa.pendaftaran.store');
    Route::post('/siswa/berkas', [App\Http\Controllers\SiswaController::class, 'uploadBerkas'])->name('siswa.berkas.upload');
    Route::delete('/siswa/berkas/{id}', [App\Http\Controllers\SiswaController::class, 'deleteBerkas'])->name('siswa.berkas.delete');
    Route::post('/siswa/pembayaran', [App\Http\Controllers\SiswaController::class, 'uploadBuktiBayar'])->name('siswa.pembayaran.upload');
    Route::post('/siswa/bayar', [App\Http\Controllers\SiswaController::class, 'uploadBuktiBayar'])->name('siswa.bayar.upload');
});

// Secure file serving for berkas
Route::middleware(['auth'])->group(function () {
    Route::get('/berkas/{filename}', [App\Http\Controllers\SiswaController::class, 'serveBerkas'])->name('berkas.serve');
});

Route::get('/tentang', function () {
    return view('tentang');
})->name('tentang');

Route::get('/prestasi-fasilitas', function () {
    return view('prestasi-fasilitas');
})->name('prestasi-fasilitas');

Route::get('/pendaftaran', function () {
    $gelombang = \App\Models\Gelombang::where('status', 'aktif')
                    ->orderBy('tgl_mulai', 'asc')
                    ->get();
    return view('pendaftaran', compact('gelombang'));
})->name('pendaftaran');

Route::get('/jurusan', [App\Http\Controllers\AdminController::class, 'jurusanPublic'])->name('jurusan');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/master-data', [App\Http\Controllers\AdminController::class, 'masterData'])->name('admin.master-data');
    Route::get('/admin/monitoring-berkas', [App\Http\Controllers\AdminController::class, 'monitoringBerkas'])->name('admin.monitoring-berkas');
    Route::get('/admin/peta-sebaran', [App\Http\Controllers\AdminController::class, 'petaSebaran'])->name('admin.peta-sebaran');
    Route::get('/admin/profile', [App\Http\Controllers\AdminController::class, 'profile'])->name('admin.profile');
});

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::get('/admin/register', function () {
    return view('admin.register');
})->name('admin.register');

// Admin CRUD Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/admin/jurusan', [App\Http\Controllers\AdminController::class, 'storeJurusan'])->name('admin.jurusan.store');
    Route::put('/admin/jurusan/{id}', [App\Http\Controllers\AdminController::class, 'updateJurusan'])->name('admin.jurusan.update');
    Route::delete('/admin/jurusan/{id}', [App\Http\Controllers\AdminController::class, 'deleteJurusan'])->name('admin.jurusan.delete');

    Route::post('/admin/gelombang', [App\Http\Controllers\AdminController::class, 'storeGelombang'])->name('admin.gelombang.store');
    Route::put('/admin/gelombang/{id}', [App\Http\Controllers\AdminController::class, 'updateGelombang'])->name('admin.gelombang.update');
    Route::delete('/admin/gelombang/{id}', [App\Http\Controllers\AdminController::class, 'deleteGelombang'])->name('admin.gelombang.delete');
    Route::patch('/admin/gelombang/{id}/toggle-status', [App\Http\Controllers\AdminController::class, 'toggleGelombangStatus'])->name('admin.gelombang.toggle-status');

    Route::post('/admin/persyaratan', [App\Http\Controllers\AdminController::class, 'storePersyaratan'])->name('admin.persyaratan.store');
    Route::put('/admin/persyaratan/{id}', [App\Http\Controllers\AdminController::class, 'updatePersyaratan'])->name('admin.persyaratan.update');
    Route::delete('/admin/persyaratan/{id}', [App\Http\Controllers\AdminController::class, 'deletePersyaratan'])->name('admin.persyaratan.delete');

    Route::post('/admin/wilayah', [App\Http\Controllers\AdminController::class, 'storeWilayah'])->name('admin.wilayah.store');
    Route::put('/admin/wilayah/{id}', [App\Http\Controllers\AdminController::class, 'updateWilayah'])->name('admin.wilayah.update');
    Route::delete('/admin/wilayah/{id}', [App\Http\Controllers\AdminController::class, 'deleteWilayah'])->name('admin.wilayah.delete');
    Route::get('/admin/wilayah/{id}/delete', [App\Http\Controllers\AdminController::class, 'deleteWilayah'])->name('admin.wilayah.delete.get');

    Route::post('/admin/verifikasi-berkas/{id}', [App\Http\Controllers\AdminController::class, 'verifikasiBerkas'])->name('admin.verifikasi-berkas');
    Route::get('/admin/export-excel', [App\Http\Controllers\AdminController::class, 'exportExcel'])->name('admin.export-excel');
    Route::get('/admin/export-pdf', [App\Http\Controllers\AdminController::class, 'exportPdf'])->name('admin.export-pdf');
});

// Pengumuman routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/pengumuman', [App\Http\Controllers\AdminController::class, 'pengumuman'])->name('admin.pengumuman');
    Route::post('/admin/pengumuman/{id}', [App\Http\Controllers\AdminController::class, 'setPengumuman'])->name('admin.pengumuman.set');
});

// API Routes untuk Wilayah
Route::get('/api/wilayah/provinsi', [App\Http\Controllers\WilayahApiController::class, 'getProvinsi']);
Route::get('/api/wilayah/kabupaten/{provinsi}', [App\Http\Controllers\WilayahApiController::class, 'getKabupaten']);
Route::get('/api/wilayah/kecamatan/{provinsi}/{kabupaten}', [App\Http\Controllers\WilayahApiController::class, 'getKecamatan']);
Route::get('/api/wilayah/kelurahan/{provinsi}/{kabupaten}/{kecamatan}', [App\Http\Controllers\WilayahApiController::class, 'getKelurahan']);
Route::get('/api/wilayah/search', [App\Http\Controllers\WilayahApiController::class, 'searchWilayah']);

// API Routes untuk Map
Route::get('/api/map/data', [App\Http\Controllers\MapController::class, 'getMapData']);
Route::get('/api/map/heatmap', [App\Http\Controllers\MapController::class, 'getHeatmapData']);
Route::get('/api/map/clusters', [App\Http\Controllers\MapController::class, 'getClusterData']);
Route::post('/api/map/geocode', [App\Http\Controllers\MapController::class, 'geocodeAddress']);
Route::post('/api/map/reverse-geocode', [App\Http\Controllers\MapController::class, 'reverseGeocode']);
Route::get('/api/map/area-statistics', [App\Http\Controllers\MapController::class, 'getAreaStatistics']);
Route::get('/api/map/export', [App\Http\Controllers\MapController::class, 'exportMapData']);

// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications/preferences', [App\Http\Controllers\NotificationController::class, 'preferences'])->name('notifications.preferences');
    Route::post('/notifications/preferences', [App\Http\Controllers\NotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');
    Route::get('/notifications/history', [App\Http\Controllers\NotificationController::class, 'history'])->name('notifications.history');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/test', [App\Http\Controllers\NotificationController::class, 'sendTestNotification'])->name('notifications.test');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/notifications', [App\Http\Controllers\NotificationController::class, 'adminNotifications'])->name('admin.notifications');
    Route::post('/admin/notifications/{id}/retry', [App\Http\Controllers\NotificationController::class, 'retryNotification'])->name('admin.notifications.retry');
    
    // Audit Log Routes
    Route::get('/admin/audit-logs', [App\Http\Controllers\AdminController::class, 'auditLogs'])->name('admin.audit-logs.index');
    Route::get('/admin/audit-logs/{id}', [App\Http\Controllers\AuditLogController::class, 'show'])->name('admin.audit-logs.show');
    Route::get('/admin/audit-logs/login-attempts', [App\Http\Controllers\AuditLogController::class, 'loginAttempts'])->name('admin.audit-logs.login-attempts');
    Route::get('/admin/audit-logs/export/excel', [App\Http\Controllers\AuditLogController::class, 'exportExcel'])->name('admin.audit-logs.export.excel');
    Route::get('/admin/audit-logs/export/pdf', [App\Http\Controllers\AuditLogController::class, 'exportPdf'])->name('admin.audit-logs.export.pdf');
    Route::get('/admin/security-dashboard', [App\Http\Controllers\AuditLogController::class, 'dashboard'])->name('admin.security-dashboard');
    
    // Map Routes
    Route::get('/admin/peta-sebaran', [App\Http\Controllers\MapController::class, 'index'])->name('admin.peta-sebaran');
    Route::get('/admin/peta-sebaran-enhanced', function() {
        $jurusan = \App\Models\Jurusan::all();
        $gelombang = \App\Models\Gelombang::all();
        return view('admin.peta-sebaran-enhanced', compact('jurusan', 'gelombang'));
    })->name('admin.peta-sebaran.enhanced');
});

// Report Routes
Route::middleware(['auth', 'role:admin,kepsek'])->group(function () {
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [App\Http\Controllers\ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::post('/reports/export/excel', [App\Http\Controllers\ReportController::class, 'exportExcel']);
    Route::get('/reports/export/pdf', [App\Http\Controllers\ReportController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::post('/reports/export/background', [App\Http\Controllers\ReportController::class, 'exportBackground'])->name('reports.export.background');
});

// Payment Routes
Route::middleware(['auth', 'role:pendaftar'])->group(function () {
    Route::get('/payment', [App\Http\Controllers\PaymentController::class, 'index'])->name('payment.index');
    Route::post('/payment/create', [App\Http\Controllers\PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payment/manual/create', [App\Http\Controllers\PaymentController::class, 'createManual'])->name('payment.manual.create');
    Route::get('/payment/manual/{transaction}', [App\Http\Controllers\PaymentController::class, 'showManual'])->name('payment.manual.show');
    Route::get('/payment/status/{orderId}', [App\Http\Controllers\PaymentController::class, 'status'])->name('payment.status');
    Route::post('/payment/cancel/{orderId}', [App\Http\Controllers\PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::get('/payment/finish', [App\Http\Controllers\PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/unfinish', [App\Http\Controllers\PaymentController::class, 'unfinish'])->name('payment.unfinish');
    Route::get('/payment/error', [App\Http\Controllers\PaymentController::class, 'error'])->name('payment.error');
});

// Payment Webhook (no auth required)
Route::post('/webhook/midtrans', [App\Http\Controllers\PaymentController::class, 'webhook'])->name('payment.webhook');

// Admin Payment Routes
Route::middleware(['auth', 'role:admin,keuangan'])->group(function () {
    Route::get('/admin/payment/dashboard', [App\Http\Controllers\PaymentController::class, 'dashboard'])->name('admin.payment.dashboard');
    Route::get('/admin/payment', [App\Http\Controllers\PaymentController::class, 'adminIndex'])->name('admin.payment.index');
    Route::get('/admin/payment/{transaction}', [App\Http\Controllers\PaymentController::class, 'adminShow'])->name('admin.payment.show');
    Route::post('/admin/payment/{transaction}/refund', [App\Http\Controllers\PaymentController::class, 'adminRefund'])->name('admin.payment.refund');
});

// Advanced Dashboard Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard-advanced', [App\Http\Controllers\DashboardController::class, 'adminDashboard'])->name('dashboard.admin-advanced');
    Route::get('/dashboard/refresh-cache', [App\Http\Controllers\DashboardController::class, 'refreshCache'])->name('dashboard.refresh-cache');
    Route::get('/dashboard/export-executive-pdf', [App\Http\Controllers\DashboardController::class, 'exportExecutivePdf'])->name('dashboard.export-executive-pdf');
    Route::get('/dashboard/system-health', [App\Http\Controllers\DashboardController::class, 'systemHealth'])->name('dashboard.system-health');
});

Route::middleware(['auth', 'role:kepsek'])->group(function () {
    Route::get('/kepsek/dashboard-executive', [App\Http\Controllers\DashboardController::class, 'executiveDashboard'])->name('dashboard.executive');
});

// Dashboard API Routes
Route::middleware(['auth', 'role:admin,kepsek'])->group(function () {
    Route::get('/api/dashboard/kpi', [App\Http\Controllers\DashboardController::class, 'apiKpi'])->name('api.dashboard.kpi');
    Route::get('/api/dashboard/registration-trend', [App\Http\Controllers\DashboardController::class, 'apiRegistrationTrend'])->name('api.dashboard.registration-trend');
    Route::get('/api/dashboard/jurusan-distribution', [App\Http\Controllers\DashboardController::class, 'apiJurusanDistribution'])->name('api.dashboard.jurusan-distribution');
    Route::get('/api/dashboard/payment-analytics', [App\Http\Controllers\DashboardController::class, 'apiPaymentAnalytics'])->name('api.dashboard.payment-analytics');
    Route::get('/api/dashboard/geographic-data', [App\Http\Controllers\DashboardController::class, 'apiGeographicData'])->name('api.dashboard.geographic-data');
    Route::get('/api/dashboard/performance-metrics', [App\Http\Controllers\DashboardController::class, 'apiPerformanceMetrics'])->name('api.dashboard.performance-metrics');
    Route::get('/api/dashboard/drill-down/jurusan/{jurusanId}', [App\Http\Controllers\DashboardController::class, 'drillDownJurusan'])->name('api.dashboard.drill-down.jurusan');
    Route::get('/api/dashboard/drill-down/payment/{status}', [App\Http\Controllers\DashboardController::class, 'drillDownPayment'])->name('api.dashboard.drill-down.payment');
});

// CSRF Token refresh
Route::get('/csrf-token', function() {
    return response()->json(['token' => csrf_token()]);
})->middleware('web');

// Alternative route without middleware
Route::any('/refresh-token', function() {
    return csrf_token();
});

// Test route untuk debug CSRF
Route::get('/test-csrf', function() {
    return [
        'csrf_token' => csrf_token(),
        'session_id' => session()->getId(),
        'session_started' => session()->isStarted(),
        'middleware_except' => app('App\Http\Middleware\VerifyCsrfToken')->getExcept()
    ];
});