@extends('layouts.app')
@section('title', 'Insight AI - TOKOPINTAR')
@section('page_title', 'Insight AI Lokal')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="fw-bold mb-0">Cross-Subsidy & Smart Inventory</h6>
    <form method="POST" action="{{ route('insight.regenerate') }}" class="d-inline">@csrf
        <button class="btn btn-primary"><i class="fas fa-sync-alt me-1"></i> Generate Ulang Sekarang</button>
    </form>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-rocket text-success me-2"></i>Top 10 Fast Mover</h6>
                <table class="table table-sm mb-0">
                    <tbody>
                        @forelse ($top as $i)
                            <tr><td class="fw-semibold">{{ $i->barang?->nama }}</td>
                                <td class="text-end small text-muted">v={{ number_format($i->velocity_30, 2) }} · dos={{ number_format($i->days_of_supply, 1) }}</td></tr>
                        @empty <tr><td class="text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-ban text-danger me-2"></i>Top 10 Dead Stock</h6>
                <table class="table table-sm mb-0">
                    <tbody>
                        @forelse ($dead as $i)
                            <tr><td class="fw-semibold">{{ $i->barang?->nama }}</td>
                                <td class="text-end small text-muted">dos={{ number_format($i->days_of_supply, 1) }}</td></tr>
                        @empty <tr><td class="text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Kelas</label>
                <select name="kelas" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach ($kelasList as $k)<option value="{{ $k }}" @selected($kelasFilter === $k)>{{ $k }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">ABC</label>
                <select name="abc" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach (['A', 'B', 'C'] as $a)<option value="{{ $a }}" @selected($abcFilter === $a)>{{ $a }}</option>@endforeach
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
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th class="text-end">Velocity/hari</th>
                        <th class="text-end">DoS</th>
                        <th class="text-center">Kelas</th>
                        <th class="text-center">ABC</th>
                        <th class="text-end">Forecast 7h</th>
                        <th class="text-end">Margin</th>
                        <th class="text-center">Strategy</th>
                        <th>Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $kelasBadge = ['FAST_MOVER'=>'success','SLOW_MOVER'=>'warning','DEAD_STOCK'=>'danger','NORMAL'=>'info','NEW'=>'secondary']; @endphp
                    @php $stratBadge = ['LOSS_LEADER'=>'warning','PROFIT_DRIVER'=>'success','BALANCED'=>'info']; @endphp
                    @forelse ($items as $i)
                        <tr>
                            <td><span class="fw-semibold">{{ $i->barang?->nama }}</span><br><small class="text-muted">{{ $i->barang?->kategori?->nama }}</small></td>
                            <td class="text-end">{{ number_format($i->velocity_30, 2) }}</td>
                            <td class="text-end">{{ number_format($i->days_of_supply, 1) }}</td>
                            <td class="text-center"><span class="badge bg-{{ $kelasBadge[$i->kelas] ?? 'secondary' }}">{{ $i->kelas }}</span></td>
                            <td class="text-center">{{ $i->abc_class ?? '-' }}</td>
                            <td class="text-end">{{ number_format($i->forecast_7, 1) }}</td>
                            <td class="text-end">{{ $i->margin_pct !== null ? number_format($i->margin_pct, 1) . '%' : '-' }}</td>
                            <td class="text-center">@if($i->strategy)<span class="badge bg-{{ $stratBadge[$i->strategy] ?? 'secondary' }}">{{ $i->strategy }}</span>@else - @endif</td>
                            <td class="small text-muted">{{ $i->strategy_text ?? $i->rekomendasi_text }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">Belum ada insight. Klik "Generate Ulang Sekarang".</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
