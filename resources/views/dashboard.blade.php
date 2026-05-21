@extends('layouts.app')
@section('title', 'Dashboard - TOKOPINTAR')
@section('page_title', 'Dashboard')
@section('content')
<div class="card mb-3" style="background:linear-gradient(135deg,#4361ee,#3f37c9); color:#fff; border:none;">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h5 class="fw-bold mb-1">👋 Halo, {{ auth()->user()->name }}!</h5>
                <p class="mb-0 opacity-75 small">Mau melakukan apa hari ini?</p>
            </div>
            <div class="col-md-5">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end mt-3 mt-md-0">
                    @if (auth()->user()->isAdmin() || auth()->user()->isKasir())
                        <a href="{{ route('pos.index') }}" class="btn btn-light btn-sm fw-semibold"><i class="fas fa-cash-register me-1"></i> Mulai Jualan</a>
                    @endif
                    @if (auth()->user()->isAdmin() || auth()->user()->isGudang())
                        <a href="{{ route('barang.create') }}" class="btn btn-light btn-sm fw-semibold"><i class="fas fa-box me-1"></i> Tambah Barang</a>
                    @endif
                    <a href="{{ route('panduan.index') }}" class="btn btn-outline-light btn-sm fw-semibold"><i class="fas fa-book-open me-1"></i> Panduan</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md">
        <div class="card stat-card" style="background:linear-gradient(135deg,#4361ee,#3f37c9);">
            <div class="card-body">
                <div class="stat-label"><i class="fas fa-cash-register me-1"></i> Penjualan Hari Ini</div>
                <div class="stat-value">{{ format_rupiah($omzetToday) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card stat-card" style="background:linear-gradient(135deg,#16a34a,#059669);">
            <div class="card-body">
                <div class="stat-label"><i class="fas fa-coins me-1"></i> Untung Hari Ini</div>
                <div class="stat-value">{{ format_rupiah($omzetToday - $hppToday) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card stat-card" style="background:linear-gradient(135deg,#0891b2,#0e7490);">
            <div class="card-body">
                <div class="stat-label"><i class="fas fa-receipt me-1"></i> Jumlah Transaksi</div>
                <div class="stat-value">{{ $trxToday }}</div>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card stat-card" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
            <div class="card-body">
                <div class="stat-label"><i class="fas fa-exclamation-triangle me-1"></i> Stok Menipis</div>
                <div class="stat-value">{{ $stokRendah }}</div>
            </div>
        </div>
    </div>
    <div class="col-md">
        <div class="card stat-card" style="background:linear-gradient(135deg,#ea580c,#c2410c);">
            <div class="card-body">
                <div class="stat-label"><i class="fas fa-clock me-1"></i> Hampir Kadaluarsa</div>
                <div class="stat-value">{{ $nearExpiry }}</div>
            </div>
        </div>
    </div>
</div>

@if (auth()->user()->isAdmin() && (($criticalAnomalies ?? 0) > 0 || ($churnRiskCount ?? 0) > 0))
<div class="row g-3 mb-3">
    @if (($criticalAnomalies ?? 0) > 0)
    <div class="col-md-6">
        <a href="{{ route('anomaly.index') }}?severity=critical" class="text-decoration-none">
            <div class="card border-danger" style="border-width:2px;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> Alert Kritis</small>
                        <div class="fs-4 fw-bold text-danger">{{ $criticalAnomalies }} masalah perlu cek</div>
                        <small class="text-muted">Klik untuk lihat detail anomali</small>
                    </div>
                    <i class="fas fa-shield-alt fs-1 text-danger opacity-25"></i>
                </div>
            </div>
        </a>
    </div>
    @endif
    @if (($churnRiskCount ?? 0) > 0)
    <div class="col-md-6">
        <a href="{{ route('customer-insight.index') }}?churn=1" class="text-decoration-none">
            <div class="card border-warning" style="border-width:2px;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-uppercase text-warning fw-bold"><i class="fas fa-user-clock me-1"></i> Pelanggan Pergi</small>
                        <div class="fs-4 fw-bold text-warning">{{ $churnRiskCount }} pelanggan beresiko hilang</div>
                        <small class="text-muted">Klik untuk lihat dan hubungi mereka</small>
                    </div>
                    <i class="fas fa-users fs-1 text-warning opacity-25"></i>
                </div>
            </div>
        </a>
    </div>
    @endif
</div>
@endif

<div class="row g-3 mb-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Penjualan 30 Hari Terakhir</h6>
                <canvas id="chartOmzet" height="80"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">5 Barang Paling Laku</h6>
                <canvas id="chartTop" height="160"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-history text-primary me-2"></i>Transaksi Terbaru</h6>
                <table class="table table-sm mb-0">
                    @forelse ($lastTrx as $t)
                        <tr><td><code class="small">{{ $t->nomor }}</code></td><td class="text-end fw-semibold">{{ format_rupiah($t->grand_total) }}</td></tr>
                    @empty <tr><td class="text-muted">Belum ada transaksi.</td></tr> @endforelse
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-rocket text-success me-2"></i>Barang Cepat Laku</h6>
                <table class="table table-sm mb-0">
                    @forelse ($fastMovers as $i)
                        <tr><td>{{ $i->barang?->nama }}</td><td class="text-end small text-muted">{{ number_format($i->velocity_30, 2) }}/hari</td></tr>
                    @empty <tr><td class="text-muted">Belum ada data - perlu beberapa transaksi dulu.</td></tr> @endforelse
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-ban text-danger me-2"></i>Barang Tidak Laku</h6>
                <table class="table table-sm mb-0">
                    @forelse ($deadStocks as $i)
                        <tr><td>{{ $i->barang?->nama }}</td><td class="text-end small text-muted">{{ number_format($i->days_of_supply, 0) }} hari</td></tr>
                    @empty <tr><td class="text-muted">Belum ada data.</td></tr> @endforelse
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const OMZET = @json($omzetSeries);
const TOP = @json($topBarang->map(fn($t) => ['nama' => $t->barang?->nama, 'qty' => (int) $t->total_qty]));
window.addEventListener('load', () => {
    if (!window.Chart) return;
    const c1 = document.getElementById('chartOmzet');
    if (c1) new Chart(c1, { type: 'line',
        data: { labels: OMZET.labels, datasets: [{ label: 'Omzet', data: OMZET.data, borderColor: '#4361ee', backgroundColor: 'rgba(67,97,238,.1)', tension: 0.3, fill: true }] },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
    const c2 = document.getElementById('chartTop');
    if (c2) new Chart(c2, { type: 'bar',
        data: { labels: TOP.map(t => t.nama), datasets: [{ data: TOP.map(t => t.qty), backgroundColor: '#4361ee' }] },
        options: { plugins: { legend: { display: false } }, indexAxis: 'y' }
    });
});
</script>
@endpush
