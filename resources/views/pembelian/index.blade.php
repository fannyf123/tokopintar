@extends('layouts.app')
@section('title', 'Pembelian')
@section('breadcrumb', 'Inventory / Pembelian')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Pembelian</h1>
    <a href="{{ route('pembelian.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">+ Pembelian</a>
</div>
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
                <th class="text-left px-3 py-3">Nomor</th>
                <th class="text-left px-3 py-3">Tanggal</th>
                <th class="text-left px-3 py-3">Supplier</th>
                <th class="text-right px-3 py-3">Total</th>
                <th class="text-center px-3 py-3">Status</th>
                <th class="px-3 py-3 w-20">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $p)
                <tr class="border-t">
                    <td class="px-3 py-2 font-mono text-xs">{{ $p->nomor }}</td>
                    <td class="px-3 py-2">{{ format_tanggal_id($p->tanggal) }}</td>
                    <td class="px-3 py-2">{{ $p->supplier?->nama }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($p->total) }}</td>
                    <td class="px-3 py-2 text-center">
                        @php $cls = ['draft' => 'bg-gray-200', 'diterima' => 'bg-green-200 text-green-800', 'batal' => 'bg-red-200 text-red-800']; @endphp
                        <span class="px-2 py-1 rounded text-xs {{ $cls[$p->status] ?? '' }}">{{ $p->status }}</span>
                    </td>
                    <td class="px-3 py-2 text-right"><a href="{{ route('pembelian.show', $p) }}" class="text-indigo-600">Detail</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Belum ada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
