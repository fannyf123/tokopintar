@extends('layouts.app')
@section('title', 'Kadaluarsa')
@section('breadcrumb', 'Inventory / Kadaluarsa')
@section('content')
<h1 class="text-2xl font-bold mb-4">Manajemen Kadaluarsa (FEFO)</h1>
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="text-left px-3 py-3">Barang</th>
                <th class="text-left px-3 py-3">No Batch</th>
                <th class="text-right px-3 py-3">Sisa</th>
                <th class="text-left px-3 py-3">Tgl Masuk</th>
                <th class="text-left px-3 py-3">Tgl Kadaluarsa</th>
                <th class="text-center px-3 py-3">Status</th>
                <th class="px-3 py-3 w-32">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $b)
                @php
                    $hari = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($b->tanggal_kadaluarsa)->startOfDay(), false);
                    if ($hari < 0) { $cls = 'bg-red-100 text-red-800'; $lbl = 'EXPIRED'; }
                    elseif ($hari <= 30) { $cls = 'bg-yellow-100 text-yellow-800'; $lbl = "{$hari} hari"; }
                    else { $cls = 'bg-green-100 text-green-800'; $lbl = "{$hari} hari"; }
                @endphp
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $b->barang?->nama }}</td>
                    <td class="px-3 py-2 font-mono text-xs">{{ $b->no_batch ?? '-' }}</td>
                    <td class="px-3 py-2 text-right">{{ $b->qty_sisa }}</td>
                    <td class="px-3 py-2">{{ format_tanggal_id($b->tanggal_masuk) }}</td>
                    <td class="px-3 py-2">{{ format_tanggal_id($b->tanggal_kadaluarsa) }}</td>
                    <td class="px-3 py-2 text-center"><span class="px-2 py-1 rounded text-xs {{ $cls }}">{{ $lbl }}</span></td>
                    <td class="px-3 py-2 text-right">
                        @if ($hari < 0)
                        <form method="POST" action="{{ route('expiry.buang', $b) }}" onsubmit="return confirm('Buang stok kadaluarsa? Tercatat di mutasi.')">
                            @csrf
                            <button class="text-red-600 hover:underline">Buang Stok</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Tidak ada batch dengan tanggal kadaluarsa.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
