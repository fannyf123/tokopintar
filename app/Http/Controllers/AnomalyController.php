<?php

namespace App\Http\Controllers;

use App\Models\StockAnomaly;
use App\Services\AnomalyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnomalyController extends Controller
{
    public function __construct(private AnomalyService $svc) {}

    public function index(Request $request): View
    {
        $jenis = $request->query('jenis');
        $severity = $request->query('severity');
        $resolved = $request->query('resolved', '0');

        $q = StockAnomaly::with('barang', 'user')->orderByDesc('created_at');
        if ($jenis) $q->where('jenis', $jenis);
        if ($severity) $q->where('severity', $severity);
        if ($resolved !== '') $q->where('resolved', $resolved === '1');

        $items = $q->paginate(20)->withQueryString();

        $summary = [
            'critical' => StockAnomaly::where('severity', 'critical')->where('resolved', false)->count(),
            'warning' => StockAnomaly::where('severity', 'warning')->where('resolved', false)->count(),
            'info' => StockAnomaly::where('severity', 'info')->where('resolved', false)->count(),
        ];

        return view('anomaly.index', compact('items', 'jenis', 'severity', 'resolved', 'summary'));
    }

    public function detect(): RedirectResponse
    {
        $count = $this->svc->detectAll();
        return redirect()->route('anomaly.index')
            ->with('success', "Selesai cek anomali. Ditemukan {$count} alert baru.");
    }

    public function resolve(StockAnomaly $anomaly): RedirectResponse
    {
        $anomaly->update(['resolved' => true]);
        return back()->with('success', 'Alert ditandai selesai.');
    }
}
