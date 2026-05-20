<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanLabaController extends Controller
{
    public function __construct(private ReportService $svc) {}

    private function range(Request $request): array
    {
        $preset = $request->query('preset');
        [$start, $end] = match ($preset) {
            'today' => [Carbon::today(), Carbon::today()->endOfDay()],
            'yesterday' => [Carbon::yesterday(), Carbon::yesterday()->endOfDay()],
            '7d' => [Carbon::today()->subDays(6), Carbon::today()->endOfDay()],
            '30d' => [Carbon::today()->subDays(29), Carbon::today()->endOfDay()],
            'this_month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfDay()],
            'this_year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfDay()],
            default => [
                $request->query('start') ? Carbon::parse($request->query('start'))->startOfDay() : Carbon::today()->subDays(29),
                $request->query('end') ? Carbon::parse($request->query('end'))->endOfDay() : Carbon::today()->endOfDay(),
            ],
        };
        $granularity = $request->query('g', 'daily');
        if (! in_array($granularity, ['daily', 'weekly', 'monthly', 'yearly'], true)) {
            $granularity = 'daily';
        }
        return [$start, $end, $granularity];
    }

    public function index(Request $request): View
    {
        [$start, $end, $g] = $this->range($request);
        $data = $this->svc->laba($start, $end, $g);
        return view('laporan.laba', compact('data', 'start', 'end', 'g'));
    }

    public function pdf(Request $request): Response
    {
        [$start, $end, $g] = $this->range($request);
        $data = $this->svc->laba($start, $end, $g);
        $pdf = Pdf::loadView('laporan.laba_pdf', compact('data', 'start', 'end', 'g'));
        return $pdf->download('laporan-laba-' . $start->format('Ymd') . '-' . $end->format('Ymd') . '.pdf');
    }

    public function csv(Request $request): StreamedResponse
    {
        [$start, $end, $g] = $this->range($request);
        $data = $this->svc->laba($start, $end, $g);
        $filename = 'laporan-laba-' . $start->format('Ymd') . '-' . $end->format('Ymd') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Bucket', 'Omzet', 'HPP', 'Laba Kotor', 'Biaya', 'Laba Bersih']);
            foreach ($data['rows'] as $r) {
                fputcsv($out, [
                    $r['bucket'], $r['omzet'], $r['hpp'],
                    $r['laba_kotor'], $r['biaya'], $r['laba_bersih'],
                ]);
            }
            fputcsv($out, [
                'TOTAL', $data['totals']['omzet'], $data['totals']['hpp'],
                $data['totals']['laba_kotor'], $data['totals']['biaya'], $data['totals']['laba_bersih'],
            ]);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
