@extends('layouts.app')
@section('title', 'Mutasi Stok')
@section('breadcrumb', 'Inventory / Mutasi')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Mutasi Stok</h1>
    <a href="{{ route('mutasi.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">+ Mutasi</a>
</div>
<form method="GET" class="bg-white rounded shadow p-3 mb-4 flex gap-2">
    <select name="jenis" class="border rounded px-3 py-2">
        <option value="">Semua jenis</option>
        @foreach ($jenisList as $j)<option value="{{ $j }}" @selected(request('jenis') === $j)>{{ $j }}</option>@endforeach
    </select>
    <button class="bg-gray-700 text-white px-4 rounded">Filter</button>
</form>
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="text-left px-3 py-3">Tanggal</th>
                <th class="text-left px-3 py-3">Barang</th>
                <th class="text-left px-3 py-3">Jenis</th>
                <th class="text-right px-3 py-3">Qty</th>
                <th class="text-left px-3 py-3">Alasan</th>
                <th class="text-left px-3 py-3">Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $m)
                <tr class="border-t">
                    <td class="px-3 py-2 text-xs">{{ $m->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-3 py-2">{{ $m->barang?->nama }}</td>
                    <td class="px-3 py-2 text-xs">{{ $m->jenis }}</td>
                    <td class="px-3 py-2 text-right font-semibold {{ $m->qty_signed < 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $m->qty_signed > 0 ? '+' : '' }}{{ $m->qty_signed }}
                    </td>
                    <td class="px-3 py-2 text-gray-600">{{ $m->alasan }}</td>
                    <td class="px-3 py-2 text-xs">{{ $m->user?->name }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada mutasi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
