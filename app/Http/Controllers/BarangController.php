<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangRequest;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(Request $request): View
    {
        $q = Barang::with('kategori', 'supplier')->orderBy('nama');

        if ($s = $request->query('q')) {
            $q->where(function ($w) use ($s) {
                $w->where('nama', 'like', "%{$s}%")
                  ->orWhere('kode', 'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%");
            });
        }
        if ($kid = $request->query('kategori_id')) {
            $q->where('kategori_id', $kid);
        }

        $items = $q->paginate(15)->withQueryString();
        $kategoris = Kategori::orderBy('nama')->get();

        return view('barang.index', compact('items', 'kategoris'));
    }

    public function create(): View
    {
        return view('barang.form', [
            'barang' => new Barang(['aktif' => true, 'satuan' => 'pcs']),
            'kategoris' => Kategori::orderBy('nama')->get(),
            'suppliers' => Supplier::orderBy('nama')->get(),
        ]);
    }

    public function store(BarangRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['kode'] = $data['kode'] ?: Barang::generateKode();
        $data['aktif'] = (bool) ($data['aktif'] ?? true);
        Barang::create($data);

        return redirect()->route('barang.index')->with('success', 'Barang dibuat.');
    }

    public function edit(Barang $barang): View
    {
        return view('barang.form', [
            'barang' => $barang,
            'kategoris' => Kategori::orderBy('nama')->get(),
            'suppliers' => Supplier::orderBy('nama')->get(),
        ]);
    }

    public function update(BarangRequest $request, Barang $barang): RedirectResponse
    {
        $data = $request->validated();
        $data['aktif'] = (bool) ($data['aktif'] ?? false);
        unset($data['stok_current']);
        $barang->update($data);

        return redirect()->route('barang.index')->with('success', 'Barang diperbarui.');
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        if ($barang->movements()->exists()) {
            $barang->update(['aktif' => false]);
            return redirect()->route('barang.index')->with('info', 'Barang punya histori, dinonaktifkan saja.');
        }
        $barang->delete();
        return redirect()->route('barang.index')->with('success', 'Barang dihapus.');
    }
}
