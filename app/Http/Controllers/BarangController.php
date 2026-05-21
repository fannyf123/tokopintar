<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarangRequest;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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

        $hargaLama = (int) $barang->harga_jual;
        $hargaBaru = (int) ($data['harga_jual'] ?? $hargaLama);

        $barang->update($data);

        if ($hargaLama !== $hargaBaru && $hargaLama > 0) {
            \App\Models\PriceHistory::create([
                'barang_id' => $barang->id,
                'harga_jual_lama' => $hargaLama,
                'harga_jual_baru' => $hargaBaru,
                'delta_persen' => round((($hargaBaru - $hargaLama) / $hargaLama) * 100, 2),
                'diubah_oleh' => auth()->id(),
            ]);
        }

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

    public function lookupBarcode(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Try Open Food Facts (free, ~partial Indonesia coverage for food products)
        try {
            $resp = Http::timeout(5)->withHeaders([
                'User-Agent' => 'TOKOPINTAR/1.0 (umkm-pos)',
            ])->get('https://world.openfoodfacts.org/cgi/search.pl', [
                'search_terms' => $q,
                'search_simple' => 1,
                'action' => 'process',
                'json' => 1,
                'page_size' => 10,
                'fields' => 'code,product_name,brands,quantity,countries_tags',
            ]);
            if ($resp->ok()) {
                foreach ((array) $resp->json('products', []) as $p) {
                    $code = $p['code'] ?? null;
                    $name = trim($p['product_name'] ?? '');
                    if (! $code || $name === '') continue;
                    $results[] = [
                        'barcode' => (string) $code,
                        'nama' => $name,
                        'brand' => $p['brands'] ?? '',
                        'qty' => $p['quantity'] ?? '',
                        'source' => 'OpenFoodFacts',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // ignore, fall through to empty
        }

        return response()->json(['results' => $results]);
    }
}
