@extends('layouts.app')
@section('title', 'Simulator Harga - TOKOPINTAR')
@section('page_title', 'Simulator What-If Harga')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-semibold mb-1">Pilih Barang</label>
                <select name="barang_id" class="form-select form-select-sm" required onchange="this.form.submit()">
                    <option value="">-- pilih barang --</option>
                    @foreach ($barangs as $b)
                        <option value="{{ $b->id }}" @selected($barang && $barang->id == $b->id)>{{ $b->nama }} (Rp {{ number_format($b->harga_jual, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>
            @if ($barang)
                <div class="col-md-4">
                    <label class="form-label small fw-semibold mb-1">Harga Jual Baru (Rp)</label>
                    <input type="number" name="harga_baru" value="{{ $hargaBaru ?: $barang->harga_jual }}" min="0" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-sm btn-primary"><i class="fas fa-calculator me-1"></i> Hitung</button>
                </div>
            @endif
        </form>
    </div>
</div>

@if ($barang && $elasticity)
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="fw-bold"><i class="fas fa-chart-line text-info me-2"></i>Sensitivitas Harga (Elasticity)</h6>
            @if ($elasticity['elasticity'] !== null)
                <div class="row mt-2">
                    <div class="col-md-4"><small class="text-muted">Nilai Elasticity</small><div class="fs-4 fw-bold">{{ $elasticity['elasticity'] }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Sumber Data</small><div>{{ $elasticity['samples'] ?? 0 }} kali ubah harga</div></div>
                    <div class="col-md-4"><small class="text-muted">Interpretasi</small><div class="small">{{ $elasticity['interpretation'] ?? '-' }}</div></div>
                </div>
            @else
                <p class="text-muted small mb-0">Belum cukup history perubahan harga untuk hitung elasticity. Akan pakai default -0.5 (cukup elastis).</p>
            @endif
        </div>
    </div>
@endif

@if ($result)
    <div class="card mb-3">
        <div class="card-body">
            <h6 class="fw-bold mb-3"><i class="fas fa-magic-wand-sparkles text-primary me-2"></i>Prediksi 30 Hari ke Depan</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded p-3 bg-light">
                        <small class="text-uppercase text-muted">Skenario Harga Sekarang</small>
                        <table class="table table-sm mt-2 mb-0">
                            <tr><td>Harga Jual</td><td class="text-end fw-bold">{{ format_rupiah($result['harga_lama']) }}</td></tr>
                            <tr><td>Modal</td><td class="text-end">{{ format_rupiah($result['modal']) }}</td></tr>
                            <tr><td>Margin/unit</td><td class="text-end">{{ format_rupiah($result['margin_lama']) }}</td></tr>
                            <tr><td>Volume Estimasi</td><td class="text-end">{{ $result['volume_lama_30hr'] }} unit</td></tr>
                            <tr class="table-light"><td><strong>Total Untung</strong></td><td class="text-end fw-bold">{{ format_rupiah($result['profit_lama_30hr']) }}</td></tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded p-3 {{ $result['profit_delta'] >= 0 ? 'bg-success bg-opacity-10' : 'bg-danger bg-opacity-10' }}">
                        <small class="text-uppercase {{ $result['profit_delta'] >= 0 ? 'text-success' : 'text-danger' }} fw-bold">Skenario Harga Baru</small>
                        <table class="table table-sm mt-2 mb-0">
                            <tr><td>Harga Jual</td><td class="text-end fw-bold">{{ format_rupiah($result['harga_baru']) }}</td></tr>
                            <tr><td>Modal</td><td class="text-end">{{ format_rupiah($result['modal']) }}</td></tr>
                            <tr><td>Margin/unit</td><td class="text-end">{{ format_rupiah($result['margin_baru']) }}</td></tr>
                            <tr><td>Volume Estimasi</td><td class="text-end">{{ $result['volume_baru_30hr'] }} unit</td></tr>
                            <tr class="{{ $result['profit_delta'] >= 0 ? 'table-success' : 'table-danger' }}">
                                <td><strong>Total Untung</strong></td>
                                <td class="text-end fw-bold">{{ format_rupiah($result['profit_baru_30hr']) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="alert {{ $result['profit_delta'] >= 0 ? 'alert-success' : 'alert-danger' }} mt-3 mb-0">
                <strong>{{ $result['profit_delta'] >= 0 ? '✅ Untung naik' : '⚠️ Untung turun' }} {{ format_rupiah(abs($result['profit_delta'])) }}/30hr ({{ $result['profit_delta_persen'] }}%)</strong><br>
                <small>Rekomendasi: {{ $result['profit_delta'] >= 0 ? 'Naikkan harga sesuai simulasi.' : 'Pertimbangkan ulang. Harga baru bisa kurangi profit total.' }}</small>
            </div>
        </div>
    </div>
@endif
@endsection
