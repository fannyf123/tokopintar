<?php

namespace App\Console\Commands;

use App\Services\AdvancedInsightService;
use Illuminate\Console\Command;

class RecomputeAssociation extends Command
{
    protected $signature = 'tokopintar:recompute-association';
    protected $description = 'Hitung ulang Apriori association rules untuk basket analysis';

    public function handle(AdvancedInsightService $svc): int
    {
        $this->info('Recomputing association rules (Apriori)...');
        $count = $svc->recomputeAssociationRules();
        $this->info("Done. {$count} aturan asosiasi tersimpan.");
        return self::SUCCESS;
    }
}
