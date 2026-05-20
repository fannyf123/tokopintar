@extends('layouts.app')
@section('title', 'Penjualan ' . $penjualan->nomor)
@section('breadcrumb', 'Penjualan / Detail')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-3xl">
    <div class="flex justify-between items-start mb-4">
        <div>
            <h1 class="text-xl font-bold">{{ $penjualan->nomor }}</h1>
            <p class="text-sm text-gray-500">{{ format_tanggal_id($penjualan->tanggal, true) }} · Kasir: {{ $penjualan->kasir?->name }}</p>
            <p class="text-sm text-gray-500">Pelanggan: {{ $penjualan->pelanggan?->nama ?? 'Umum' }}</p>
        </div>
        <a href="{{ route('penjualan.struk', $penjualan) }}" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded">Cetak Struk</a>
    </div>
    <table class="w-full text-sm border">
        <thead class="bg-gray-50 text-xs uppercase">
            <tr>
                <th class="text-left px-3 py-2">Barang</th>
                <th class="px-3 py-2">Qty</th>
                <th class="px-3 py-2">Harga</th>
                <th class="px-3 py-2">Diskon</th>
                <th class="px-3 py-2">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualan->details as $d)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $d->barang?->nama }}</td>
                    <td class="px-3 py-2 text-center">{{ $d->qty }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($d->harga_jual_saat_itu) }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($d->diskon_item) }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($d->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-gray-50">
            <tr><td colspan="4" class="text-right px-3 py-2">Subtotal</td><td class="px-3 py-2 text-right">{{ format_rupiah($penjualan->total) }}</td></tr>
            <tr><td colspan="4" class="text-right px-3 py-2">Diskon</td><td class="px-3 py-2 text-right">- {{ format_rupiah($penjualan->diskon) }}</td></tr>
            <tr><td colspan="4" class="text-right px-3 py-2">Pajak</td><td class="px-3 py-2 text-right">+ {{ format_rupiah($penjualan->pajak) }}</td></tr>
            <tr class="font-bold"><td colspan="4" class="text-right px-3 py-2">Grand Total</td><td class="px-3 py-2 text-right">{{ format_rupiah($penjualan->grand_total) }}</td></tr>
            <tr><td colspan="4" class="text-right px-3 py-2">Dibayar ({{ strtoupper($penjualan->metode_bayar) }})</td><td class="px-3 py-2 text-right">{{ format_rupiah($penjualan->dibayar) }}</td></tr>
            <tr><td colspan="4" class="text-right px-3 py-2">Kembalian</td><td class="px-3 py-2 text-right">{{ format_rupiah($penjualan->kembalian) }}</td></tr>
        </tfoot>
    </table>
    <a href="{{ route('penjualan.index') }}" class="text-indigo-600 mt-4 inline-block">&larr; Kembali</a>
</div>
@endsection
