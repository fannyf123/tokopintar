@extends('layouts.app')
@section('title', 'Penjualan ' . $penjualan->nomor . ' - TOKOPINTAR')
@section('page_title', 'Penjualan ' . $penjualan->nomor)
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1">{{ $penjualan->nomor }}</h5>
                <p class="text-muted small mb-0">{{ format_tanggal_id($penjualan->tanggal, true) }} · Kasir: {{ $penjualan->kasir?->name }}</p>
                <p class="text-muted small mb-0">Pelanggan: {{ $penjualan->pelanggan?->nama ?? 'Umum' }}</p>
            </div>
            <a href="{{ route('penjualan.struk', $penjualan) }}" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-print me-1"></i> Cetak Struk</a>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Barang</th>
                        <th class="text-center" style="width:80px;">Qty</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Diskon</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualan->details as $d)
                        <tr>
                            <td class="fw-semibold">{{ $d->barang?->nama }}</td>
                            <td class="text-center">{{ $d->qty }}</td>
                            <td class="text-end">{{ format_rupiah($d->harga_jual_saat_itu) }}</td>
                            <td class="text-end">{{ format_rupiah($d->diskon_item) }}</td>
                            <td class="text-end">{{ format_rupiah($d->subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr><td colspan="4" class="text-end">Subtotal</td><td class="text-end">{{ format_rupiah($penjualan->total) }}</td></tr>
                    <tr><td colspan="4" class="text-end">Diskon</td><td class="text-end">- {{ format_rupiah($penjualan->diskon) }}</td></tr>
                    <tr><td colspan="4" class="text-end">Pajak</td><td class="text-end">+ {{ format_rupiah($penjualan->pajak) }}</td></tr>
                    <tr class="fw-bold fs-6"><td colspan="4" class="text-end">Grand Total</td><td class="text-end">{{ format_rupiah($penjualan->grand_total) }}</td></tr>
                    <tr><td colspan="4" class="text-end">Dibayar ({{ strtoupper($penjualan->metode_bayar) }})</td><td class="text-end">{{ format_rupiah($penjualan->dibayar) }}</td></tr>
                    <tr><td colspan="4" class="text-end">Kembalian</td><td class="text-end">{{ format_rupiah($penjualan->kembalian) }}</td></tr>
                </tfoot>
            </table>
        </div>
        <a href="{{ route('penjualan.index') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    </div>
</div>
@endsection
