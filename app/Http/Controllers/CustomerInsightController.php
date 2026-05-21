<?php

namespace App\Http\Controllers;

use App\Models\CustomerInsight;
use App\Services\CustomerInsightService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerInsightController extends Controller
{
    public function __construct(private CustomerInsightService $svc) {}

    public function index(Request $request): View
    {
        $segment = $request->query('segment');
        $churn = $request->query('churn');

        $q = CustomerInsight::with('pelanggan')->orderByDesc('monetary');
        if ($segment) $q->where('segment', $segment);
        if ($churn === '1') $q->where('churn_risk', true);

        $items = $q->paginate(20)->withQueryString();

        $summary = CustomerInsight::selectRaw('segment, COUNT(*) as cnt, SUM(monetary) as total_value')
            ->groupBy('segment')
            ->get()->keyBy('segment');

        $totalChurn = CustomerInsight::where('churn_risk', true)->count();
        $totalClv = (int) CustomerInsight::sum('clv_estimate');

        return view('customer-insight.index', compact('items', 'segment', 'churn', 'summary', 'totalChurn', 'totalClv'));
    }

    public function regenerate(): RedirectResponse
    {
        $count = $this->svc->recomputeAll();
        return redirect()->route('customer-insight.index')
            ->with('success', "Berhasil hitung ulang {$count} pelanggan.");
    }
}
