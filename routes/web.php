<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Cashier\PosController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReceiptSettingController;
use App\Http\Controllers\Superadmin\EventController;
use App\Http\Controllers\Superadmin\FinancialReportController;
use App\Http\Controllers\Superadmin\TenantController;
use App\Http\Controllers\Accounting\AkunController;
use App\Http\Controllers\Accounting\PengeluaranController;
use App\Http\Controllers\Accounting\JurnalController;
use App\Http\Controllers\Accounting\LaporanAkuntansiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware(['role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::post('/leave-tenant', [TenantController::class, 'leave'])->name('leave-tenant');
        Route::post('/tenants/{tenant}/enter', [TenantController::class, 'enter'])->name('tenants.enter');
        Route::get('/financial', [FinancialReportController::class, 'index'])->name('financial.index');
        Route::get('/financial/tenants/{tenant}', [FinancialReportController::class, 'showTenant'])->name('financial.tenant');
        Route::resource('tenants', TenantController::class)->except(['show']);
    });

    Route::middleware(['tenant'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::prefix('pos')->name('pos.')->group(function () {
            Route::get('/', [PosController::class, 'index'])->name('index');
            Route::post('/cart/add', [PosController::class, 'addToCart'])->name('cart.add');
            Route::patch('/cart/update', [PosController::class, 'updateCart'])->name('cart.update');
            Route::delete('/cart/remove', [PosController::class, 'removeFromCart'])->name('cart.remove');
            Route::delete('/cart/clear', [PosController::class, 'clearCart'])->name('cart.clear');
            Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
        });

        Route::resource('transactions', TransactionController::class)->only(['index', 'show']);
        Route::get('/transactions/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transactions.receipt');
        Route::post('/transactions/{transaction}/void', [TransactionController::class, 'void'])->name('transactions.void');

        // Shift Kasir (Buka & Tutup Laci Kas)
        Route::post('/shifts/open', [\App\Http\Controllers\CashierShiftController::class, 'open'])->name('shifts.open');
        Route::post('/shifts/{shift}/close', [\App\Http\Controllers\CashierShiftController::class, 'close'])->name('shifts.close');

        // Modul Akuntansi & Keuangan SAK Indonesia (Hanya Admin & Superadmin)
        Route::middleware(['role:admin|superadmin'])->prefix('akuntansi')->name('akuntansi.')->group(function () {
            Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
            Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
            Route::delete('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

            Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
            Route::post('/jurnal/manual', [JurnalController::class, 'storeManual'])->name('jurnal.manual');

            Route::get('/laporan/laba-rugi', [LaporanAkuntansiController::class, 'labaRugi'])->name('laporan.laba-rugi');
            Route::get('/laporan/neraca', [LaporanAkuntansiController::class, 'neraca'])->name('laporan.neraca');
            Route::get('/laporan/buku-besar', [LaporanAkuntansiController::class, 'bukuBesar'])->name('laporan.buku-besar');

            Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
            Route::post('/akun', [AkunController::class, 'store'])->name('akun.store');
            Route::post('/akun/reset', [AkunController::class, 'resetDefault'])->name('akun.reset');
            Route::put('/akun/{akun}', [AkunController::class, 'update'])->name('akun.update');
            Route::delete('/akun/{akun}', [AkunController::class, 'destroy'])->name('akun.destroy');
        });

        Route::middleware(['role:admin|superadmin'])->prefix('admin')->name('admin.')->group(function () {
            Route::get('/shifts', [\App\Http\Controllers\CashierShiftController::class, 'index'])->name('shifts.index');
            Route::resource('categories', CategoryController::class);
            Route::resource('products', ProductController::class);

            Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
            Route::get('/reports/daily/export', [ReportController::class, 'exportDaily'])->name('reports.daily.export');
            Route::get('/reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
            Route::get('/reports/monthly/export', [ReportController::class, 'exportMonthly'])->name('reports.monthly.export');

            Route::resource('users', UserController::class);

            Route::get('/receipt-settings', [ReceiptSettingController::class, 'edit'])->name('receipt-settings.edit');
            Route::put('/receipt-settings', [ReceiptSettingController::class, 'update'])->name('receipt-settings.update');

            // Stock Management
            Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
            Route::get('/stock/create', [StockController::class, 'create'])->name('stock.create');
            Route::post('/stock', [StockController::class, 'store'])->name('stock.store');
        });
    });
});

require __DIR__.'/auth.php';
