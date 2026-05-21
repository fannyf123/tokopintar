@extends('layouts.app')
@section('title', 'Detail Pengeluaran - TOKOPINTAR')
@section('page_title', 'Detail Pengeluaran')
@section('content')
<div class="card">
    <div class="card-body">
        <table class="table table-sm">
            <tr><td class="text-muted" style="width:160px;">Tanggal</td><td>{{ format_tanggal_id($pengeluaran->tanggal) }}</td></tr>
            <tr><td class="text-muted">Kategori</td><td><span class="badge bg-light text-dark">{{ ucfirst($pengeluaran->kategori) }}</span></td></tr>
            <tr><td class="text-muted">Jumlah</td><td class="fw-bold">{{ format_rupiah($pengeluaran->jumlah) }}</td></tr>
            <tr><td class="text-muted">Catatan</td><td>{{ $pengeluaran->catatan ?? '-' }}</td></tr>
        </table>
        @if ($pengeluaran->bukti)
            <img src="{{ asset('storage/' . $pengeluaran->bukti) }}" class="img-thumbnail mt-3" style="max-width:300px;" alt="Bukti">
        @endif
        <div class="mt-3">
            <a href="{{ route('pengeluaran.index') }}" class="btn btn-sm btn-light"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>
</div>
@endsection
