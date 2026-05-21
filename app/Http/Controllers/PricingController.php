<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PriceHistory;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function __construct(private PricingService $svc) {}

    public function simulator(Request $request): View
    {
        $barangId = $request->query('barang_id');
        $hargaBaru = (int) $request->query('harga_baru', 0);
        $result = null;
        $elasticity = null;
        $barang = null;

        if ($barangId) {
            $barang = Barang::find($barangId);
            if ($barang) {
                $elasticity = $this->svc->priceElasticity($barangId);
                if ($hargaBaru > 0) {
                    $result = $this->svc->simulateMargin($barangId, $hargaBaru);
                }
            }
        }

        $barangs = Barang::where('aktif', true)->orderBy('nama')->get();
        return view('pricing.simulator', compact('barangs', 'barang', 'result', 'elasticity', 'hargaBaru'));
    }

    public function history(Barang $barang): View
    {
        $items = PriceHistory::with('user')
            ->where('barang_id', $barang->id)
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('pricing.history', compact('barang', 'items'));
    }
}
