<?php

namespace App\Http\Controllers;

use App\Services\ProductCatalogService;
use App\Services\ProductImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function __construct(private ProductCatalogService $svc) {}

    public function index(): View
    {
        return view('catalog.index', [
            'summary' => $this->svc->summary(),
            'catalog' => $this->svc->catalog(),
        ]);
    }

    public function import(): RedirectResponse
    {
        $hasil = $this->svc->import();

        if ($hasil['ditambah'] === 0) {
            return back()->with('info', 'Semua produk katalog sudah ada di daftar barang Anda. Tidak ada yang ditambahkan.');
        }

        $pesan = "Berhasil menambah {$hasil['ditambah']} produk contoh"
            . ($hasil['kategori_baru'] > 0 ? " ({$hasil['kategori_baru']} kategori baru)" : '')
            . ". {$hasil['dilewati']} produk dilewati karena sudah ada. "
            . 'Silakan isi harga & stok di menu Daftar Barang — produk masih nonaktif sampai harga diisi.';

        return back()->with('success', $pesan);
    }

    public function importCsv(Request $request, ProductImportService $importer): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ], [
            'file.mimes' => 'File harus berformat CSV. Dari Excel: Save As → CSV.',
            'file.max' => 'Ukuran file maksimal 2MB.',
        ]);

        $hasil = $importer->importCsv($request->file('file')->getRealPath());

        if ($hasil['ditambah'] === 0 && empty($hasil['error'])) {
            return back()->with('info', 'Tidak ada produk baru. Semua sudah ada di daftar barang.');
        }

        $pesan = "Berhasil impor {$hasil['ditambah']} produk. {$hasil['dilewati']} dilewati (sudah ada). "
            . 'Produk dengan harga jual > 0 langsung aktif.';
        if (! empty($hasil['error'])) {
            $pesan .= ' Catatan: ' . implode(' ', array_slice($hasil['error'], 0, 5));
        }

        return back()->with('success', $pesan);
    }

    public function templateCsv(): Response
    {
        $rows = [
            ['nama', 'kategori', 'satuan', 'harga_beli', 'harga_jual', 'stok'],
            ['Contoh Kopi Sachet', 'Kopi & Teh', 'sachet', '1000', '1500', '50'],
            ['Contoh Air Mineral', 'Minuman', 'botol', '2500', '3500', '24'],
        ];
        $out = "\xEF\xBB\xBF";
        foreach ($rows as $r) {
            $out .= implode(',', $r) . "\r\n";
        }

        return response($out, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-import-produk.csv"',
        ]);
    }
}
