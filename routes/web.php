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
        Route::resource('events', EventController::class)->except(['show']);
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

        Route::middleware(['role:admin|superadmin'])->prefix('admin')->name('admin.')->group(function () {
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
