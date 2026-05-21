<?php

namespace App\Console\Commands;

use App\Services\AnomalyService;
use Illuminate\Console\Command;

class DetectAnomaly extends Command
{
    protected $signature = 'tokopintar:detect-anomaly';
    protected $description = 'Scan transaksi untuk deteksi anomaly (fraud, stock leak, sales spike/drop)';

    public function handle(AnomalyService $svc): int
    {
        $this->info('Detecting anomalies...');
        $count = $svc->detectAll();
        $this->info("Done. {$count} alert baru ditemukan.");
        return self::SUCCESS;
    }
}
