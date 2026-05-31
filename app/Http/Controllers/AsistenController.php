<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Services\AsistenService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AsistenController extends Controller
{
    public function __construct(private AsistenService $svc) {}

    public function ringkasan(): View
    {
        return view('asisten.ringkasan', $this->svc->ringkasanHarian());
    }

    public function restock(): View
    {
        // Barang aktif yang stok <= stok_min, plus estimasi kebutuhan dari velocity 30 hari
        $start = Carbon::today()->subDays(30)->startOfDay();

        $terjual = DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'p.id', '=', 'pd.penjualan_id')
            ->where('p.status', Penjualan::STATUS_LUNAS)
            ->where('p.tanggal', '>=', $start)
            ->groupBy('pd.barang_id')
            ->select('pd.barang_id', DB::raw('SUM(pd.qty) as total30'))
            ->pluck('total30', 'pd.barang_id');

        $barangs = DB::table('barangs')
            ->where('aktif', true)
            ->whereColumn('stok_current', '<=', 'stok_min')
            ->orderByRaw('stok_current - stok_min')
            ->get(['id', 'nama', 'satuan', 'stok_current', 'stok_min', 'stok_max']);

        $list = $barangs->map(function ($b) use ($terjual) {
            $per30 = (int) ($terjual[$b->id] ?? 0);
            $perHari = $per30 / 30;
            // Saran beli: isi sampai stok_max, minimal cukup 14 hari
            $targetStok = max((int) $b->stok_max, (int) ceil($perHari * 14));
            $saranBeli = max(0, $targetStok - (int) $b->stok_current);
            $sisaHari = $perHari > 0 ? round($b->stok_current / $perHari, 1) : null;

            // Urgensi
            if ($b->stok_current <= 0) { $urg = 'HABIS'; $warna = 'dark'; }
            elseif ($sisaHari !== null && $sisaHari <= 3) { $urg = 'MENDESAK'; $warna = 'danger'; }
            elseif ($sisaHari !== null && $sisaHari <= 7) { $urg = 'SEGERA'; $warna = 'warning'; }
            else { $urg = 'PERLU'; $warna = 'info'; }

            return [
                'nama' => $b->nama,
                'satuan' => $b->satuan,
                'stok_current' => (int) $b->stok_current,
                'stok_min' => (int) $b->stok_min,
                'per_hari' => round($perHari, 1),
                'sisa_hari' => $sisaHari,
                'saran_beli' => $saranBeli,
                'urgensi' => $urg,
                'warna' => $warna,
            ];
        });

        return view('asisten.restock', ['list' => $list]);
    }

    public function traffic(): View
    {
        $start = Carbon::today()->subDays(60)->startOfDay();

        $rows = DB::table('penjualans')
            ->where('status', Penjualan::STATUS_LUNAS)
            ->where('tanggal', '>=', $start)
            ->get(['tanggal', 'grand_total']);

        // Inisialisasi ember jam (0-23) & hari (0=Minggu..6=Sabtu)
        $perJam = array_fill(0, 24, ['trx' => 0, 'omzet' => 0]);
        $namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $perHari = [];
        foreach ($namaHari as $i => $n) {
            $perHari[$i] = ['nama' => $n, 'trx' => 0, 'omzet' => 0];
        }

        foreach ($rows as $r) {
            $c = Carbon::parse($r->tanggal);
            $jam = (int) $c->format('G');
            $dow = (int) $c->format('w');
            $perJam[$jam]['trx']++;
            $perJam[$jam]['omzet'] += (int) $r->grand_total;
            $perHari[$dow]['trx']++;
            $perHari[$dow]['omzet'] += (int) $r->grand_total;
        }

        // Cari jam & hari teramai
        $jamTeramai = collect($perJam)->sortByDesc('omzet')->keys()->first();
        $hariTeramai = collect($perHari)->sortByDesc('omzet')->first();

        return view('asisten.traffic', [
            'perJam' => $perJam,
            'perHari' => array_values($perHari),
            'jamTeramai' => $jamTeramai,
            'hariTeramai' => $hariTeramai,
            'adaData' => $rows->count() > 0,
        ]);
    }
}
