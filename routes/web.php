<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;

// Registrasi
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/register/send-otp', [AuthController::class, 'sendOtp'])
    ->middleware('throttle:6,1') // max 6 requests per minute
    ->name('register.send-otp');

// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Forgot Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Dashboard
Route::middleware(['check_session', 'record_activity'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('feature_access:dashboard_view');
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('feature_access:dashboard_view');

    // Logout
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    // Catalog
    // Catalog (Public/All authenticated users can view catalog? Or needs permission? No permission feature defined solely for catalog view, assuming public for auth users. Bid needs permission.)
    Route::get('/catalog', [AuctionController::class, 'catalog'])->name('catalog');
    Route::post('/auction/bid', [AuctionController::class, 'placeBid'])->name('auction.bid')->middleware('feature_access:auction_bid');

    // Add Auction
    Route::get('/auction-add', [AuctionController::class, 'add_auction'])->name('auction.add.params')->middleware('feature_access:auction_create');
    Route::post('/auction-add', [AuctionController::class, 'store_auction'])->name('auction.store')->middleware('feature_access:auction_create');

    // Lelangku
    Route::get('/lelangku', [AuctionController::class, 'lelangku'])->name('auction.lelangku');
    Route::post('/lelangku/mulai', [AuctionController::class, 'start_auction'])->name('auction.start')->middleware('feature_access:auction_create');

    // Dapat / Barang Menang (won auctions)
    Route::get('/dapat', [AuctionController::class, 'dapat'])->name('auction.dapat');

    // Moderasi
    Route::get('/moderasi', [AuctionController::class, 'moderasi'])->name('admin.moderasi');
    Route::post('/moderasi/aksi', [AuctionController::class, 'moderasi_aksi'])->name('admin.moderasi_aksi');
    Route::post('/moderasi/suspend', [AuctionController::class, 'suspend_user'])->name('admin.suspend_user');

    // Manajemen Lelang (Admin, Petugas) -> Now controlled by auction_manage_view
    Route::get('/manajemen-lelang', [AuctionController::class, 'manajemen_lelang'])->name('admin.manajemen_lelang')->middleware('feature_access:auction_manage_view');
    Route::post('/manajemen-lelang/cancel', [AuctionController::class, 'cancel_auction'])->name('admin.cancel_auction')->middleware('feature_access:auction_cancel');
    Route::post('/manajemen-lelang/delete', [AuctionController::class, 'delete_auction'])->name('admin.delete_auction')->middleware('feature_access:auction_delete');

    // Lelang Tercancel    // Super Admin Routes -> Now granular
    Route::get('/lelang-tercancel', [AuctionController::class, 'canceled_auctions'])->name('admin.canceled_auctions')->middleware('feature_access:auction_canceled_view');
    Route::post('/lelang-tercancel/uncancel', [AuctionController::class, 'uncancel_auction'])->name('admin.uncancel_auction')->middleware('feature_access:auction_uncancel');

    // Lelang Terhapus (Super Admin)
    Route::get('/lelang-terhapus', [AuctionController::class, 'deleted_auctions'])->name('admin.deleted_auctions')->middleware('feature_access:auction_deleted_view');
    Route::post('/lelang-terhapus/restore', [AuctionController::class, 'restore_auction'])->name('admin.restore_auction')->middleware('feature_access:auction_restore');

    // Setting Website (Super Admin only for now, but use setting_view)
    Route::get('/setting-website', [\App\Http\Controllers\SettingController::class, 'index'])->name('setting.index')->middleware('feature_access:setting_view');
    Route::post('/setting-website/update-hak-akses', [
        \App\Http\Controllers\SettingController::class,
        'updateHakAkses'
    ])->name('SettingController.updateHakAkses')->middleware('feature_access:setting_view');

    Route::post('/setting-website/update-logo', [
        \App\Http\Controllers\SettingController::class,
        'update_logo'
    ])->name('setting.update_logo')->middleware('feature_access:setting_view'); // Logo update tied to setting_view for now
    Route::post('/setting-website/backup', [
        \App\Http\Controllers\SettingController::class,
        'backup'
    ])->middleware(['throttle:2,1', 'feature_access:setting_view'])->name('setting.backup'); // Backup also setting_view

    // Laporan (Admin, Manager, Super Admin) -> report_view
    Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index')->middleware('feature_access:report_view');
    Route::get('/laporan/export/pdf', [ReportController::class, 'exportPdf'])->name('laporan.export.pdf')->middleware('feature_access:report_view');
    Route::get('/laporan/export/excel', [ReportController::class, 'exportExcel'])->name('laporan.export.excel');

    // History Data (Admin, Super Admin) -> Now history_view
    Route::get('/history-data', [HistoryController::class, 'index'])->name('history.index')->middleware('feature_access:history_view');

    // Data User (Admin, Super Admin)
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
});