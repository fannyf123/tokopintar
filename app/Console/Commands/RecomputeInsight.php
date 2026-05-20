<?php

namespace App\Console\Commands;

use App\Services\InsightService;
use Illuminate\Console\Command;

class RecomputeInsight extends Command
{
    protected $signature = 'tokopintar:recompute-insight';
    protected $description = 'Recompute product insights (velocity, DoS, classification, ABC, forecast)';

    public function handle(InsightService $svc): int
    {
        $this->info('Recomputing product insights...');
        $n = $svc->recomputeAll();
        $this->info("Done. {$n} barang diproses.");
        return self::SUCCESS;
    }
}
