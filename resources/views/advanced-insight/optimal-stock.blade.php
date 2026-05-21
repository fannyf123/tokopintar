@extends('layouts.app')
@section('title', 'Optimal Stock - TOKOPINTAR')
@section('page_title', 'Optimal Stock & Forecast Pintar')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-semibold mb-1">Pilih Barang</label>
                <select name="barang_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- pilih barang untuk analisa --</option>
                    @foreach ($barangs as $b)
                        <option value="{{ $b->id }}" @selected($barang && $barang->id == $b->id)>{{ $b->nama }} (stok: {{ $b->stok_current }})</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if ($barang && $result)
    <div class="alert alert-info">
        <strong>{{ $barang->nama }}</strong><br>
        Stok sekarang: <strong>{{ $barang->stok_current }}</strong> · Stok min manual: <strong>{{ $barang->stok_min }}</strong>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <small class="text-uppercase text-primary fw-bold">Reorder Point</small>
                    <div class="fs-3 fw-bold text-primary">{{ $result['reorder_point'] }}</div>
                    <small class="text-muted">Pesan ulang saat stok turun ke angka ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <small class="text-uppercase text-warning fw-bold">Safety Stock</small>
                    <div class="fs-3 fw-bold text-warning">{{ $result['safety_stock'] }}</div>
                    <small class="text-muted">Buffer minimum (95% service level)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <small class="text-uppercase text-success fw-bold">EOQ</small>
                    <div class="fs-3 fw-bold text-success">{{ $result['eoq'] }}</div>
                    <small class="text-muted">Qty optimal per order (≈ 14 hari supply)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body">
                    <small class="text-uppercase text-info fw-bold">Demand Avg/hari</small>
                    <div class="fs-3 fw-bold text-info">{{ $result['demand_avg'] ?? 0 }}</div>
                    <small class="text-muted">±{{ $result['demand_std'] ?? 0 }} (std dev)</small>
                </div>
            </div>
        </div>
    </div>

    @if ($forecast && $forecast['method'] === 'holt_winters')
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="fw-bold"><i class="fas fa-magic-wand-sparkles text-primary me-2"></i>Forecast 7 Hari ke Depan</h6>
                <div class="d-flex align-items-center gap-3 mt-3">
                    <div class="display-4 fw-bold text-primary">{{ $forecast['forecast_7'] }}</div>
                    <div>
                        <strong>unit total estimasi terjual</strong><br>
                        <small class="text-muted">Method: Holt-Winters (level + trend + seasonality 7 hari).<br>
                        Lebih akurat dari rata-rata simple karena tangkap pola hari kerja vs weekend.</small>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="alert alert-warning small">
        <strong>📋 Saran Aksi:</strong>
        @if ($barang->stok_current <= $result['reorder_point'])
            <span class="text-danger fw-bold">REORDER SEKARANG.</span> Stok ({{ $barang->stok_current }}) sudah di bawah Reorder Point ({{ $result['reorder_point'] }}).
            Pesan minimal {{ $result['eoq'] }} unit ke pemasok.
        @else
            Stok aman. Reorder saat menyentuh {{ $result['reorder_point'] }} unit. Order EOQ optimal: {{ $result['eoq'] }} unit per kali pesan.
        @endif
    </div>
@elseif ($barang && $result['method'] === 'insufficient_data')
    <div class="alert alert-warning">Data jualan barang ini kurang dari 7 hari, belum bisa analisa.</div>
@endif
@endsection
