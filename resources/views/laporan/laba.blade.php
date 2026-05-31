@extends('layouts.app')
@section('title', 'Laporan Untung - TOKOPINTAR')
@section('page_title', 'Laporan Untung Toko')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Mulai</label>
                <input type="date" name="start" value="{{ $start->toDateString() }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Sampai</label>
                <input type="date" name="end" value="{{ $end->toDateString() }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Granularity</label>
                <select name="g" class="form-select form-select-sm">
                    @foreach (['daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'yearly' => 'Tahunan'] as $v => $l)
                        <option value="{{ $v }}" @selected($g === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="fas fa-check me-1"></i> Terapkan</button>
            </div>
        </form>
        <div class="d-flex flex-wrap gap-1 mt-3">
            @foreach (['today' => 'Hari Ini', 'yesterday' => 'Kemarin', '7d' => '7 Hari', '30d' => '30 Hari', 'this_month' => 'Bulan Ini', 'this_year' => 'Tahun Ini'] as $p => $l)
                <a href="?preset={{ $p }}&g={{ $g }}" class="btn btn-sm btn-outline-secondary">{{ $l }}</a>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    @php $cards = [
        ['Omzet', $data['totals']['omzet'], 'primary', 'fa-cash-register'],
        ['HPP', $data['totals']['hpp'], 'secondary', 'fa-warehouse'],
        ['Laba Kotor', $data['totals']['laba_kotor'], 'success', 'fa-coins'],
        ['Biaya', $data['totals']['biaya'], 'danger', 'fa-money-bill-wave'],
        ['Laba Bersih', $data['totals']['laba_bersih'], 'info', 'fa-chart-line'],
    ]; @endphp
    @foreach ($cards as [$lbl, $val, $color, $icon])
        <div class="col-6 col-md">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted text-uppercase small">{{ $lbl }}</div>
                            <div class="fs-5 fw-bold text-{{ $color }}">{{ format_rupiah($val) }}</div>
                        </div>
                        <div class="text-{{ $color }}"><i class="fas {{ $icon }} fs-4 opacity-50"></i></div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Tren Omzet vs Laba Bersih</h6>
        <canvas id="chartLaba" height="80"></canvas>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Rincian Per Periode</h6>
            <div class="d-flex gap-2">
                <a href="{{ route('laporan.laba.pdf', request()->all()) }}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf me-1"></i> PDF</a>
                <a href="{{ route('laporan.laba.csv', request()->all()) }}" class="btn btn-sm btn-success"><i class="fas fa-file-csv me-1"></i> CSV</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-stack">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th class="text-end">Omzet</th>
                        <th class="text-end">HPP</th>
                        <th class="text-end">Laba Kotor</th>
                        <th class="text-end">Biaya</th>
                        <th class="text-end">Laba Bersih</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['rows'] as $r)
                        <tr>
                            <td data-label="Periode"><code>{{ $r['bucket'] }}</code></td>
                            <td data-label="Omzet" class="text-end">{{ format_rupiah($r['omzet']) }}</td>
                            <td data-label="HPP" class="text-end">{{ format_rupiah($r['hpp']) }}</td>
                            <td data-label="Laba Kotor" class="text-end">{{ format_rupiah($r['laba_kotor']) }}</td>
                            <td data-label="Biaya" class="text-end">{{ format_rupiah($r['biaya']) }}</td>
                            <td data-label="Laba Bersih" class="text-end fw-bold {{ $r['laba_bersih'] >= 0 ? 'text-success' : 'text-danger' }}">{{ format_rupiah($r['laba_bersih']) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data di rentang ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const ROWS = @json($data['rows']);
window.addEventListener('load', () => {
    if (!window.Chart) return;
    const ctx = document.getElementById('chartLaba');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ROWS.map(r => r.bucket),
            datasets: [
                {label: 'Omzet', data: ROWS.map(r => r.omzet), borderColor: '#4361ee', backgroundColor:'rgba(67,97,238,.1)', tension: 0.3, fill:true},
                {label: 'Laba Bersih', data: ROWS.map(r => r.laba_bersih), borderColor: '#16a34a', tension: 0.3},
            ],
        },
        options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
    });
});
</script>
@endpush
