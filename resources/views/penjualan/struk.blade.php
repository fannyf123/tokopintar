<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><title>Struk {{ $penjualan->nomor }}</title>
<style>
@page { size: 58mm auto; margin: 2mm; }
body { font-family: 'Courier New', monospace; font-size: 10px; line-height: 1.35; max-width: 54mm; margin: 0 auto; color:#000; }
.center { text-align: center; }
.right { text-align: right; }
hr { border: 0; border-top: 1px dashed #000; margin: 4px 0; }
table { width: 100%; border-collapse: collapse; }
td { vertical-align: top; padding: 1px 0; word-break: break-word; }
.bold { font-weight: bold; }
.big { font-size: 12px; }
@media print { .no-print { display: none; } body { width: 54mm; } }
</style>
</head>
<body onload="window.print()">
<div class="center bold big">{{ config('app.name') }}</div>
<div class="center">Struk Penjualan</div>
<hr>
<div>No : {{ $penjualan->nomor }}</div>
<div>Tgl: {{ format_tanggal_id($penjualan->tanggal, true) }}</div>
<div>Ksr: {{ $penjualan->kasir?->name }}</div>
@if ($penjualan->pelanggan)<div>Plg: {{ $penjualan->pelanggan->nama }}</div>@endif
<hr>
<table>
@foreach ($penjualan->details as $d)
    <tr><td colspan="2">{{ $d->barang?->nama }}</td></tr>
    <tr>
        <td>{{ $d->qty }} x {{ format_rupiah($d->harga_jual_saat_itu, false) }}</td>
        <td class="right">{{ format_rupiah($d->subtotal, false) }}</td>
    </tr>
@endforeach
</table>
<hr>
<table>
    <tr><td>Subtotal</td><td class="right">{{ format_rupiah($penjualan->total, false) }}</td></tr>
    @if ($penjualan->diskon > 0)<tr><td>Diskon</td><td class="right">-{{ format_rupiah($penjualan->diskon, false) }}</td></tr>@endif
    @if ($penjualan->pajak > 0)<tr><td>Pajak</td><td class="right">+{{ format_rupiah($penjualan->pajak, false) }}</td></tr>@endif
    <tr class="bold"><td>TOTAL</td><td class="right">{{ format_rupiah($penjualan->grand_total, false) }}</td></tr>
    <tr><td>Bayar ({{ strtoupper($penjualan->metode_bayar) }})</td><td class="right">{{ format_rupiah($penjualan->dibayar, false) }}</td></tr>
    @if ($penjualan->status === 'hutang')
    <tr class="bold"><td>SISA HUTANG</td><td class="right">{{ format_rupiah($penjualan->grand_total - $penjualan->dibayar, false) }}</td></tr>
    @else
    <tr><td>Kembali</td><td class="right">{{ format_rupiah($penjualan->kembalian, false) }}</td></tr>
    @endif
</table>
<hr>
@if ($penjualan->kode_verifikasi)
<div class="center">Kode Verifikasi</div>
<div class="center bold big">{{ $penjualan->kode_verifikasi }}</div>
<div class="center" style="font-size:8px;">Tunjukkan kode ini untuk klaim/komplain</div>
<hr>
@endif
<div class="center">Terima kasih atas kunjungan Anda</div>
<div class="no-print center" style="margin-top:8px"><button onclick="window.print()">Print</button> <button onclick="window.close()">Tutup</button></div>
</body></html>
