<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\PenjualanDetail;
use App\Models\ProductBatch;
use App\Models\ProductInsight;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InsightService
{
    public const NEAR_EXPIRY_DAYS = 30;
    public const ABC_A_LIMIT = 0.80;
    public const ABC_B_LIMIT = 0.95;
    public const SES_ALPHA = 0.30;

    public function recomputeAll(int $windowHari = 30, int $windowAbc = 90): int
    {
        $count = 0;
        $tanggalAwal = Carbon::today()->subDays($windowHari);
        $tanggalAbcAwal = Carbon::today()->subDays($windowAbc);

        $omzetTotal = (int) DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('p.tanggal', '>=', $tanggalAbcAwal)
            ->sum('pd.subtotal');

        $omzetMap = $omzetTotal > 0
            ? DB::table('penjualan_details as pd')
                ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
                ->where('p.status', 'lunas')
                ->where('p.tanggal', '>=', $tanggalAbcAwal)
                ->groupBy('pd.barang_id')
                ->orderByDesc(DB::raw('SUM(pd.subtotal)'))
                ->pluck(DB::raw('SUM(pd.subtotal) as oms'), 'pd.barang_id')
                ->toArray()
            : [];

        $abcMap = $this->buildAbcMap($omzetMap, $omzetTotal);
        $basketMap = $this->buildBasketMap($tanggalAbcAwal);

        Barang::chunk(200, function ($barangs) use ($tanggalAwal, $windowHari, $abcMap, $basketMap, &$count) {
            foreach ($barangs as $barang) {
                $insight = $this->computeForBarang($barang, $tanggalAwal, $windowHari, $abcMap, $basketMap);
                ProductInsight::updateOrCreate(
                    ['barang_id' => $barang->id],
                    $insight,
                );
                $count++;
            }
        });

        return $count;
    }

    private function computeForBarang(Barang $barang, Carbon $tanggalAwal, int $windowHari, array $abcMap, array $basketMap): array
    {
        $totalQty = (int) DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('p.tanggal', '>=', $tanggalAwal)
            ->where('pd.barang_id', $barang->id)
            ->sum('pd.qty');

        $velocity = $totalQty / max($windowHari, 1);
        $dos = $velocity > 0
            ? ((int) $barang->stok_current) / $velocity
            : ((int) $barang->stok_current > 0 ? 9999 : 0);

        $umurHari = $barang->created_at ? $barang->created_at->diffInDays(now()) : 999;

        $kelas = $this->classify($barang, $velocity, $dos, $umurHari);

        $forecast7 = $this->sesForecast7($barang, $tanggalAwal, $windowHari);

        $marginPct = $this->marginPct($barang);

        [$strategy, $partners, $strategyText] = $this->classifyStrategy($barang, $marginPct, $velocity, $kelas, $basketMap);

        $rekom = $this->buildRekomendasi($barang, $kelas, $velocity, $forecast7);

        return [
            'velocity_30' => round($velocity, 4),
            'days_of_supply' => round(min($dos, 9999.99), 2),
            'kelas' => $kelas,
            'abc_class' => $abcMap[$barang->id] ?? null,
            'forecast_7' => round($forecast7, 4),
            'rekomendasi_text' => $rekom,
            'margin_pct' => round($marginPct, 2),
            'strategy' => $strategy,
            'strategy_partner_ids' => $partners ? implode(',', $partners) : null,
            'strategy_text' => $strategyText,
            'dihitung_pada' => now(),
        ];
    }

    private function marginPct(Barang $barang): float
    {
        $hb = (int) $barang->harga_beli;
        $hj = (int) $barang->harga_jual;
        if ($hj <= 0) return 0.0;
        return (($hj - $hb) / $hj) * 100.0;
    }

    /**
     * Classify pricing strategy:
     * - LOSS_LEADER: margin <= 5% — barang pemikat (dijual mendekati modal untuk narik traffic)
     * - PROFIT_DRIVER: margin >= 25% — penyumbang profit utama
     * - BALANCED: di antaranya
     *
     * Untuk LOSS_LEADER, cari "partner" PROFIT_DRIVER yang sering dibeli bareng (basket co-occurrence).
     * Bisnis: subsidi silang — kerugian/profit tipis di leader ditutup oleh driver yang ikut terjual.
     */
    private function classifyStrategy(Barang $barang, float $marginPct, float $velocity, string $kelas, array $basketMap): array
    {
        if ($marginPct <= 5.0) {
            $strategy = ProductInsight::STRATEGY_LOSS_LEADER;
        } elseif ($marginPct >= 25.0) {
            $strategy = ProductInsight::STRATEGY_PROFIT_DRIVER;
        } else {
            $strategy = ProductInsight::STRATEGY_BALANCED;
        }

        $partners = [];
        $text = '';

        if ($strategy === ProductInsight::STRATEGY_LOSS_LEADER) {
            $partners = $basketMap[$barang->id] ?? [];
            if ($partners) {
                $names = Barang::whereIn('id', $partners)->pluck('nama')->take(3)->implode(', ');
                $text = "Loss leader (margin " . round($marginPct, 1) . "%). Sering dibeli bareng: {$names}. Tampilkan dekat barang ini & buat bundling.";
            } else {
                $text = "Loss leader (margin " . round($marginPct, 1) . "%). Naikkan harga jual, atau pasangkan dengan profit-driver agar profit total naik.";
            }
        } elseif ($strategy === ProductInsight::STRATEGY_PROFIT_DRIVER) {
            $text = "Profit driver (margin " . round($marginPct, 1) . "%). Aman untuk dipasangkan dengan loss-leader sebagai bundling untuk dorong perputaran.";
        } else {
            $text = "Margin sehat (" . round($marginPct, 1) . "%). Pertahankan harga, fokus rotasi.";
        }

        if ($kelas === ProductInsight::KELAS_SLOW || $kelas === ProductInsight::KELAS_DEAD) {
            $text .= ' Slow/Dead — pertimbangkan diskon flash sebagai loss-leader sementara untuk kosongkan stok.';
        }

        return [$strategy, $partners, $text];
    }

    /**
     * Build basket co-occurrence map: untuk tiap loss-leader candidate (margin tipis),
     * temukan barang lain yang sering muncul di transaksi yang sama (lift > threshold).
     * Return: [barang_id => [partner_id_1, partner_id_2, ...]] (top 5 partners by co-count).
     */
    private function buildBasketMap(Carbon $sejak): array
    {
        $rows = DB::table('penjualan_details as a')
            ->join('penjualan_details as b', function ($join) {
                $join->on('a.penjualan_id', '=', 'b.penjualan_id')
                     ->whereColumn('a.barang_id', '<>', 'b.barang_id');
            })
            ->join('penjualans as p', 'p.id', '=', 'a.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('p.tanggal', '>=', $sejak)
            ->selectRaw('a.barang_id as src, b.barang_id as dst, COUNT(*) as co')
            ->groupBy('a.barang_id', 'b.barang_id')
            ->havingRaw('COUNT(*) >= 2')
            ->orderByRaw('COUNT(*) DESC')
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $map[$r->src] ??= [];
            if (count($map[$r->src]) < 5) {
                $map[$r->src][] = (int) $r->dst;
            }
        }
        return $map;
    }

    private function classify(Barang $barang, float $velocity, float $dos, int $umurHari): string
    {
        if ($umurHari < 14 && $velocity == 0.0) {
            return ProductInsight::KELAS_NEW;
        }

        $lastOut = $barang->last_out_at;
        $lastOutOld = $lastOut === null || $lastOut->lt(now()->subDays(60));

        if ($dos > 90 || ($lastOutOld && (int) $barang->stok_current > 0)) {
            return ProductInsight::KELAS_DEAD;
        }
        if ($velocity >= 1 && $dos <= 7) {
            return ProductInsight::KELAS_FAST;
        }
        if ($dos > 30 && $dos <= 90) {
            return ProductInsight::KELAS_SLOW;
        }
        return ProductInsight::KELAS_NORMAL;
    }

    private function buildAbcMap(array $omzetMap, int $omzetTotal): array
    {
        if (empty($omzetMap) || $omzetTotal <= 0) return [];
        $cum = 0;
        $out = [];
        foreach ($omzetMap as $bid => $oms) {
            $cum += (int) $oms;
            $share = $cum / $omzetTotal;
            $out[$bid] = $share <= self::ABC_A_LIMIT ? 'A' : ($share <= self::ABC_B_LIMIT ? 'B' : 'C');
        }
        return $out;
    }

    private function sesForecast7(Barang $barang, Carbon $tanggalAwal, int $windowHari): float
    {
        $rows = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('p.tanggal', '>=', $tanggalAwal)
            ->where('pd.barang_id', $barang->id)
            ->selectRaw('DATE(p.tanggal) as d, SUM(pd.qty) as q')
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('q', 'd');

        if ($rows->isEmpty()) return 0.0;

        $level = (float) $rows->first();
        foreach ($rows as $val) {
            $level = self::SES_ALPHA * (float) $val + (1 - self::SES_ALPHA) * $level;
        }
        return $level * 7.0;
    }

    private function buildRekomendasi(Barang $barang, string $kelas, float $velocity, float $forecast7): string
    {
        $stok = (int) $barang->stok_current;
        $min = (int) $barang->stok_min;

        $nearExpiry = ProductBatch::where('barang_id', $barang->id)
            ->where('qty_sisa', '>', 0)
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<=', now()->addDays(self::NEAR_EXPIRY_DAYS))
            ->exists();

        $expired = ProductBatch::where('barang_id', $barang->id)
            ->where('qty_sisa', '>', 0)
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<', now())
            ->exists();

        if ($expired) {
            return 'EXPIRED — pisahkan & buang via mutasi expired_dibuang.';
        }
        if ($nearExpiry) {
            return 'Akan kadaluarsa <= 30 hari — diskon agresif atau bundling.';
        }
        if ($kelas === ProductInsight::KELAS_FAST && $stok <= $min + (int) ceil($forecast7)) {
            $saran = (int) max(1, ceil($forecast7) + $min - $stok);
            return "Reorder segera. Saran qty: {$saran}.";
        }
        if ($kelas === ProductInsight::KELAS_SLOW) {
            return 'Slow mover — pertimbangkan diskon atau bundling.';
        }
        if ($kelas === ProductInsight::KELAS_DEAD) {
            return 'Dead stock — cuci gudang, retur supplier, atau hapus listing.';
        }
        if ($kelas === ProductInsight::KELAS_NEW) {
            return 'Barang baru — kumpulkan data >= 14 hari sebelum dianalisis.';
        }
        return 'Normal — pantau berkala.';
    }
}
