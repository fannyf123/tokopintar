<?php

namespace App\Http\Controllers;

use App\Models\ProductInsight;
use App\Services\InsightService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InsightController extends Controller
{
    public function __construct(private InsightService $svc) {}

    public function index(Request $request): View
    {
        $kelas = $request->query('kelas');
        $abc = $request->query('abc');

        $q = ProductInsight::with('barang.kategori')->orderByDesc('velocity_30');
        if ($kelas) $q->where('kelas', $kelas);
        if ($abc) $q->where('abc_class', $abc);
        $items = $q->paginate(20)->withQueryString();

        $top = ProductInsight::with('barang')
            ->where('kelas', ProductInsight::KELAS_FAST)
            ->orderByDesc('velocity_30')->limit(10)->get();
        $dead = ProductInsight::with('barang')
            ->where('kelas', ProductInsight::KELAS_DEAD)
            ->orderByDesc('days_of_supply')->limit(10)->get();

        return view('insight.index', [
            'items' => $items,
            'top' => $top,
            'dead' => $dead,
            'kelasList' => ProductInsight::KELAS_LIST,
            'kelasFilter' => $kelas,
            'abcFilter' => $abc,
        ]);
    }

    public function regenerate(): RedirectResponse
    {
        $n = $this->svc->recomputeAll();
        return back()->with('success', "Insight di-regenerate untuk {$n} barang.");
    }
}
