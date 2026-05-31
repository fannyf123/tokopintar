<?php

namespace App\Http\Controllers;

use App\Services\ProductCatalogService;
use Illuminate\Http\RedirectResponse;
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
}
