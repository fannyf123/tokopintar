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

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('panduan', [\App\Http\Controllers\PanduanController::class, 'index'])->name('panduan.index');

    Route::prefix('backup')->name('backup.')->group(function () {
        Route::get('/', [\App\Http\Controllers\BackupController::class, 'index'])->name('index');
        Route::get('download', [\App\Http\Controllers\BackupController::class, 'download'])->name('download');
        Route::post('upload-r2', [\App\Http\Controllers\BackupController::class, 'uploadR2'])->name('upload-r2');
    });

    Route::get('tutup-kasir', [\App\Http\Controllers\TutupKasirController::class, 'index'])->name('tutup-kasir.index');

    Route::prefix('catalog')->name('catalog.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CatalogController::class, 'index'])->name('index');
        Route::post('import', [\App\Http\Controllers\CatalogController::class, 'import'])->name('import');
        Route::post('import-csv', [\App\Http\Controllers\CatalogController::class, 'importCsv'])->name('import-csv');
        Route::get('template', [\App\Http\Controllers\CatalogController::class, 'templateCsv'])->name('template');
    });

    Route::prefix('export')->name('export.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ExportController::class, 'index'])->name('index');
        Route::get('{dataset}/{format}', [\App\Http\Controllers\ExportController::class, 'download'])->name('download');
    });

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('password', [ProfileController::class, 'changePassword'])->name('password');
    });

    Route::resource('kategori', KategoriController::class)->except('show');
    Route::resource('pengeluaran', PengeluaranController::class);

    Route::resource('supplier', SupplierController::class)->except('show');
    Route::get('barang/lookup-barcode', [BarangController::class, 'lookupBarcode'])->name('barang.lookup-barcode');
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

    Route::resource('pelanggan', PelangganController::class)->except('show');

    Route::prefix('pos')->name('pos.')->group(function () {
        Route::get('/', [PenjualanController::class, 'pos'])->name('index');
        Route::get('search', [PenjualanController::class, 'searchBarang'])->name('search');
        Route::get('cross-sell', [PenjualanController::class, 'crossSell'])->name('crosssell');
        Route::post('/', [PenjualanController::class, 'store'])->name('store');
    });

    Route::prefix('penjualan')->name('penjualan.')->group(function () {
        Route::get('/', [PenjualanController::class, 'index'])->name('index');
        Route::get('{penjualan}', [PenjualanController::class, 'show'])->name('show');
        Route::get('{penjualan}/struk', [PenjualanController::class, 'struk'])->name('struk');
        Route::post('{penjualan}/lunasi', [PenjualanController::class, 'lunasi'])->name('lunasi');
        Route::post('{penjualan}/retur', [PenjualanController::class, 'retur'])->name('retur');
    });

    Route::prefix('insight')->name('insight.')->group(function () {
        Route::get('/', [InsightController::class, 'index'])->name('index');
        Route::post('regenerate', [InsightController::class, 'regenerate'])->name('regenerate');
    });

    Route::prefix('asisten')->name('asisten.')->group(function () {
        Route::get('ringkasan', [\App\Http\Controllers\AsistenController::class, 'ringkasan'])->name('ringkasan');
        Route::get('restock', [\App\Http\Controllers\AsistenController::class, 'restock'])->name('restock');
        Route::get('traffic', [\App\Http\Controllers\AsistenController::class, 'traffic'])->name('traffic');
    });

    Route::prefix('customer-insight')->name('customer-insight.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CustomerInsightController::class, 'index'])->name('index');
        Route::post('regenerate', [\App\Http\Controllers\CustomerInsightController::class, 'regenerate'])->name('regenerate');
    });

    Route::prefix('anomaly')->name('anomaly.')->group(function () {
        Route::get('/', [\App\Http\Controllers\AnomalyController::class, 'index'])->name('index');
        Route::post('detect', [\App\Http\Controllers\AnomalyController::class, 'detect'])->name('detect');
        Route::post('{anomaly}/resolve', [\App\Http\Controllers\AnomalyController::class, 'resolve'])->name('resolve');
    });

    Route::prefix('advanced')->name('advanced.')->group(function () {
        Route::get('association-rules', [\App\Http\Controllers\AdvancedInsightController::class, 'associationRules'])->name('rules.index');
        Route::post('association-rules/regenerate', [\App\Http\Controllers\AdvancedInsightController::class, 'regenerateRules'])->name('rules.regenerate');
        Route::get('optimal-stock', [\App\Http\Controllers\AdvancedInsightController::class, 'optimalStock'])->name('stock');
        Route::get('cannibalization', [\App\Http\Controllers\AdvancedInsightController::class, 'cannibalization'])->name('cannibal');
        Route::get('pareto', [\App\Http\Controllers\AdvancedInsightController::class, 'pareto'])->name('pareto');
    });

    Route::prefix('pricing')->name('pricing.')->group(function () {
        Route::get('simulator', [\App\Http\Controllers\PricingController::class, 'simulator'])->name('simulator');
        Route::get('history/{barang}', [\App\Http\Controllers\PricingController::class, 'history'])->name('history');
    });

    Route::prefix('competitor')->name('competitor.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CompetitorPriceController::class, 'index'])->name('index');
        Route::get('create', [\App\Http\Controllers\CompetitorPriceController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\CompetitorPriceController::class, 'store'])->name('store');
        Route::delete('{competitor}', [\App\Http\Controllers\CompetitorPriceController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('bundle')->name('bundle.')->group(function () {
        Route::get('/', [\App\Http\Controllers\BundleController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\BundleController::class, 'store'])->name('store');
        Route::delete('{bundle}', [\App\Http\Controllers\BundleController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('laporan/laba')->name('laporan.laba.')->group(function () {
        Route::get('/', [LaporanLabaController::class, 'index'])->name('index');
        Route::get('pdf', [LaporanLabaController::class, 'pdf'])->name('pdf');
        Route::get('csv', [LaporanLabaController::class, 'csv'])->name('csv');
    });
});
