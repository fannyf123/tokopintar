@extends('layouts.app')
@section('title', 'Insight Pelanggan - TOKOPINTAR')
@section('page_title', 'Analisa Pelanggan (RFM)')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="fw-bold mb-0">Segmentasi Pelanggan + Prediksi Churn</h6>
    <form method="POST" action="{{ route('customer-insight.regenerate') }}" class="d-inline">@csrf
        <button class="btn btn-primary"><i class="fas fa-sync-alt me-1"></i> Hitung Ulang Sekarang</button>
    </form>
</div>

<div class="row g-3 mb-3">
    @php $segBadge = ['CHAMPION'=>'success','LOYAL'=>'primary','POTENTIAL'=>'info','AT_RISK'=>'warning','LOST'=>'danger','NEW'=>'secondary']; @endphp
    @foreach (['CHAMPION', 'LOYAL', 'POTENTIAL', 'AT_RISK', 'LOST', 'NEW'] as $seg)
        @php $row = $summary[$seg] ?? null; @endphp
        <div class="col-6 col-md">
            <div class="card">
                <div class="card-body">
                    <span class="badge bg-{{ $segBadge[$seg] }}">{{ $seg }}</span>
                    <div class="fs-5 fw-bold mt-2">{{ $row->cnt ?? 0 }}</div>
                    <small class="text-muted">{{ format_rupiah($row->total_value ?? 0) }}</small>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Pelanggan Beresiko Hilang</div>
                <div class="fs-4 fw-bold text-danger">{{ $totalChurn }}</div>
                <small class="text-muted">Belanja terlambat dari pola normal mereka</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Total CLV (12 Bulan)</div>
                <div class="fs-4 fw-bold text-success">{{ format_rupiah($totalClv) }}</div>
                <small class="text-muted">Estimasi nilai pelanggan dalam setahun ke depan</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Segmen</label>
                <select name="segment" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach (['CHAMPION', 'LOYAL', 'POTENTIAL', 'AT_RISK', 'LOST', 'NEW'] as $s)
                        <option value="{{ $s }}" @selected($segment === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Beresiko Hilang</label>
                <select name="churn" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="1" @selected($churn === '1')>Hanya beresiko</option>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-secondary"><i class="fas fa-filter me-1"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-stack">
                <thead>
                    <tr>
                        <th>Pelanggan</th>
                        <th class="text-center">R/F/M</th>
                        <th class="text-center">Segmen</th>
                        <th class="text-end">Frequency</th>
                        <th class="text-end">Monetary</th>
                        <th class="text-end">CLV 12bln</th>
                        <th class="text-center">Recency</th>
                        <th>Saran Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $i)
                        <tr>
                            <td data-label="Pelanggan">
                                <strong>{{ $i->pelanggan?->nama }}</strong>
                                @if ($i->churn_risk)<span class="badge bg-danger ms-1" style="font-size:9px">CHURN</span>@endif
                                <br><small class="text-muted">{{ $i->pelanggan?->no_hp ?? '-' }}</small>
                            </td>
                            <td data-label="R/F/M" class="text-center"><code>{{ $i->r_score }}/{{ $i->f_score }}/{{ $i->m_score }}</code></td>
                            <td data-label="Segmen" class="text-center"><span class="badge bg-{{ $segBadge[$i->segment] ?? 'secondary' }}">{{ $i->segment }}</span></td>
                            <td data-label="Frequency" class="text-end">{{ $i->frequency }}x</td>
                            <td data-label="Monetary" class="text-end">{{ format_rupiah($i->monetary) }}</td>
                            <td data-label="CLV 12bln" class="text-end fw-bold">{{ format_rupiah($i->clv_estimate) }}</td>
                            <td data-label="Recency" class="text-center">{{ $i->recency_days }} hari lalu</td>
                            <td data-label="Saran Aksi" class="small text-muted" style="max-width:300px">{{ $i->rekomendasi_text }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data, klik "Hitung Ulang Sekarang".</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
