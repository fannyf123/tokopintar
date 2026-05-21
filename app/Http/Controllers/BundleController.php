<?php

namespace App\Http\Controllers;

use App\Models\AssociationRule;
use App\Models\Barang;
use App\Models\Bundle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BundleController extends Controller
{
    public function index(): View
    {
        $items = Bundle::with('barangA', 'barangB')->orderByDesc('lift_score')->get();
        $suggestions = $this->generateSuggestions();
        return view('bundle.index', compact('items', 'suggestions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'barang_a_id' => ['required', 'exists:barangs,id'],
            'barang_b_id' => ['required', 'exists:barangs,id', 'different:barang_a_id'],
            'harga_bundle' => ['required', 'integer', 'min:0'],
        ]);

        $a = Barang::find($data['barang_a_id']);
        $b = Barang::find($data['barang_b_id']);
        $hargaNormal = (int) $a->harga_jual + (int) $b->harga_jual;
        $modal = (int) $a->harga_beli + (int) $b->harga_beli;
        $marginPct = $data['harga_bundle'] > 0 ? round((($data['harga_bundle'] - $modal) / $data['harga_bundle']) * 100, 2) : 0;

        $rule = AssociationRule::where(function ($q) use ($a, $b) {
            $q->where('antecedent_barang_id', $a->id)->where('consequent_barang_id', $b->id);
        })->orWhere(function ($q) use ($a, $b) {
            $q->where('antecedent_barang_id', $b->id)->where('consequent_barang_id', $a->id);
        })->orderByDesc('lift')->first();

        Bundle::create([
            'nama' => $data['nama'],
            'barang_a_id' => $data['barang_a_id'],
            'barang_b_id' => $data['barang_b_id'],
            'harga_bundle' => $data['harga_bundle'],
            'harga_normal' => $hargaNormal,
            'saving' => max(0, $hargaNormal - $data['harga_bundle']),
            'total_margin_pct' => $marginPct,
            'lift_score' => $rule->lift ?? 0,
            'aktif' => true,
        ]);
        return redirect()->route('bundle.index')->with('success', 'Bundle dibuat.');
    }

    public function destroy(Bundle $bundle): RedirectResponse
    {
        $bundle->delete();
        return back()->with('success', 'Bundle dihapus.');
    }

    private function generateSuggestions(): array
    {
        $rules = AssociationRule::with('antecedent', 'consequent')
            ->where('lift', '>=', 1.5)
            ->orderByDesc('lift')
            ->limit(20)
            ->get();

        $existing = Bundle::get(['barang_a_id', 'barang_b_id'])->map(fn ($b) => $b->barang_a_id . '-' . $b->barang_b_id)->toArray();

        $suggestions = [];
        $seen = [];
        foreach ($rules as $r) {
            $a = $r->antecedent;
            $b = $r->consequent;
            if (! $a || ! $b) continue;
            $key = min($a->id, $b->id) . '-' . max($a->id, $b->id);
            if (in_array($key, $seen)) continue;
            $seen[] = $key;

            $hargaNormal = (int) $a->harga_jual + (int) $b->harga_jual;
            $modal = (int) $a->harga_beli + (int) $b->harga_beli;
            $diskon10 = (int) round($hargaNormal * 0.9);
            $marginPct = $diskon10 > 0 ? round((($diskon10 - $modal) / $diskon10) * 100, 2) : 0;

            $suggestions[] = [
                'a' => $a, 'b' => $b,
                'harga_normal' => $hargaNormal,
                'harga_bundle_saran' => $diskon10,
                'saving' => $hargaNormal - $diskon10,
                'margin_pct' => $marginPct,
                'lift' => round($r->lift, 2),
                'sudah_ada' => in_array($a->id . '-' . $b->id, $existing) || in_array($b->id . '-' . $a->id, $existing),
            ];
            if (count($suggestions) >= 10) break;
        }
        return $suggestions;
    }
}
