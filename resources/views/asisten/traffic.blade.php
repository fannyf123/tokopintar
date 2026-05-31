@extends('layouts.app')
@section('title', 'Jam & Hari Ramai - TOKOPINTAR')
@section('page_title', 'Analisa Jam & Hari Ramai')
@section('content')
@php $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.'); @endphp

<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-1"><i class="fas fa-clock text-primary me-2"></i>Kapan Toko Paling Ramai</h6>
        <p class="text-muted small mb-0">Berdasarkan data penjualan 60 hari terakhir. Berguna untuk atur jam buka, stok, dan promo.</p>
    </div>
</div>

@if (!$adaData)
    <div class="card"><div class="card-body text-center text-muted py-5"><i class="fas fa-chart-bar fs-1 d-block mb-2 opacity-50"></i>Belum ada cukup data penjualan untuk dianalisa.</div></div>
@else

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card h-100" style="background:linear-gradient(135deg,#4361ee,#3f37c9);color:#fff;border:none;">
            <div class="card-body">
                <div class="stat-label opacity-75"><i class="fas fa-clock me-1"></i> Jam Paling Ramai</div>
                <div class="fs-3 fw-bold">{{ sprintf('%02d:00 - %02d:00', $jamTeramai, $jamTeramai + 1) }}</div>
                <small class="opacity-75">Pastikan stok & tenaga siap di jam ini.</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100" style="background:linear-gradient(135deg,#16a34a,#059669);color:#fff;border:none;">
            <div class="card-body">
                <div class="stat-label opacity-75"><i class="fas fa-calendar-day me-1"></i> Hari Paling Ramai</div>
                <div class="fs-3 fw-bold">{{ $hariTeramai['nama'] }}</div>
                <small class="opacity-75">Omzet {{ $rp($hariTeramai['omzet']) }} dalam 60 hari.</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Penjualan per Jam</h6>
        <canvas id="chartJam" height="90"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Penjualan per Hari</h6>
        <canvas id="chartHari" height="70"></canvas>
    </div>
</div>

@push('scripts')
<script>
const PER_JAM = @json(array_values($perJam));
const PER_HARI = @json($perHari);
window.addEventListener('load', () => {
    if (!window.Chart) return;
    const jamLabels = PER_JAM.map((_, i) => String(i).padStart(2,'0') + ':00');
    new Chart(document.getElementById('chartJam'), {
        type: 'bar',
        data: { labels: jamLabels, datasets: [{ label: 'Omzet', data: PER_JAM.map(x => x.omzet), backgroundColor: '#4361ee' }] },
        options: { plugins: { legend: { display:false } }, scales: { y: { beginAtZero:true } } }
    });
    new Chart(document.getElementById('chartHari'), {
        type: 'bar',
        data: { labels: PER_HARI.map(x => x.nama), datasets: [{ label: 'Omzet', data: PER_HARI.map(x => x.omzet), backgroundColor: '#16a34a' }] },
        options: { plugins: { legend: { display:false } }, scales: { y: { beginAtZero:true } } }
    });
});
</script>
@endpush
@endif
@endsection
