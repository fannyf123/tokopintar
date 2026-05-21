<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\PriceHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PricingService
{
    public function priceElasticity(int $barangId): array
    {
        $changes = PriceHistory::where('barang_id', $barangId)
            ->orderBy('created_at')
            ->get();

        if ($changes->count() < 1) {
            return ['elasticity' => null, 'method' => 'no_history', 'samples' => 0];
        }

        $samples = [];
        foreach ($changes as $ch) {
            $before = Carbon::parse($ch->created_at)->subDays(14);
            $after = Carbon::parse($ch->created_at);
            $afterEnd = Carbon::parse($ch->created_at)->addDays(14);

            $qtyBefore = (float) DB::table('penjualan_details as pd')
                ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
                ->where('p.status', 'lunas')
                ->where('pd.barang_id', $barangId)
                ->whereBetween('p.tanggal', [$before, $after])
                ->sum('pd.qty');

            $qtyAfter = (float) DB::table('penjualan_details as pd')
                ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
                ->where('p.status', 'lunas')
                ->where('pd.barang_id', $barangId)
                ->whereBetween('p.tanggal', [$after, $afterEnd])
                ->sum('pd.qty');

            if ($qtyBefore <= 0 || $ch->harga_jual_lama <= 0) continue;

            $deltaQ = ($qtyAfter - $qtyBefore) / $qtyBefore;
            $deltaP = ((float) $ch->harga_jual_baru - (float) $ch->harga_jual_lama) / (float) $ch->harga_jual_lama;
            if (abs($deltaP) < 0.001) continue;

            $samples[] = $deltaQ / $deltaP;
        }

        if (empty($samples)) {
            return ['elasticity' => null, 'method' => 'insufficient_data', 'samples' => 0];
        }

        $elasticity = array_sum($samples) / count($samples);
        return [
            'elasticity' => round($elasticity, 3),
            'method' => 'midpoint_avg',
            'samples' => count($samples),
            'interpretation' => $this->interpretElasticity($elasticity),
        ];
    }

    private function interpretElasticity(float $e): string
    {
        $abs = abs($e);
        if ($abs >= 1.5) return 'Sangat sensitif harga - hindari naikkan harga, mungkin pelanggan kabur.';
        if ($abs >= 1.0) return 'Elastis - naik harga 10% bisa kurangi volume sekitar 10%.';
        if ($abs >= 0.5) return 'Cukup elastis - bisa naik harga sedikit tanpa drastis kurangi volume.';
        return 'Tidak elastis - aman naik harga, volume cuma sedikit turun.';
    }

    public function simulateMargin(int $barangId, int $hargaJualBaru): array
    {
        $b = Barang::find($barangId);
        if (! $b) return ['error' => 'Barang tidak ditemukan'];

        $hargaSekarang = (int) $b->harga_jual;
        $modal = (int) $b->harga_beli;
        $deltaPersen = $hargaSekarang > 0 ? (($hargaJualBaru - $hargaSekarang) / $hargaSekarang) : 0;

        $velocity30 = (float) DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->where('pd.barang_id', $barangId)
            ->where('p.tanggal', '>=', Carbon::today()->subDays(30))
            ->sum('pd.qty');

        $marginLama = $hargaSekarang - $modal;
        $marginBaru = $hargaJualBaru - $modal;

        $elasticityData = $this->priceElasticity($barangId);
        $elasticity = $elasticityData['elasticity'] ?? -0.5;

        $deltaQ = $elasticity * $deltaPersen;
        $velocityBaru = max(0, $velocity30 * (1 + $deltaQ));

        $profitLama = $velocity30 * $marginLama;
        $profitBaru = $velocityBaru * $marginBaru;

        return [
            'harga_lama' => $hargaSekarang,
            'harga_baru' => $hargaJualBaru,
            'modal' => $modal,
            'margin_lama' => $marginLama,
            'margin_baru' => $marginBaru,
            'volume_lama_30hr' => round($velocity30, 1),
            'volume_baru_30hr' => round($velocityBaru, 1),
            'profit_lama_30hr' => (int) round($profitLama),
            'profit_baru_30hr' => (int) round($profitBaru),
            'profit_delta' => (int) round($profitBaru - $profitLama),
            'profit_delta_persen' => $profitLama > 0 ? round((($profitBaru - $profitLama) / $profitLama) * 100, 2) : 0,
            'elasticity_used' => $elasticity,
            'elasticity_source' => $elasticityData['method'] ?? 'fallback',
        ];
    }
}
