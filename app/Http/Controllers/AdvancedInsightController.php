<?php

namespace App\Http\Controllers;

use App\Models\AssociationRule;
use App\Models\Barang;
use App\Services\AdvancedInsightService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvancedInsightController extends Controller
{
    public function __construct(private AdvancedInsightService $svc) {}

    public function associationRules(Request $request): View
    {
        $items = AssociationRule::with('antecedent', 'consequent')
            ->orderByDesc('lift')
            ->orderByDesc('confidence')
            ->paginate(20);

        return view('advanced-insight.association', compact('items'));
    }

    public function regenerateRules(): RedirectResponse
    {
        $count = $this->svc->recomputeAssociationRules();
        return back()->with('success', "Berhasil hitung ulang {$count} aturan asosiasi.");
    }

    public function optimalStock(Request $request): View
    {
        $barangId = $request->query('barang_id');
        $result = null;
        $forecast = null;
        $barang = null;

        if ($barangId) {
            $barang = Barang::find($barangId);
            if ($barang) {
                $result = $this->svc->optimalStockLevel($barangId);
                $forecast = $this->svc->holtWintersForecast($barangId);
            }
        }

        $barangs = Barang::where('aktif', true)->orderBy('nama')->get();
        return view('advanced-insight.optimal-stock', compact('barangs', 'barang', 'result', 'forecast'));
    }

    public function cannibalization(): View
    {
        $items = $this->svc->detectCannibalization();
        return view('advanced-insight.cannibalization', compact('items'));
    }

    public function pareto(): View
    {
        $data = $this->svc->paretoCheck();
        return view('advanced-insight.pareto', compact('data'));
    }
}
