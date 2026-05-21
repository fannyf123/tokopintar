<?php

namespace App\Services;

use App\Models\CustomerInsight;
use App\Models\Pelanggan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerInsightService
{
    public const CLV_HORIZON_MONTHS = 12;
    public const CHURN_MULTIPLIER = 2.5;

    public function recomputeAll(): int
    {
        $count = 0;
        $today = Carbon::today();

        $rows = DB::table('penjualans as p')
            ->select('p.pelanggan_id',
                DB::raw('MAX(p.tanggal) as last_tx'),
                DB::raw('COUNT(DISTINCT p.id) as freq'),
                DB::raw('SUM(p.grand_total) as monetary'),
                DB::raw('MIN(p.tanggal) as first_tx'))
            ->whereNotNull('p.pelanggan_id')
            ->where('p.status', 'lunas')
            ->groupBy('p.pelanggan_id')
            ->get();

        if ($rows->isEmpty()) return 0;

        $rByPelanggan = [];
        $fByPelanggan = [];
        $mByPelanggan = [];
        foreach ($rows as $r) {
            $rByPelanggan[$r->pelanggan_id] = $today->diffInDays(Carbon::parse($r->last_tx)->startOfDay());
            $fByPelanggan[$r->pelanggan_id] = (int) $r->freq;
            $mByPelanggan[$r->pelanggan_id] = (int) $r->monetary;
        }

        $rThresh = $this->quintiles($rByPelanggan, true);
        $fThresh = $this->quintiles($fByPelanggan, false);
        $mThresh = $this->quintiles($mByPelanggan, false);

        foreach ($rows as $r) {
            $pid = $r->pelanggan_id;
            $recency = $rByPelanggan[$pid];
            $frequency = $fByPelanggan[$pid];
            $monetary = $mByPelanggan[$pid];

            $rs = $this->scoreFromThresh($recency, $rThresh, true);
            $fs = $this->scoreFromThresh($frequency, $fThresh, false);
            $ms = $this->scoreFromThresh($monetary, $mThresh, false);

            $segment = $this->classifySegment($rs, $fs, $ms);
            $avgInterval = $this->avgInterval($r);
            $churnRisk = $this->isChurnRisk($recency, $avgInterval, $frequency);
            $clv = $this->estimateClv($monetary, $frequency, $r);
            $rekom = $this->buildRekomendasi($segment, $churnRisk, $rs, $fs, $ms, $recency);

            CustomerInsight::updateOrCreate(
                ['pelanggan_id' => $pid],
                [
                    'recency_days' => $recency,
                    'frequency' => $frequency,
                    'monetary' => $monetary,
                    'r_score' => $rs,
                    'f_score' => $fs,
                    'm_score' => $ms,
                    'segment' => $segment,
                    'avg_interval_days' => round($avgInterval, 2),
                    'churn_risk' => $churnRisk,
                    'clv_estimate' => $clv,
                    'rekomendasi_text' => $rekom,
                    'dihitung_pada' => now(),
                ]
            );
            $count++;
        }
        return $count;
    }

    private function quintiles(array $values, bool $reverse): array
    {
        $sorted = array_values($values);
        sort($sorted);
        if ($reverse) $sorted = array_reverse($sorted);
        $n = count($sorted);
        if ($n === 0) return [0, 0, 0, 0];
        return [
            $sorted[(int) floor($n * 0.2)] ?? 0,
            $sorted[(int) floor($n * 0.4)] ?? 0,
            $sorted[(int) floor($n * 0.6)] ?? 0,
            $sorted[(int) floor($n * 0.8)] ?? 0,
        ];
    }

    private function scoreFromThresh($value, array $thresh, bool $reverseRecency): int
    {
        if ($reverseRecency) {
            if ($value <= $thresh[0]) return 5;
            if ($value <= $thresh[1]) return 4;
            if ($value <= $thresh[2]) return 3;
            if ($value <= $thresh[3]) return 2;
            return 1;
        }
        if ($value >= $thresh[0]) return 5;
        if ($value >= $thresh[1]) return 4;
        if ($value >= $thresh[2]) return 3;
        if ($value >= $thresh[3]) return 2;
        return 1;
    }

    private function classifySegment(int $r, int $f, int $m): string
    {
        if ($r >= 4 && $f >= 4 && $m >= 4) return CustomerInsight::SEG_CHAMPION;
        if ($r >= 3 && $f >= 3) return CustomerInsight::SEG_LOYAL;
        if ($r >= 4 && $f <= 2) return CustomerInsight::SEG_NEW;
        if ($r >= 3 && $m >= 3) return CustomerInsight::SEG_POTENTIAL;
        if ($r <= 2 && $f >= 3) return CustomerInsight::SEG_AT_RISK;
        if ($r <= 1 && $f <= 2) return CustomerInsight::SEG_LOST;
        return CustomerInsight::SEG_POTENTIAL;
    }

    private function avgInterval($r): float
    {
        $first = Carbon::parse($r->first_tx);
        $last = Carbon::parse($r->last_tx);
        $freq = max(1, (int) $r->freq - 1);
        $totalDays = $first->diffInDays($last);
        return $freq > 0 ? $totalDays / $freq : 0;
    }

    private function isChurnRisk(int $recency, float $avgInterval, int $freq): bool
    {
        if ($freq < 2 || $avgInterval <= 0) return false;
        return $recency > ($avgInterval * self::CHURN_MULTIPLIER);
    }

    private function estimateClv(int $monetary, int $frequency, $r): int
    {
        if ($frequency <= 0) return 0;
        $first = Carbon::parse($r->first_tx);
        $last = Carbon::parse($r->last_tx);
        $monthsActive = max(1, $first->diffInMonths($last) + 1);
        $monthlyValue = $monetary / $monthsActive;
        return (int) round($monthlyValue * self::CLV_HORIZON_MONTHS);
    }

    private function buildRekomendasi(string $seg, bool $churn, int $r, int $f, int $m, int $recency): string
    {
        if ($churn) {
            return "Pelanggan tidak belanja {$recency} hari (di atas pola normal). Hubungi via WhatsApp / kirim promo personal.";
        }
        return match ($seg) {
            CustomerInsight::SEG_CHAMPION => 'Champion! Pelanggan terbaik. Pertahankan dengan loyalty reward atau early access produk baru.',
            CustomerInsight::SEG_LOYAL => 'Loyal. Tingkatkan ke Champion dengan upselling produk premium.',
            CustomerInsight::SEG_POTENTIAL => 'Potensial. Beri promo bertarget agar lebih sering belanja.',
            CustomerInsight::SEG_AT_RISK => 'Beresiko hilang. Kirim diskon khusus segera.',
            CustomerInsight::SEG_LOST => 'Sudah lama tidak balik. Coba campaign reaktivasi sekali, kalau tidak respon, hapus dari list aktif.',
            CustomerInsight::SEG_NEW => 'Pelanggan baru. Kirim welcome message + diskon kunjungan kedua.',
            default => 'Pantau berkala.',
        };
    }
}
