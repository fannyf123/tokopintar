@extends('layouts.app')
@section('title', 'Detail Pengeluaran')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-xl">
    <h1 class="text-xl font-bold mb-3">Detail Pengeluaran</h1>
    <dl class="text-sm space-y-1">
        <div class="flex"><dt class="w-32 text-gray-500">Tanggal</dt><dd>{{ format_tanggal_id($pengeluaran->tanggal) }}</dd></div>
        <div class="flex"><dt class="w-32 text-gray-500">Kategori</dt><dd>{{ ucfirst($pengeluaran->kategori) }}</dd></div>
        <div class="flex"><dt class="w-32 text-gray-500">Jumlah</dt><dd class="font-semibold">{{ format_rupiah($pengeluaran->jumlah) }}</dd></div>
        <div class="flex"><dt class="w-32 text-gray-500">Catatan</dt><dd>{{ $pengeluaran->catatan ?? '-' }}</dd></div>
    </dl>
    @if ($pengeluaran->bukti)
        <img src="{{ asset('storage/' . $pengeluaran->bukti) }}" class="mt-3 max-w-xs border rounded" alt="Bukti">
    @endif
    <a href="{{ route('pengeluaran.index') }}" class="text-indigo-600 mt-4 inline-block">&larr; Kembali</a>
</div>
@endsection
