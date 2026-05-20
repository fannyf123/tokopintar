<?php

namespace App\Http\Controllers;

use App\Http\Requests\MutasiStokRequest;
use App\Models\Barang;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MutasiStokController extends Controller
{
    public function __construct(private StockService $stock) {}

    public function index(Request $request): View
    {
        $q = StockMovement::with('barang', 'batch', 'user')->orderByDesc('id');
        if ($jenis = $request->query('jenis')) {
            $q->where('jenis', $jenis);
        }
        if ($bid = $request->query('barang_id')) {
            $q->where('barang_id', $bid);
        }
        $items = $q->paginate(20)->withQueryString();
        return view('mutasi.index', [
            'items' => $items,
            'jenisList' => StockMovement::JENIS_LIST,
        ]);
    }

    public function create(): View
    {
        return view('mutasi.form', [
            'barangs' => Barang::orderBy('nama')->get(),
            'jenisList' => StockMovement::JENIS_MUTASI,
        ]);
    }

    public function store(MutasiStokRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $barang = Barang::findOrFail($data['barang_id']);

        try {
            $this->stock->mutasi(
                barang: $barang,
                jenis: $data['jenis'],
                qty: (int) $data['qty'],
                alasan: $data['alasan'],
                batchId: $data['batch_id'] ?? null,
            );
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('mutasi.index')->with('success', 'Mutasi stok dicatat.');
    }

    public function batches(Barang $barang)
    {
        $batches = ProductBatch::where('barang_id', $barang->id)
            ->where('qty_sisa', '>', 0)
            ->orderBy('tanggal_kadaluarsa')
            ->get(['id', 'no_batch', 'qty_sisa', 'tanggal_kadaluarsa']);
        return response()->json($batches);
    }
}
