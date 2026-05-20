@extends('layouts.app')
@section('title', 'Insight AI')
@section('breadcrumb', 'Admin / Insight AI')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Insight AI Lokal</h1>
    <form method="POST" action="{{ route('insight.regenerate') }}">@csrf
        <button class="bg-indigo-600 text-white px-4 py-2 rounded">Generate Ulang Sekarang</button>
    </form>
</div>

<div class="grid md:grid-cols-2 gap-4 mb-4">
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="font-semibold mb-2">Top 10 Fast Mover</h2>
        <table class="w-full text-sm"><tbody>
            @forelse ($top as $i)
                <tr class="border-t"><td class="py-1">{{ $i->barang?->nama }}</td><td class="text-right text-xs text-gray-500">v={{ number_format($i->velocity_30, 2) }}, dos={{ number_format($i->days_of_supply, 1) }}</td></tr>
            @empty <tr><td class="py-2 text-gray-400">Belum ada data.</td></tr>
            @endforelse
        </tbody></table>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="font-semibold mb-2">Top 10 Dead Stock</h2>
        <table class="w-full text-sm"><tbody>
            @forelse ($dead as $i)
                <tr class="border-t"><td class="py-1">{{ $i->barang?->nama }}</td><td class="text-right text-xs text-gray-500">dos={{ number_format($i->days_of_supply, 1) }}</td></tr>
            @empty <tr><td class="py-2 text-gray-400">Belum ada data.</td></tr>
            @endforelse
        </tbody></table>
    </div>
</div>

<form method="GET" class="bg-white rounded shadow p-3 mb-4 flex gap-2">
    <select name="kelas" class="border rounded px-3 py-2">
        <option value="">Semua kelas</option>
        @foreach ($kelasList as $k)<option value="{{ $k }}" @selected($kelasFilter === $k)>{{ $k }}</option>@endforeach
    </select>
    <select name="abc" class="border rounded px-3 py-2">
        <option value="">Semua ABC</option>
        @foreach (['A', 'B', 'C'] as $a)<option value="{{ $a }}" @selected($abcFilter === $a)>{{ $a }}</option>@endforeach
    </select>
    <button class="bg-gray-700 text-white px-4 rounded">Filter</button>
</form>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="text-left px-3 py-3">Barang</th>
                <th class="text-right px-3 py-3">Velocity/hari</th>
                <th class="text-right px-3 py-3">DoS</th>
                <th class="text-center px-3 py-3">Kelas</th>
                <th class="text-center px-3 py-3">ABC</th>
                <th class="text-right px-3 py-3">Forecast 7h</th>
                <th class="text-left px-3 py-3">Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $i)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $i->barang?->nama }} <span class="text-xs text-gray-400">{{ $i->barang?->kategori?->nama }}</span></td>
                    <td class="px-3 py-2 text-right">{{ number_format($i->velocity_30, 2) }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($i->days_of_supply, 1) }}</td>
                    <td class="px-3 py-2 text-center text-xs">{{ $i->kelas }}</td>
                    <td class="px-3 py-2 text-center">{{ $i->abc_class ?? '-' }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($i->forecast_7, 1) }}</td>
                    <td class="px-3 py-2 text-gray-700">{{ $i->rekomendasi_text }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Belum ada insight. Klik "Generate Ulang Sekarang".</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
