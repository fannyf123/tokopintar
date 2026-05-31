<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackupService
{
    /**
     * Tabel data bisnis yang dibackup, urut sesuai dependensi (untuk restore).
     * Tabel sistem (cache, jobs, sessions, migrations) sengaja tidak diikutkan.
     */
    public const TABLES = [
        'users',
        'kategoris',
        'suppliers',
        'pelanggans',
        'barangs',
        'product_batches',
        'pembelians',
        'pembelian_details',
        'penjualans',
        'penjualan_details',
        'penjualan_batch_useds',
        'stock_movements',
        'pengeluarans',
        'product_insights',
        'customer_insights',
        'stock_anomalies',
        'association_rules',
        'price_histories',
        'competitor_prices',
        'bundles',
    ];

    public function generate(): array
    {
        $data = [
            'app' => 'tokopintar',
            'version' => 1,
            'generated_at' => now()->toIso8601String(),
            'tables' => [],
        ];

        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $data['tables'][$table] = DB::table($table)->get()
                ->map(fn ($row) => (array) $row)
                ->all();
        }

        return $data;
    }

    public function toJson(): string
    {
        return json_encode($this->generate(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function filename(): string
    {
        return 'tokopintar-backup-' . now()->format('Ymd-His') . '.json';
    }

    /**
     * Ringkasan jumlah baris tiap tabel untuk ditampilkan di halaman backup.
     */
    public function summary(): array
    {
        $out = [];
        foreach (self::TABLES as $table) {
            if (Schema::hasTable($table)) {
                $out[$table] = DB::table($table)->count();
            }
        }
        return $out;
    }
}
