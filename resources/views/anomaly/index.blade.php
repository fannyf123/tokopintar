@extends('layouts.app')
@section('title', 'Deteksi Anomali - TOKOPINTAR')
@section('page_title', 'Deteksi Anomali Toko')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="fw-bold mb-0">Anti-Fraud & Stock Leak Detection</h6>
    <form method="POST" action="{{ route('anomaly.detect') }}" class="d-inline">@csrf
        <button class="btn btn-primary"><i class="fas fa-shield-alt me-1"></i> Cek Sekarang</button>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-uppercase text-danger fw-bold">Critical</small>
                        <div class="fs-3 fw-bold text-danger">{{ $summary['critical'] }}</div>
                    </div>
                    <i class="fas fa-exclamation-triangle text-danger fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-uppercase text-warning fw-bold">Warning</small>
                        <div class="fs-3 fw-bold text-warning">{{ $summary['warning'] }}</div>
                    </div>
                    <i class="fas fa-flag text-warning fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-uppercase text-info fw-bold">Info</small>
                        <div class="fs-3 fw-bold text-info">{{ $summary['info'] }}</div>
                    </div>
                    <i class="fas fa-info-circle text-info fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Jenis</label>
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach (['fraud_kasir', 'stock_leak', 'sales_spike', 'sales_drop', 'diskon_spike', 'void_pattern', 'offhours_trx'] as $j)
                        <option value="{{ $j }}" @selected($jenis === $j)>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Severity</label>
                <select name="severity" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="critical" @selected($severity === 'critical')>Critical</option>
                    <option value="warning" @selected($severity === 'warning')>Warning</option>
                    <option value="info" @selected($severity === 'info')>Info</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Status</label>
                <select name="resolved" class="form-select form-select-sm">
                    <option value="0" @selected($resolved === '0')>Belum diselesaikan</option>
                    <option value="1" @selected($resolved === '1')>Sudah diselesaikan</option>
                    <option value="" @selected($resolved === '')>Semua</option>
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
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Severity</th>
                        <th>Jenis</th>
                        <th>Detail Alert</th>
                        <th class="text-end">Score</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sevBadge = ['critical'=>'danger','warning'=>'warning','info'=>'info']; @endphp
                    @forelse ($items as $a)
                        <tr class="{{ $a->resolved ? 'opacity-50' : '' }}">
                            <td class="small">{{ $a->created_at->format('d M H:i') }}</td>
                            <td><span class="badge bg-{{ $sevBadge[$a->severity] }}">{{ strtoupper($a->severity) }}</span></td>
                            <td><code class="small">{{ $a->jenis }}</code></td>
                            <td>
                                <strong>{{ $a->judul }}</strong>
                                @if ($a->barang)<br><small class="text-muted">Barang: {{ $a->barang->nama }}</small>@endif
                                @if ($a->user)<br><small class="text-muted">User: {{ $a->user->name }}</small>@endif
                                <br><small class="text-muted">{{ $a->detail }}</small>
                            </td>
                            <td class="text-end fw-bold">{{ number_format($a->score, 2) }}</td>
                            <td>
                                @if (! $a->resolved)
                                    <form method="POST" action="{{ route('anomaly.resolve', $a) }}" class="d-inline">@csrf
                                        <button class="btn btn-sm btn-success" title="Tandai Selesai"><i class="fas fa-check"></i></button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada anomali. Klik "Cek Sekarang" untuk scan ulang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
