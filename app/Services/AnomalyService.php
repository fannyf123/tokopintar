<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\StockAnomaly;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnomalyService
{
    public const Z_THRESHOLD = 2.5;
    public const VOID_THRESHOLD = 3;
    public const OFFHOURS_START = 22;
    public const OFFHOURS_END = 5;
    public const DISKON_PERSEN_SPIKE = 30;

    public function detectAll(): int
    {
        $count = 0;
        $count += $this->detectSalesAnomaly();
        $count += $this->detectStockLeak();
        $count += $this->detectFraudPattern();
        $count += $this->detectOffHours();
        $count += $this->detectDiskonSpike();
        return $count;
    }

    private function detectSalesAnomaly(): int
    {
        $count = 0;
        $end = Carbon::today();
        $start = Carbon::today()->subDays(30);

        $rows = DB::table('penjualans')
            ->where('status', 'lunas')
            ->whereBetween('tanggal', [$start, $end])
            ->selectRaw('DATE(tanggal) as d, SUM(grand_total) as omzet')
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        if ($rows->count() < 7) return 0;

        $values = $rows->pluck('omzet')->map(fn ($v) => (float) $v)->toArray();
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(fn ($v) => ($v - $mean) ** 2, $values)) / count($values);
        $std = sqrt($variance);
        if ($std <= 0) return 0;

        $today = $rows->last();
        $z = ((float) $today->omzet - $mean) / $std;

        if (abs($z) >= self::Z_THRESHOLD) {
            $jenis = $z > 0 ? StockAnomaly::JENIS_SALES_SPIKE : StockAnomaly::JENIS_SALES_DROP;
            $sev = abs($z) >= 3 ? StockAnomaly::SEV_CRITICAL : StockAnomaly::SEV_WARNING;
            $arah = $z > 0 ? 'naik drastis' : 'turun drastis';

            StockAnomaly::create([
                'jenis' => $jenis,
                'severity' => $sev,
                'judul' => "Penjualan {$today->d} {$arah}",
                'detail' => sprintf(
                    'Omzet hari ini Rp %s. Rata-rata 30 hari Rp %s (z-score %.2f). %s',
                    number_format($today->omzet, 0, ',', '.'),
                    number_format($mean, 0, ',', '.'),
                    $z,
                    $z > 0 ? 'Periksa apakah ada event/promo, atau false positive.' : 'Periksa apakah ada masalah toko: tutup mendadak? Stok kosong banyak?'
                ),
                'score' => abs($z),
            ]);
            $count++;
        }
        return $count;
    }

    private function detectStockLeak(): int
    {
        $count = 0;
        $sejak = Carbon::today()->subDays(30);

        Barang::chunk(100, function ($barangs) use (&$count, $sejak) {
            foreach ($barangs as $b) {
                $sold = (int) DB::table('penjualan_details as pd')
                    ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
                    ->where('p.status', 'lunas')
                    ->where('p.tanggal', '>=', $sejak)
                    ->where('pd.barang_id', $b->id)
                    ->sum('pd.qty');

                $movement = (int) DB::table('stock_movements')
                    ->where('barang_id', $b->id)
                    ->where('jenis', 'stock_out')
                    ->where('created_at', '>=', $sejak)
                    ->sum(DB::raw('ABS(qty_signed)'));

                $diff = $movement - $sold;
                if ($diff > 0 && $sold > 0 && ($diff / max($sold, 1)) > 0.05) {
                    StockAnomaly::create([
                        'jenis' => StockAnomaly::JENIS_STOCK_LEAK,
                        'barang_id' => $b->id,
                        'severity' => $diff > 10 ? StockAnomaly::SEV_CRITICAL : StockAnomaly::SEV_WARNING,
                        'judul' => "Stok {$b->nama} bocor",
                        'detail' => "Movement out: {$movement}, terjual: {$sold}. Selisih {$diff} unit tidak terjelaskan. Cek mutasi & opname fisik.",
                        'score' => $diff,
                    ]);
                    $count++;
                }
            }
        });
        return $count;
    }

    private function detectFraudPattern(): int
    {
        $count = 0;
        $sejak = Carbon::today()->subDays(7);

        $voidPatterns = DB::table('stock_movements')
            ->where('jenis', 'retur_jual')
            ->where('created_at', '>=', $sejak)
            ->groupBy('dibuat_oleh')
            ->select('dibuat_oleh', DB::raw('COUNT(*) as cnt'))
            ->having('cnt', '>=', self::VOID_THRESHOLD)
            ->get();

        foreach ($voidPatterns as $vp) {
            if (! $vp->dibuat_oleh) continue;
            StockAnomaly::create([
                'jenis' => StockAnomaly::JENIS_VOID_PATTERN,
                'user_id' => $vp->dibuat_oleh,
                'severity' => $vp->cnt >= 10 ? StockAnomaly::SEV_CRITICAL : StockAnomaly::SEV_WARNING,
                'judul' => 'Pola void/retur mencurigakan',
                'detail' => "User ID {$vp->dibuat_oleh} melakukan {$vp->cnt} retur dalam 7 hari. Periksa apakah pola wajar.",
                'score' => $vp->cnt,
            ]);
            $count++;
        }
        return $count;
    }

    private function detectOffHours(): int
    {
        $count = 0;
        $sejak = Carbon::today()->subDays(7);

        $driver = config('database.default');
        $hourExpr = $driver === 'pgsql' ? 'EXTRACT(HOUR FROM tanggal)' : "strftime('%H', tanggal)";

        $rows = DB::table('penjualans')
            ->where('status', 'lunas')
            ->where('tanggal', '>=', $sejak)
            ->select('id', 'kasir_id', 'tanggal', DB::raw("$hourExpr as jam"))
            ->get();

        foreach ($rows as $r) {
            $jam = (int) $r->jam;
            if ($jam >= self::OFFHOURS_START || $jam <= self::OFFHOURS_END) {
                StockAnomaly::create([
                    'jenis' => StockAnomaly::JENIS_OFFHOURS_TRX,
                    'user_id' => $r->kasir_id,
                    'severity' => StockAnomaly::SEV_INFO,
                    'judul' => 'Transaksi di luar jam toko',
                    'detail' => "Trx ID {$r->id} jam {$jam}:00. Konfirmasi apakah toko buka 24h atau ini transaksi mencurigakan.",
                    'score' => 1,
                ]);
                $count++;
            }
        }
        return $count;
    }

    private function detectDiskonSpike(): int
    {
        $count = 0;
        $sejak = Carbon::today()->subDays(7);

        $rows = DB::table('penjualans')
            ->where('status', 'lunas')
            ->where('tanggal', '>=', $sejak)
            ->where('total', '>', 0)
            ->select('id', 'kasir_id', 'tanggal', 'total', 'diskon')
            ->whereRaw('(diskon * 100.0 / total) >= ?', [self::DISKON_PERSEN_SPIKE])
            ->get();

        foreach ($rows as $r) {
            $persen = round(((float) $r->diskon * 100.0) / max(1, (float) $r->total), 2);
            StockAnomaly::create([
                'jenis' => StockAnomaly::JENIS_DISKON_SPIKE,
                'user_id' => $r->kasir_id,
                'severity' => $persen >= 50 ? StockAnomaly::SEV_CRITICAL : StockAnomaly::SEV_WARNING,
                'judul' => 'Diskon besar di transaksi',
                'detail' => "Trx ID {$r->id} diskon {$persen}% (Rp " . number_format($r->diskon, 0, ',', '.') . " dari Rp " . number_format($r->total, 0, ',', '.') . "). Konfirmasi otorisasi diskon.",
                'score' => $persen,
            ]);
            $count++;
        }
        return $count;
    }
}
