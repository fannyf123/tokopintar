<?php

namespace App\Services;

use App\Models\AssociationRule;
use App\Models\Barang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdvancedInsightService
{
    public const APRIORI_MIN_SUPPORT = 0.01;
    public const APRIORI_MIN_CONFIDENCE = 0.20;
    public const SERVICE_LEVEL_Z = 1.65;

    public function recomputeAssociationRules(int $windowDays = 90): int
    {
        AssociationRule::truncate();
        $sejak = Carbon::today()->subDays($windowDays);

        $totalTrx = (int) DB::table('penjualans')
            ->where('status', 'lunas')
            ->where('tanggal', '>=', $sejak)
            ->count();

        if ($totalTrx < 20) return 0;

        $itemFreq = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('p.tanggal', '>=', $sejak)
            ->groupBy('pd.barang_id')
            ->select('pd.barang_id', DB::raw('COUNT(DISTINCT pd.penjualan_id) as trx_cnt'))
            ->pluck('trx_cnt', 'pd.barang_id');

        $minTrxCount = max(2, (int) ceil($totalTrx * self::APRIORI_MIN_SUPPORT));

        $pairs = DB::table('penjualan_details as a')
            ->join('penjualan_details as b', function ($j) {
                $j->on('a.penjualan_id', '=', 'b.penjualan_id')
                  ->whereColumn('a.barang_id', '<>', 'b.barang_id');
            })
            ->join('penjualans as p', 'p.id', '=', 'a.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('p.tanggal', '>=', $sejak)
            ->groupBy('a.barang_id', 'b.barang_id')
            ->select('a.barang_id as ant', 'b.barang_id as cons', DB::raw('COUNT(DISTINCT a.penjualan_id) as co'))
            ->havingRaw('COUNT(DISTINCT a.penjualan_id) >= ?', [$minTrxCount])
            ->get();

        $count = 0;
        foreach ($pairs as $p) {
            $support = $p->co / $totalTrx;
            $antTrx = (int) ($itemFreq[$p->ant] ?? 0);
            $consTrx = (int) ($itemFreq[$p->cons] ?? 0);
            if ($antTrx === 0 || $consTrx === 0) continue;

            $confidence = $p->co / $antTrx;
            if ($confidence < self::APRIORI_MIN_CONFIDENCE) continue;

            $expectedConfidence = $consTrx / $totalTrx;
            $lift = $expectedConfidence > 0 ? $confidence / $expectedConfidence : 0;

            AssociationRule::updateOrCreate(
                ['antecedent_barang_id' => $p->ant, 'consequent_barang_id' => $p->cons],
                [
                    'support' => round($support, 4),
                    'confidence' => round($confidence, 4),
                    'lift' => round($lift, 4),
                    'co_count' => (int) $p->co,
                    'dihitung_pada' => now(),
                ]
            );
            $count++;
        }
        return $count;
    }

    public function holtWintersForecast(int $barangId, int $windowDays = 60, int $forecastHorizon = 7): array
    {
        $sejak = Carbon::today()->subDays($windowDays);
        $rows = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('p.tanggal', '>=', $sejak)
            ->where('pd.barang_id', $barangId)
            ->selectRaw('DATE(p.tanggal) as d, SUM(pd.qty) as q')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('q', 'd');

        if ($rows->count() < 14) {
            return ['forecast_7' => 0, 'method' => 'insufficient_data'];
        }

        $alpha = 0.3;
        $beta = 0.1;
        $gamma = 0.2;
        $period = 7;

        $values = $rows->values()->map(fn ($v) => (float) $v)->toArray();
        $n = count($values);

        $level = $values[0];
        $trend = ($values[$period] ?? $values[$n - 1]) - $values[0];
        $trend = $trend / $period;

        $seasonal = array_fill(0, $period, 0);
        for ($i = 0; $i < min($period, $n); $i++) {
            $seasonal[$i] = $values[$i] - $level;
        }

        for ($t = 0; $t < $n; $t++) {
            $s = $seasonal[$t % $period];
            $newLevel = $alpha * ($values[$t] - $s) + (1 - $alpha) * ($level + $trend);
            $newTrend = $beta * ($newLevel - $level) + (1 - $beta) * $trend;
            $seasonal[$t % $period] = $gamma * ($values[$t] - $newLevel) + (1 - $gamma) * $s;
            $level = $newLevel;
            $trend = $newTrend;
        }

        $totalForecast = 0;
        for ($h = 1; $h <= $forecastHorizon; $h++) {
            $f = $level + $h * $trend + $seasonal[($n + $h - 1) % $period];
            $totalForecast += max(0, $f);
        }
        return ['forecast_7' => round($totalForecast, 2), 'method' => 'holt_winters'];
    }

    public function optimalStockLevel(int $barangId, int $windowDays = 30, int $leadTimeDays = 3): array
    {
        $sejak = Carbon::today()->subDays($windowDays);
        $daily = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('p.tanggal', '>=', $sejak)
            ->where('pd.barang_id', $barangId)
            ->selectRaw('DATE(p.tanggal) as d, SUM(pd.qty) as q')
            ->groupBy('d')
            ->pluck('q')
            ->map(fn ($v) => (float) $v);

        if ($daily->count() < 7) {
            return [
                'reorder_point' => 0,
                'safety_stock' => 0,
                'eoq' => 0,
                'method' => 'insufficient_data',
            ];
        }

        $mean = $daily->avg();
        $variance = $daily->map(fn ($v) => ($v - $mean) ** 2)->avg();
        $std = sqrt($variance);

        $safetyStock = (int) ceil(self::SERVICE_LEVEL_Z * $std * sqrt($leadTimeDays));
        $reorderPoint = (int) ceil($mean * $leadTimeDays + $safetyStock);
        $eoq = (int) ceil($mean * 14);

        return [
            'reorder_point' => $reorderPoint,
            'safety_stock' => $safetyStock,
            'eoq' => $eoq,
            'demand_avg' => round($mean, 2),
            'demand_std' => round($std, 2),
            'method' => 'normal_dist_95',
        ];
    }

    public function detectCannibalization(int $windowDays = 30): array
    {
        $sejak = Carbon::today()->subDays($windowDays);
        $prevPeriod = Carbon::today()->subDays($windowDays * 2);

        $current = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('p.tanggal', '>=', $sejak)
            ->groupBy('pd.barang_id')
            ->select('pd.barang_id', DB::raw('SUM(pd.qty) as q'))
            ->pluck('q', 'pd.barang_id');

        $previous = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->whereBetween('p.tanggal', [$prevPeriod, $sejak])
            ->groupBy('pd.barang_id')
            ->select('pd.barang_id', DB::raw('SUM(pd.qty) as q'))
            ->pluck('q', 'pd.barang_id');

        $declining = [];
        $rising = [];
        foreach ($previous as $bid => $prevQty) {
            $curQty = (int) ($current[$bid] ?? 0);
            $prevQty = (int) $prevQty;
            if ($prevQty <= 5) continue;
            $delta = ($curQty - $prevQty) / $prevQty;
            if ($delta <= -0.4) {
                $declining[$bid] = ['qty_prev' => $prevQty, 'qty_now' => $curQty, 'delta' => $delta];
            } elseif ($delta >= 0.5) {
                $rising[$bid] = ['qty_prev' => $prevQty, 'qty_now' => $curQty, 'delta' => $delta];
            }
        }

        $cannibals = [];
        foreach ($declining as $declId => $declData) {
            $declBarang = Barang::with('kategori')->find($declId);
            if (! $declBarang) continue;
            foreach ($rising as $riseId => $riseData) {
                $riseBarang = Barang::find($riseId);
                if (! $riseBarang) continue;
                if ($riseBarang->kategori_id === $declBarang->kategori_id) {
                    $cannibals[] = [
                        'declining_id' => $declId,
                        'declining_nama' => $declBarang->nama,
                        'rising_id' => $riseId,
                        'rising_nama' => $riseBarang->nama,
                        'kategori' => $declBarang->kategori?->nama ?? '-',
                        'declining_delta' => round($declData['delta'] * 100, 1),
                        'rising_delta' => round($riseData['delta'] * 100, 1),
                    ];
                }
            }
        }
        return $cannibals;
    }

    public function paretoCheck(int $windowDays = 30, int $prevWindowDays = 60): array
    {
        $sejak = Carbon::today()->subDays($windowDays);
        $prevStart = Carbon::today()->subDays($prevWindowDays);
        $prevEnd = Carbon::today()->subDays($windowDays);

        $aktif = DB::table('product_insights')
            ->where('abc_class', 'A')
            ->pluck('barang_id')->toArray();

        if (empty($aktif)) return ['decline' => [], 'message' => 'Belum ada klasifikasi ABC.'];

        $cur = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('p.tanggal', '>=', $sejak)
            ->whereIn('pd.barang_id', $aktif)
            ->groupBy('pd.barang_id')
            ->select('pd.barang_id', DB::raw('SUM(pd.subtotal) as omzet'))
            ->pluck('omzet', 'pd.barang_id');

        $prev = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->whereBetween('p.tanggal', [$prevStart, $prevEnd])
            ->whereIn('pd.barang_id', $aktif)
            ->groupBy('pd.barang_id')
            ->select('pd.barang_id', DB::raw('SUM(pd.subtotal) as omzet'))
            ->pluck('omzet', 'pd.barang_id');

        $decline = [];
        foreach ($prev as $bid => $prevOmzet) {
            $curOmzet = (int) ($cur[$bid] ?? 0);
            if ($prevOmzet <= 0) continue;
            $delta = ($curOmzet - $prevOmzet) / $prevOmzet;
            if ($delta <= -0.3) {
                $b = Barang::find($bid);
                if ($b) {
                    $decline[] = [
                        'barang_id' => $bid,
                        'nama' => $b->nama,
                        'omzet_prev' => (int) $prevOmzet,
                        'omzet_now' => $curOmzet,
                        'delta_persen' => round($delta * 100, 1),
                    ];
                }
            }
        }
        return ['decline' => $decline];
    }
}
