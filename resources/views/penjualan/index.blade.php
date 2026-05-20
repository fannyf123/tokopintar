@extends('layouts.app')
@section('title', 'Riwayat Penjualan')
@section('breadcrumb', 'Penjualan')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Riwayat Penjualan</h1>
    <a href="{{ route('pos.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">+ POS Baru</a>
</div>
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
                <th class="text-left px-3 py-3">Nomor</th>
                <th class="text-left px-3 py-3">Tanggal</th>
                <th class="text-left px-3 py-3">Kasir</th>
                <th class="text-left px-3 py-3">Pelanggan</th>
                <th class="text-right px-3 py-3">Grand Total</th>
                <th class="text-center px-3 py-3">Metode</th>
                <th class="px-3 py-3 w-24">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $p)
                <tr class="border-t">
                    <td class="px-3 py-2 font-mono text-xs">{{ $p->nomor }}</td>
                    <td class="px-3 py-2">{{ format_tanggal_id($p->tanggal, true) }}</td>
                    <td class="px-3 py-2">{{ $p->kasir?->name }}</td>
                    <td class="px-3 py-2">{{ $p->pelanggan?->nama ?? '-' }}</td>
                    <td class="px-3 py-2 text-right font-semibold">{{ format_rupiah($p->grand_total) }}</td>
                    <td class="px-3 py-2 text-center">{{ strtoupper($p->metode_bayar) }}</td>
                    <td class="px-3 py-2 text-right">
                        <a href="{{ route('penjualan.show', $p) }}" class="text-indigo-600">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Belum ada transaksi.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
