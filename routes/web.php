<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpiryController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanLabaController;
use App\Http\Controllers\MutasiStokController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PembelianController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware(['auth', 'aktif'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('password', [ProfileController::class, 'changePassword'])->name('password');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('kategori', KategoriController::class)->except('show');
        Route::resource('pengeluaran', PengeluaranController::class);
    });

    Route::middleware('role:admin,gudang')->group(function () {
        Route::resource('supplier', SupplierController::class)->except('show');
        Route::resource('barang', BarangController::class)->except('show');

        Route::prefix('pembelian')->name('pembelian.')->group(function () {
            Route::get('/', [PembelianController::class, 'index'])->name('index');
            Route::get('create', [PembelianController::class, 'create'])->name('create');
            Route::post('/', [PembelianController::class, 'store'])->name('store');
            Route::get('{pembelian}', [PembelianController::class, 'show'])->name('show');
            Route::post('{pembelian}/terima', [PembelianController::class, 'terima'])->name('terima');
            Route::post('{pembelian}/batal', [PembelianController::class, 'batal'])->name('batal');
        });

        Route::prefix('mutasi')->name('mutasi.')->group(function () {
            Route::get('/', [MutasiStokController::class, 'index'])->name('index');
            Route::get('create', [MutasiStokController::class, 'create'])->name('create');
            Route::post('/', [MutasiStokController::class, 'store'])->name('store');
            Route::get('batches/{barang}', [MutasiStokController::class, 'batches'])->name('batches');
        });

        Route::prefix('expiry')->name('expiry.')->group(function () {
            Route::get('/', [ExpiryController::class, 'index'])->name('index');
            Route::post('{batch}/buang', [ExpiryController::class, 'buang'])->name('buang');
        });
    });

    Route::middleware('role:admin,kasir')->group(function () {
        Route::resource('pelanggan', PelangganController::class)->except('show');

        Route::prefix('pos')->name('pos.')->group(function () {
            Route::get('/', [PenjualanController::class, 'pos'])->name('index');
            Route::get('search', [PenjualanController::class, 'searchBarang'])->name('search');
            Route::post('/', [PenjualanController::class, 'store'])->name('store');
        });

        Route::prefix('penjualan')->name('penjualan.')->group(function () {
            Route::get('/', [PenjualanController::class, 'index'])->name('index');
            Route::get('{penjualan}', [PenjualanController::class, 'show'])->name('show');
            Route::get('{penjualan}/struk', [PenjualanController::class, 'struk'])->name('struk');
        });
    });

    Route::middleware('role:admin')->group(function () {
        Route::prefix('insight')->name('insight.')->group(function () {
            Route::get('/', [InsightController::class, 'index'])->name('index');
            Route::post('regenerate', [InsightController::class, 'regenerate'])->name('regenerate');
        });

        Route::prefix('laporan/laba')->name('laporan.laba.')->group(function () {
            Route::get('/', [LaporanLabaController::class, 'index'])->name('index');
            Route::get('pdf', [LaporanLabaController::class, 'pdf'])->name('pdf');
            Route::get('csv', [LaporanLabaController::class, 'csv'])->name('csv');
        });
    });
});
