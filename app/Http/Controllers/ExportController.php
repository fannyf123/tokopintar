<?php

namespace App\Http\Controllers;

use App\Services\ExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function __construct(private ExportService $svc) {}

    public function index(): View
    {
        return view('export.index', ['datasets' => $this->svc->datasets()]);
    }

    public function download(string $dataset, string $format)
    {
        $datasets = $this->svc->datasets();
        if (! isset($datasets[$dataset])) {
            abort(404);
        }
        [$header, $rows] = $this->svc->rows($dataset);
        $title = $this->svc->title($dataset);
        $base = 'tokopintar-' . $dataset . '-' . now()->format('Ymd');

        return match ($format) {
            'csv' => $this->csv($header, $rows, $base),
            'excel' => $this->htmlTable($header, $rows, $title, $base, 'xls'),
            'word' => $this->htmlTable($header, $rows, $title, $base, 'doc'),
            'pdf' => $this->pdf($header, $rows, $title, $base),
            default => abort(404),
        };
    }

    private function csv(array $header, array $rows, string $base): StreamedResponse
    {
        return response()->streamDownload(function () use ($header, $rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel baca UTF-8
            fputcsv($out, $header);
            foreach ($rows as $r) {
                fputcsv($out, $r);
            }
            fclose($out);
        }, $base . '.csv', ['Content-Type' => 'text/csv']);
    }

    private function htmlTable(array $header, array $rows, string $title, string $base, string $ext): Response
    {
        $html = view('export.table', compact('header', 'rows', 'title'))->render();
        $mime = $ext === 'xls'
            ? 'application/vnd.ms-excel'
            : 'application/msword';

        return response($html, 200, [
            'Content-Type' => $mime . '; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $base . '.' . $ext . '"',
        ]);
    }

    private function pdf(array $header, array $rows, string $title, string $base): Response
    {
        $pdf = Pdf::loadView('export.table', compact('header', 'rows', 'title'))
            ->setPaper('a4', 'landscape');
        return $pdf->download($base . '.pdf');
    }
}
