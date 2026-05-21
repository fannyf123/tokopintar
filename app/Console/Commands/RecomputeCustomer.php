<?php

namespace App\Console\Commands;

use App\Services\CustomerInsightService;
use Illuminate\Console\Command;

class RecomputeCustomer extends Command
{
    protected $signature = 'tokopintar:recompute-customer';
    protected $description = 'Hitung ulang RFM segmentation + churn prediction untuk semua pelanggan';

    public function handle(CustomerInsightService $svc): int
    {
        $this->info('Recomputing customer insights...');
        $count = $svc->recomputeAll();
        $this->info("Done. {$count} pelanggan diproses.");
        return self::SUCCESS;
    }
}
