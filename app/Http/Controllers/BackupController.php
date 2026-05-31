<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use App\Services\R2Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BackupController extends Controller
{
    public function __construct(private BackupService $backup) {}

    public function index(): View
    {
        return view('backup.index', [
            'summary' => $this->backup->summary(),
            'total' => array_sum($this->backup->summary()),
            'r2ready' => app(R2Service::class)->isConfigured(),
        ]);
    }

    public function download(): Response
    {
        $json = $this->backup->toJson();
        $name = $this->backup->filename();

        return response($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $name . '"',
        ]);
    }

    public function uploadR2(R2Service $r2): RedirectResponse
    {
        if (! $r2->isConfigured()) {
            return back()->with('error', 'Backup cloud (R2) belum diaktifkan.');
        }
        try {
            $key = 'backups/' . $this->backup->filename();
            $r2->put($key, $this->backup->toJson());
            return back()->with('success', 'Cadangan berhasil dikirim ke cloud (R2): ' . $key);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal kirim ke cloud: ' . $e->getMessage());
        }
    }
}
