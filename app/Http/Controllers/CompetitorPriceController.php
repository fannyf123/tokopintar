<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\CompetitorPrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompetitorPriceController extends Controller
{
    public function index(): View
    {
        $items = CompetitorPrice::with('barang')
            ->orderByDesc('tanggal_observasi')
            ->paginate(20);

        $gaps = \App\Models\Barang::where('aktif', true)
            ->whereExists(function ($q) {
                $q->select('id')->from('competitor_prices as cp')
                  ->whereColumn('cp.barang_id', 'barangs.id');
            })
            ->with(['kategori'])
            ->get()
            ->map(function ($b) {
                $latest = CompetitorPrice::where('barang_id', $b->id)
                    ->orderByDesc('tanggal_observasi')->first();
                if (! $latest || $latest->harga_competitor <= 0) return null;
                $delta = $b->harga_jual - $latest->harga_competitor;
                $deltaPct = round(($delta / max(1, $latest->harga_competitor)) * 100, 1);
                return [
                    'barang' => $b, 'competitor' => $latest, 'delta' => $delta, 'delta_pct' => $deltaPct,
                ];
            })->filter()->sortByDesc(fn ($x) => abs($x['delta_pct']))->values();

        return view('competitor.index', compact('items', 'gaps'));
    }

    public function create(): View
    {
        return view('competitor.form', [
            'item' => new CompetitorPrice(['tanggal_observasi' => now()->toDateString()]),
            'barangs' => Barang::orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'competitor_name' => ['required', 'string', 'max:100'],
            'harga_competitor' => ['required', 'integer', 'min:0'],
            'tanggal_observasi' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);
        $data['dibuat_oleh'] = auth()->id();
        CompetitorPrice::create($data);
        return redirect()->route('competitor.index')->with('success', 'Harga kompetitor dicatat.');
    }

    public function destroy(CompetitorPrice $competitor): RedirectResponse
    {
        $competitor->delete();
        return back()->with('success', 'Data dihapus.');
    }
}
