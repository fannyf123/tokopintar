@extends('layouts.app')
@section('title', 'Ringkasan Harian - TOKOPINTAR')
@section('page_title', 'Ringkasan Hari Ini')
@section('content')
@php $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.'); @endphp

<div class="card mb-3" style="background:linear-gradient(135deg,#4361ee,#3f37c9);color:#fff;border:none;">
    <div class="card-body">
        <h5 class="fw-bold mb-1"><i class="fas fa-robot me-2"></i>Ringkasan {{ $tanggal->translatedFormat('l, d F Y') }}</h5>
        <p class="mb-0 opacity-75 small">Rangkuman singkat kondisi toko hari ini.</p>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#4361ee,#3f37c9);">
            <div class="card-body"><div class="stat-label">Penjualan</div><div class="stat-value">{{ $rp($omzet) }}</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#16a34a,#059669);">
            <div class="card-body"><div class="stat-label">Untung</div><div class="stat-value">{{ $rp($untung) }}</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#0891b2,#0e7490);">
            <div class="card-body"><div class="stat-label">Transaksi</div><div class="stat-value">{{ $trx }}</div></div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card stat-card" style="background:linear-gradient(135deg,#ea580c,#c2410c);">
            <div class="card-body"><div class="stat-label">Pengeluaran</div><div class="stat-value">{{ $rp($pengeluaran) }}</div></div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="fas fa-comment-dots text-primary me-2"></i>Kata Asisten</h6>
        @foreach ($kalimat as $k)
            <div class="d-flex align-items-start mb-2">
                <i class="fas {{ $k['icon'] }} text-{{ $k['warna'] }} me-2 mt-1"></i>
                <span>{{ $k['teks'] }}</span>
            </div>
        @endforeach
        @if ($hutang_jml > 0)
            <div class="d-flex align-items-start mb-2">
                <i class="fas fa-hand-holding-usd text-warning me-2 mt-1"></i>
                <span>Ada {{ $hutang_jml }} transaksi hutang hari ini, total sisa {{ $rp($hutang_sisa) }}.</span>
            </div>
        @endif
    </div>
</div>

@if ($terlaris->count() > 0)
<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="fas fa-fire text-danger me-2"></i>Barang Terlaris Hari Ini</h6>
        @foreach ($terlaris as $t)
            <div class="d-flex justify-content-between border-bottom py-2">
                <span class="fw-semibold">{{ $t->nama }}</span>
                <span class="text-muted">{{ $t->total_qty }} {{ $t->satuan }}</span>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
