@extends('layouts.app')
@section('title', 'Laporan Laba')
@section('breadcrumb', 'Admin / Laporan Laba')
@section('content')
<h1 class="text-2xl font-bold mb-4">Laporan Laba</h1>

<form method="GET" class="bg-white rounded shadow p-4 mb-4 flex flex-wrap gap-2 items-end">
    <div>
        <label class="block text-xs uppercase text-gray-500">Mulai</label>
        <input type="date" name="start" value="{{ $start->toDateString() }}" class="border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-xs uppercase text-gray-500">Sampai</label>
        <input type="date" name="end" value="{{ $end->toDateString() }}" class="border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-xs uppercase text-gray-500">Granularity</label>
        <select name="g" class="border rounded px-3 py-2">
            @foreach (['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'yearly' => 'Tahunan'] as $v => $l)
                <option value="{{ $v }}" @selected($g === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>
    <button class="bg-indigo-600 text-white px-4 py-2 rounded">Terapkan</button>
    <div class="ml-auto flex gap-2">
        @foreach (['today' => 'Hari Ini', 'yesterday' => 'Kemarin', '7d' => '7 Hari', '30d' => '30 Hari', 'this_month' => 'Bulan Ini', 'this_year' => 'Tahun Ini'] as $p => $l)
            <a href="?preset={{ $p }}&g={{ $g }}" class="text-xs px-3 py-2 border rounded hover:bg-gray-50">{{ $l }}</a>
        @endforeach
    </div>
</form>

<div class="grid md:grid-cols-5 gap-3 mb-4">
    @foreach ([
        ['Omzet', $data['totals']['omzet'], 'text-blue-600'],
        ['HPP', $data['totals']['hpp'], 'text-gray-600'],
        ['Laba Kotor', $data['totals']['laba_kotor'], 'text-green-600'],
        ['Biaya', $data['totals']['biaya'], 'text-red-600'],
        ['Laba Bersih', $data['totals']['laba_bersih'], 'text-indigo-600'],
    ] as [$lbl, $val, $cls])
        <div class="bg-white rounded shadow p-4">
            <div class="text-xs uppercase text-gray-500">{{ $lbl }}</div>
            <div class="text-xl font-bold {{ $cls }}">{{ format_rupiah($val) }}</div>
        </div>
    @endforeach
</div>

<div class="bg-white rounded shadow p-4 mb-4">
    <canvas id="chartLaba" height="80"></canvas>
</div>

<div class="bg-white rounded shadow overflow-x-auto mb-4">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="text-left px-3 py-3">Periode</th>
                <th class="text-right px-3 py-3">Omzet</th>
                <th class="text-right px-3 py-3">HPP</th>
                <th class="text-right px-3 py-3">Laba Kotor</th>
                <th class="text-right px-3 py-3">Biaya</th>
                <th class="text-right px-3 py-3">Laba Bersih</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data['rows'] as $r)
                <tr class="border-t">
                    <td class="px-3 py-2 font-mono text-xs">{{ $r['bucket'] }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($r['omzet']) }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($r['hpp']) }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($r['laba_kotor']) }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($r['biaya']) }}</td>
                    <td class="px-3 py-2 text-right font-semibold {{ $r['laba_bersih'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ format_rupiah($r['laba_bersih']) }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Tidak ada data di rentang ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="flex gap-2">
    <a href="{{ route('laporan.laba.pdf', request()->all()) }}" class="bg-red-600 text-white px-4 py-2 rounded">Ekspor PDF</a>
    <a href="{{ route('laporan.laba.csv', request()->all()) }}" class="bg-green-600 text-white px-4 py-2 rounded">Ekspor CSV</a>
</div>

<script>
const ROWS = @json($data['rows']);
window.addEventListener('load', () => {
    if (!window.Chart) return;
    const ctx = document.getElementById('chartLaba');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ROWS.map(r => r.bucket),
            datasets: [
                {label: 'Omzet', data: ROWS.map(r => r.omzet), borderColor: '#2563eb', tension: 0.3},
                {label: 'Laba Bersih', data: ROWS.map(r => r.laba_bersih), borderColor: '#16a34a', tension: 0.3},
            ],
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
});
</script>
@endsection
