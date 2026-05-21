<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function laba(Carbon $start, Carbon $end, string $granularity = 'daily'): array
    {
        $bucket = $this->bucketExpression($granularity);

        $omzetRows = DB::table('penjualans')
            ->where('status', 'lunas')
            ->whereBetween('tanggal', [$start, $end])
            ->selectRaw("$bucket as bucket, SUM(grand_total) as omzet")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()->keyBy('bucket');

        $hppRows = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', 'lunas')
            ->whereBetween('p.tanggal', [$start, $end])
            ->selectRaw("$bucket as bucket, SUM(pd.qty * pd.hpp_saat_itu) as hpp")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()->keyBy('bucket');

        $bucketBiaya = $this->bucketExpression($granularity, 'tanggal');
        $biayaRows = DB::table('pengeluarans')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw("$bucketBiaya as bucket, SUM(jumlah) as biaya")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()->keyBy('bucket');

        $buckets = collect($omzetRows->keys())
            ->merge($hppRows->keys())
            ->merge($biayaRows->keys())
            ->unique()
            ->sort()
            ->values();

        $rows = [];
        $totOmzet = 0; $totHpp = 0; $totBiaya = 0;
        foreach ($buckets as $b) {
            $om = (int) ($omzetRows[$b]->omzet ?? 0);
            $hp = (int) ($hppRows[$b]->hpp ?? 0);
            $bi = (int) ($biayaRows[$b]->biaya ?? 0);
            $rows[] = [
                'bucket' => $b,
                'omzet' => $om,
                'hpp' => $hp,
                'laba_kotor' => $om - $hp,
                'biaya' => $bi,
                'laba_bersih' => $om - $hp - $bi,
            ];
            $totOmzet += $om; $totHpp += $hp; $totBiaya += $bi;
        }

        return [
            'rows' => $rows,
            'totals' => [
                'omzet' => $totOmzet,
                'hpp' => $totHpp,
                'laba_kotor' => $totOmzet - $totHpp,
                'biaya' => $totBiaya,
                'laba_bersih' => $totOmzet - $totHpp - $totBiaya,
            ],
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
            'granularity' => $granularity,
        ];
    }

    private function bucketExpression(string $granularity, string $col = 'tanggal'): string
    {
        $driver = config('database.default');
        return match ($driver) {
            'sqlite' => match ($granularity) {
                'weekly' => "strftime('%Y-W%W', $col)",
                'monthly' => "strftime('%Y-%m', $col)",
                'yearly' => "strftime('%Y', $col)",
                default => "strftime('%Y-%m-%d', $col)",
            },
            'pgsql' => match ($granularity) {
                'weekly' => "to_char($col, 'IYYY-\"W\"IW')",
                'monthly' => "to_char($col, 'YYYY-MM')",
                'yearly' => "to_char($col, 'YYYY')",
                default => "to_char($col, 'YYYY-MM-DD')",
            },
            default => match ($granularity) {
                'weekly' => "DATE_FORMAT($col, '%x-W%v')",
                'monthly' => "DATE_FORMAT($col, '%Y-%m')",
                'yearly' => "DATE_FORMAT($col, '%Y')",
                default => "DATE_FORMAT($col, '%Y-%m-%d')",
            },
        };
    }
}
