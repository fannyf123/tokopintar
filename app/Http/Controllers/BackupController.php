<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
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
}
