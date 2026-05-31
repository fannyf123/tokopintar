<?php

namespace App\Services;

use App\Models\Penjualan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AsistenService
{
    /**
     * Ringkasan harian dalam bahasa manusia.
     */
    public function ringkasanHarian(?Carbon $tanggal = null): array
    {
        $tanggal = $tanggal ?? Carbon::today();
        $start = $tanggal->copy()->startOfDay();
        $end = $tanggal->copy()->endOfDay();

        // Penjualan lunas hari ini
        $jual = DB::table('penjualans')
            ->whereBetween('tanggal', [$start, $end])
            ->where('status', Penjualan::STATUS_LUNAS)
            ->selectRaw('COUNT(*) as trx, COALESCE(SUM(grand_total),0) as omzet')
            ->first();

        // HPP hari ini (untuk untung)
        $hpp = (int) DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->whereBetween('p.tanggal', [$start, $end])
            ->where('p.status', Penjualan::STATUS_LUNAS)
            ->selectRaw('COALESCE(SUM(pd.qty * pd.hpp_saat_itu),0) as hpp')
            ->value('hpp');

        $omzet = (int) ($jual->omzet ?? 0);
        $trx = (int) ($jual->trx ?? 0);
        $untung = $omzet - $hpp;

        // Barang terlaris hari ini
        $terlaris = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->join('barangs as b', 'b.id', '=', 'pd.barang_id')
            ->whereBetween('p.tanggal', [$start, $end])
            ->where('p.status', Penjualan::STATUS_LUNAS)
            ->groupBy('b.id', 'b.nama', 'b.satuan')
            ->select('b.nama', 'b.satuan', DB::raw('SUM(pd.qty) as total_qty'))
            ->orderByDesc('total_qty')
            ->limit(3)
            ->get();

        // Pengeluaran hari ini
        $pengeluaran = (int) DB::table('pengeluarans')
            ->whereDate('tanggal', $tanggal->toDateString())
            ->sum('jumlah');

        // Stok menipis
        $stokKritis = DB::table('barangs')
            ->where('aktif', true)
            ->whereColumn('stok_current', '<=', 'stok_min')
            ->orderBy('stok_current')
            ->select('nama', 'stok_current', 'satuan')
            ->limit(5)
            ->get();

        // Bandingkan dengan kemarin
        $kemarinOmzet = (int) DB::table('penjualans')
            ->whereBetween('tanggal', [
                $tanggal->copy()->subDay()->startOfDay(),
                $tanggal->copy()->subDay()->endOfDay(),
            ])
            ->where('status', Penjualan::STATUS_LUNAS)
            ->sum('grand_total');

        // Hutang baru hari ini
        $hutang = DB::table('penjualans')
            ->whereBetween('tanggal', [$start, $end])
            ->where('status', Penjualan::STATUS_HUTANG)
            ->selectRaw('COUNT(*) as jml, COALESCE(SUM(grand_total - dibayar),0) as sisa')
            ->first();

        return [
            'tanggal' => $tanggal,
            'omzet' => $omzet,
            'trx' => $trx,
            'untung' => $untung,
            'pengeluaran' => $pengeluaran,
            'terlaris' => $terlaris,
            'stok_kritis' => $stokKritis,
            'kemarin_omzet' => $kemarinOmzet,
            'hutang_jml' => (int) ($hutang->jml ?? 0),
            'hutang_sisa' => (int) ($hutang->sisa ?? 0),
            'kalimat' => $this->susunKalimat($omzet, $untung, $trx, $terlaris, $stokKritis, $kemarinOmzet, $pengeluaran),
        ];
    }

    private function susunKalimat($omzet, $untung, $trx, $terlaris, $stokKritis, $kemarinOmzet, $pengeluaran): array
    {
        $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
        $out = [];

        if ($trx === 0) {
            $out[] = ['icon' => 'fa-circle-info', 'warna' => 'secondary', 'teks' => 'Belum ada penjualan tercatat hari ini.'];
            return $out;
        }

        $out[] = [
            'icon' => 'fa-cash-register', 'warna' => 'primary',
            'teks' => "Hari ini jual {$rp($omzet)} dari {$trx} transaksi, untung sekitar {$rp($untung)}.",
        ];

        // Perbandingan kemarin
        if ($kemarinOmzet > 0) {
            $selisih = $omzet - $kemarinOmzet;
            $pct = round(abs($selisih) / $kemarinOmzet * 100);
            if ($selisih > 0) {
                $out[] = ['icon' => 'fa-arrow-trend-up', 'warna' => 'success', 'teks' => "Lebih ramai {$pct}% dibanding kemarin. Bagus!"];
            } elseif ($selisih < 0) {
                $out[] = ['icon' => 'fa-arrow-trend-down', 'warna' => 'warning', 'teks' => "Turun {$pct}% dibanding kemarin ({$rp($kemarinOmzet)})."];
            } else {
                $out[] = ['icon' => 'fa-equals', 'warna' => 'info', 'teks' => 'Penjualan sama persis dengan kemarin.'];
            }
        }

        // Terlaris
        if ($terlaris->count() > 0) {
            $t = $terlaris->first();
            $out[] = ['icon' => 'fa-fire', 'warna' => 'danger', 'teks' => "Paling laku: {$t->nama} ({$t->total_qty} {$t->satuan})."];
        }

        // Pengeluaran
        if ($pengeluaran > 0) {
            $out[] = ['icon' => 'fa-money-bill-wave', 'warna' => 'secondary', 'teks' => "Ada pengeluaran {$rp($pengeluaran)} hari ini, sudah dipotong dari untung bersih."];
        }

        // Stok kritis
        if ($stokKritis->count() > 0) {
            $nama = $stokKritis->take(3)->pluck('nama')->implode(', ');
            $out[] = ['icon' => 'fa-triangle-exclamation', 'warna' => 'danger', 'teks' => "Segera restock: {$nama}" . ($stokKritis->count() > 3 ? ' dan lainnya.' : '.')];
        }

        return $out;
    }
}
