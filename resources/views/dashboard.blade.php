@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')
@section('content')
<h1 class="text-2xl font-bold mb-4">Dashboard</h1>

<div class="grid md:grid-cols-5 gap-3 mb-4">
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs uppercase text-gray-500">Omzet Hari Ini</div>
        <div class="text-xl font-bold text-blue-600">{{ format_rupiah($omzetToday) }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs uppercase text-gray-500">Laba Kotor Hari Ini</div>
        <div class="text-xl font-bold text-green-600">{{ format_rupiah($omzetToday - $hppToday) }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs uppercase text-gray-500">Transaksi</div>
        <div class="text-xl font-bold">{{ $trxToday }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs uppercase text-gray-500">Stok Rendah</div>
        <div class="text-xl font-bold text-red-600">{{ $stokRendah }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs uppercase text-gray-500">Akan Kadaluarsa</div>
        <div class="text-xl font-bold text-yellow-600">{{ $nearExpiry }}</div>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-4 mb-4">
    <div class="md:col-span-2 bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Omzet 30 Hari</h2>
        <canvas id="chartOmzet" height="80"></canvas>
    </div>
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Top 5 Produk Terlaris</h2>
        <canvas id="chartTop" height="160"></canvas>
    </div>
</div>

<div class="grid md:grid-cols-3 gap-4">
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Transaksi Terakhir</h2>
        <table class="w-full text-sm">
            @forelse ($lastTrx as $t)
                <tr class="border-t"><td class="py-1 font-mono text-xs">{{ $t->nomor }}</td><td class="text-right">{{ format_rupiah($t->grand_total) }}</td></tr>
            @empty <tr><td class="py-2 text-gray-400">Belum ada.</td></tr> @endforelse
        </table>
    </div>
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Fast Mover</h2>
        <table class="w-full text-sm">
            @forelse ($fastMovers as $i)
                <tr class="border-t"><td class="py-1">{{ $i->barang?->nama }}</td><td class="text-right text-xs text-gray-500">{{ number_format($i->velocity_30, 2) }}/hari</td></tr>
            @empty <tr><td class="py-2 text-gray-400">Belum ada insight.</td></tr> @endforelse
        </table>
    </div>
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">Dead Stock</h2>
        <table class="w-full text-sm">
            @forelse ($deadStocks as $i)
                <tr class="border-t"><td class="py-1">{{ $i->barang?->nama }}</td><td class="text-right text-xs text-gray-500">dos {{ number_format($i->days_of_supply, 0) }}</td></tr>
            @empty <tr><td class="py-2 text-gray-400">Belum ada.</td></tr> @endforelse
        </table>
    </div>
</div>

<script>
const OMZET = @json($omzetSeries);
const TOP = @json($topBarang->map(fn($t) => ['nama' => $t->barang?->nama, 'qty' => (int) $t->total_qty]));
window.addEventListener('load', () => {
    if (!window.Chart) return;
    const c1 = document.getElementById('chartOmzet');
    if (c1) new Chart(c1, { type: 'line',
        data: { labels: OMZET.labels, datasets: [{ label: 'Omzet', data: OMZET.data, borderColor: '#2563eb', tension: 0.3, fill: false }] },
        options: { plugins: { legend: { display: false } } }
    });
    const c2 = document.getElementById('chartTop');
    if (c2) new Chart(c2, { type: 'bar',
        data: { labels: TOP.map(t => t.nama), datasets: [{ data: TOP.map(t => t.qty), backgroundColor: '#6366f1' }] },
        options: { plugins: { legend: { display: false } }, indexAxis: 'y' }
    });
});
</script>
@endsection
