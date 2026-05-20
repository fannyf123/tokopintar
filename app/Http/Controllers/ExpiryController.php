<?php

namespace App\Http\Controllers;

use App\Models\ProductBatch;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExpiryController extends Controller
{
    public function __construct(private StockService $stock) {}

    public function index(): View
    {
        $items = ProductBatch::with('barang')
            ->where('qty_sisa', '>', 0)
            ->whereNotNull('tanggal_kadaluarsa')
            ->orderBy('tanggal_kadaluarsa')
            ->paginate(30);

        return view('expiry.index', compact('items'));
    }

    public function buang(ProductBatch $batch): RedirectResponse
    {
        if ($batch->qty_sisa <= 0) {
            return back()->with('info', 'Batch sudah kosong.');
        }
        try {
            $this->stock->buangExpiredBatch($batch);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
        return back()->with('success', 'Stok kadaluarsa dibuang dan dicatat di mutasi.');
    }
}
