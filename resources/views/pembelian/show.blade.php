@extends('layouts.app')
@section('title', 'Pembelian #' . $pembelian->nomor)
@section('breadcrumb', 'Inventory / Pembelian / Detail')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-start justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold">{{ $pembelian->nomor }}</h1>
            <p class="text-sm text-gray-500">{{ format_tanggal_id($pembelian->tanggal) }} · {{ $pembelian->supplier?->nama }}</p>
        </div>
        <div class="flex items-center gap-2">
            @php $cls = ['draft' => 'bg-gray-200', 'diterima' => 'bg-green-200 text-green-800', 'batal' => 'bg-red-200 text-red-800']; @endphp
            <span class="px-3 py-1 rounded text-xs {{ $cls[$pembelian->status] ?? '' }}">{{ strtoupper($pembelian->status) }}</span>
            @if ($pembelian->status === 'draft')
                <form method="POST" action="{{ route('pembelian.terima', $pembelian) }}" onsubmit="return confirm('Terima dan masukkan ke stok?')">@csrf
                    <button class="bg-green-600 text-white px-4 py-2 rounded">Terima Barang</button>
                </form>
                <form method="POST" action="{{ route('pembelian.batal', $pembelian) }}" onsubmit="return confirm('Batalkan?')">@csrf
                    <button class="bg-red-600 text-white px-4 py-2 rounded">Batal</button>
                </form>
            @endif
        </div>
    </div>
    <table class="w-full text-sm border">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="text-left px-3 py-2">Barang</th>
                <th class="px-3 py-2">Qty</th>
                <th class="px-3 py-2">H. Beli</th>
                <th class="px-3 py-2">Subtotal</th>
                <th class="px-3 py-2">No Batch</th>
                <th class="px-3 py-2">Kadaluarsa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembelian->details as $d)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $d->barang?->nama }}</td>
                    <td class="px-3 py-2 text-center">{{ $d->qty }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($d->harga_beli) }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($d->subtotal) }}</td>
                    <td class="px-3 py-2 font-mono text-xs">{{ $d->no_batch ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $d->tanggal_kadaluarsa ? format_tanggal_id($d->tanggal_kadaluarsa) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
            <tr><td colspan="3" class="text-right px-3 py-2 font-semibold">Total</td><td class="px-3 py-2 text-right font-bold">{{ format_rupiah($pembelian->total) }}</td><td colspan="2"></td></tr>
            <tr><td colspan="3" class="text-right px-3 py-2">Dibayar</td><td class="px-3 py-2 text-right">{{ format_rupiah($pembelian->dibayar) }}</td><td colspan="2"></td></tr>
        </tfoot>
    </table>
    @if ($pembelian->catatan)<p class="text-sm text-gray-600 mt-3">Catatan: {{ $pembelian->catatan }}</p>@endif
    <div class="mt-4">
        <a href="{{ route('pembelian.index') }}" class="text-indigo-600">&larr; Kembali</a>
    </div>
</div>
@endsection
