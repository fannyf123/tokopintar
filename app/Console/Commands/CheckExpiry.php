<?php

namespace App\Console\Commands;

use App\Models\ProductBatch;
use Illuminate\Console\Command;

class CheckExpiry extends Command
{
    protected $signature = 'tokopintar:check-expiry';
    protected $description = 'Cek batch yang akan/sudah kadaluarsa, log warning';

    public function handle(): int
    {
        $expired = ProductBatch::with('barang')
            ->where('qty_sisa', '>', 0)
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<', now())
            ->get();

        $near = ProductBatch::with('barang')
            ->where('qty_sisa', '>', 0)
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '>=', now())
            ->whereDate('tanggal_kadaluarsa', '<=', now()->addDays(30))
            ->get();

        $this->warn("EXPIRED ({$expired->count()} batch):");
        foreach ($expired as $b) {
            $this->line(" - {$b->barang?->nama} batch {$b->no_batch} sisa {$b->qty_sisa} (exp {$b->tanggal_kadaluarsa->toDateString()})");
        }

        $this->warn("NEAR EXPIRY <=30 hari ({$near->count()} batch):");
        foreach ($near as $b) {
            $this->line(" - {$b->barang?->nama} batch {$b->no_batch} sisa {$b->qty_sisa} (exp {$b->tanggal_kadaluarsa->toDateString()})");
        }

        return self::SUCCESS;
    }
}
