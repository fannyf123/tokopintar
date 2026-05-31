<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TutupKasirController extends Controller
{
    public function index(Request $request): View
    {
        $tanggal = $request->date('tanggal')
            ? Carbon::parse($request->date('tanggal'))
            : Carbon::today();

        $start = $tanggal->copy()->startOfDay();
        $end = $tanggal->copy()->endOfDay();

        // Penjualan lunas hari ini, dikelompokkan per metode bayar
        $perMetode = Penjualan::whereBetween('tanggal', [$start, $end])
            ->where('status', Penjualan::STATUS_LUNAS)
            ->select('metode_bayar', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('metode_bayar')
            ->get()
            ->keyBy('metode_bayar');

        $omzetLunas = (int) $perMetode->sum('total');
        $trxLunas = (int) $perMetode->sum('jumlah');

        // Uang tunai masuk (hanya metode cash)
        $tunaiMasuk = (int) ($perMetode['cash']->total ?? 0);

        // Hutang baru hari ini (yang belum lunas)
        $hutang = Penjualan::whereBetween('tanggal', [$start, $end])
            ->where('status', Penjualan::STATUS_HUTANG)
            ->select(DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(grand_total - dibayar) as sisa'), DB::raw('SUM(dibayar) as dp'))
            ->first();

        // Pengeluaran hari ini
        $pengeluaran = (int) Pengeluaran::whereDate('tanggal', $tanggal->toDateString())->sum('jumlah');

        // Uang tunai yang seharusnya ada di laci
        $kasLaci = $tunaiMasuk + (int) ($hutang->dp ?? 0) - $pengeluaran;

        return view('tutup-kasir.index', [
            'tanggal' => $tanggal,
            'perMetode' => $perMetode,
            'omzetLunas' => $omzetLunas,
            'trxLunas' => $trxLunas,
            'tunaiMasuk' => $tunaiMasuk,
            'hutangJumlah' => (int) ($hutang->jumlah ?? 0),
            'hutangSisa' => (int) ($hutang->sisa ?? 0),
            'hutangDp' => (int) ($hutang->dp ?? 0),
            'pengeluaran' => $pengeluaran,
            'kasLaci' => $kasLaci,
        ]);
    }
}
