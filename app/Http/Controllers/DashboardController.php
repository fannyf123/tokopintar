<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\ProductBatch;
use App\Models\ProductInsight;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();

        $omzetToday = (int) Penjualan::where('status', 'lunas')
            ->whereBetween('tanggal', [$today, $todayEnd])
            ->sum('grand_total');

        $hppToday = (int) PenjualanDetail::join('penjualans', 'penjualans.id', '=', 'penjualan_details.penjualan_id')
            ->where('penjualans.status', 'lunas')
            ->whereBetween('penjualans.tanggal', [$today, $todayEnd])
            ->sum(DB::raw('penjualan_details.qty * penjualan_details.hpp_saat_itu'));

        $trxToday = Penjualan::where('status', 'lunas')
            ->whereBetween('tanggal', [$today, $todayEnd])
            ->count();

        $stokRendah = Barang::where('aktif', true)
            ->whereColumn('stok_current', '<=', 'stok_min')
            ->count();

        $nearExpiry = ProductBatch::where('qty_sisa', '>', 0)
            ->whereNotNull('tanggal_kadaluarsa')
            ->whereDate('tanggal_kadaluarsa', '<=', now()->addDays(30))
            ->count();

        $omzetSeries = $this->buildOmzet30Series();

        $topBarang = PenjualanDetail::join('penjualans', 'penjualans.id', '=', 'penjualan_details.penjualan_id')
            ->where('penjualans.status', 'lunas')
            ->whereBetween('penjualans.tanggal', [Carbon::today()->subDays(29), $todayEnd])
            ->groupBy('penjualan_details.barang_id')
            ->selectRaw('penjualan_details.barang_id, SUM(penjualan_details.qty) as total_qty')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('barang')
            ->get();

        $lastTrx = Penjualan::with('kasir', 'pelanggan')
            ->orderByDesc('id')->limit(5)->get();

        $fastMovers = ProductInsight::with('barang')
            ->where('kelas', ProductInsight::KELAS_FAST)
            ->orderByDesc('velocity_30')->limit(5)->get();

        $deadStocks = ProductInsight::with('barang')
            ->where('kelas', ProductInsight::KELAS_DEAD)
            ->orderByDesc('days_of_supply')->limit(5)->get();

        return view('dashboard', compact(
            'omzetToday', 'hppToday', 'trxToday', 'stokRendah', 'nearExpiry',
            'omzetSeries', 'topBarang', 'lastTrx', 'fastMovers', 'deadStocks'
        ));
    }

    private function buildOmzet30Series(): array
    {
        $start = Carbon::today()->subDays(29);
        $rows = Penjualan::where('status', 'lunas')
            ->where('tanggal', '>=', $start)
            ->selectRaw('DATE(tanggal) as d, SUM(grand_total) as omzet')
            ->groupBy('d')
            ->pluck('omzet', 'd')->toArray();

        $labels = []; $data = [];
        for ($i = 0; $i < 30; $i++) {
            $d = $start->copy()->addDays($i)->toDateString();
            $labels[] = $d;
            $data[] = (int) ($rows[$d] ?? 0);
        }
        return ['labels' => $labels, 'data' => $data];
    }
}
