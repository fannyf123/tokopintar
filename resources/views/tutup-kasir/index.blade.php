@extends('layouts.app')
@section('title', 'Tutup Kasir - TOKOPINTAR')
@section('page_title', 'Tutup Kasir Harian')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal->toDateString() }}" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-auto">
                <button class="btn btn-sm btn-secondary"><i class="fas fa-search me-1"></i> Lihat</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-cash-register text-primary me-2"></i>Rekap Penjualan {{ $tanggal->isToday() ? 'Hari Ini' : $tanggal->translatedFormat('d M Y') }}</h6>
                <table class="table table-sm table-stack">
                    <thead>
                        <tr><th>Cara Bayar</th><th class="text-center">Transaksi</th><th class="text-end">Total</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($perMetode as $metode => $row)
                            <tr>
                                <td data-label="Cara Bayar">{{ strtoupper($metode) }}</td>
                                <td data-label="Transaksi" class="text-center">{{ $row->jumlah }}</td>
                                <td data-label="Total" class="text-end fw-semibold">{{ format_rupiah($row->total) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada penjualan lunas.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-top fw-bold">
                            <td>TOTAL OMZET LUNAS</td>
                            <td class="text-center">{{ $trxLunas }}</td>
                            <td class="text-end text-primary">{{ format_rupiah($omzetLunas) }}</td>
                        </tr>
                    </tfoot>
                </table>

                @if ($hutangJumlah > 0)
                <div class="alert alert-warning small mt-2 mb-0">
                    <i class="fas fa-hand-holding-usd me-1"></i> Ada <strong>{{ $hutangJumlah }}</strong> transaksi hutang hari ini.
                    DP masuk {{ format_rupiah($hutangDp) }}, sisa hutang {{ format_rupiah($hutangSisa) }}.
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-wallet text-success me-2"></i>Uang Tunai di Laci</h6>
                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Tunai dari penjualan</span><span class="fw-semibold">{{ format_rupiah($tunaiMasuk) }}</span></div>
                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">DP hutang (tunai)</span><span class="fw-semibold">+ {{ format_rupiah($hutangDp) }}</span></div>
                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Pengeluaran hari ini</span><span class="fw-semibold text-danger">- {{ format_rupiah($pengeluaran) }}</span></div>
                <hr>
                <div class="d-flex justify-content-between fs-5 fw-bold mb-1"><span>Uang Tunai Seharusnya</span><span class="text-success">{{ format_rupiah($kasLaci) }}</span></div>
                <p class="text-muted" style="font-size:11px;">Hitung uang fisik di laci, lalu bandingkan dengan angka ini. Kalau cocok, kas seimbang.</p>

                <div class="mt-3">
                    <label class="form-label small fw-semibold">Uang Fisik di Laci (hitung manual)</label>
                    <input type="text" inputmode="numeric" id="fisik" class="form-control" placeholder="ketik jumlah uang fisik" oninput="cekSelisih()">
                    <div id="selisihBox" class="mt-2 small d-none"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const KAS_SEHARUSNYA = {{ $kasLaci }};
function cekSelisih() {
    const raw = +String(document.getElementById('fisik').value).replace(/[^\d]/g, '') || 0;
    const box = document.getElementById('selisihBox');
    const selisih = raw - KAS_SEHARUSNYA;
    box.classList.remove('d-none');
    if (selisih === 0) {
        box.className = 'mt-2 small alert alert-success py-2';
        box.innerHTML = '<i class="fas fa-check me-1"></i> Kas seimbang! Cocok.';
    } else if (selisih > 0) {
        box.className = 'mt-2 small alert alert-info py-2';
        box.innerHTML = '<i class="fas fa-arrow-up me-1"></i> Uang LEBIH ' + 'Rp ' + selisih.toLocaleString('id-ID') + ' dari seharusnya.';
    } else {
        box.className = 'mt-2 small alert alert-danger py-2';
        box.innerHTML = '<i class="fas fa-arrow-down me-1"></i> Uang KURANG ' + 'Rp ' + Math.abs(selisih).toLocaleString('id-ID') + ' dari seharusnya.';
    }
}
</script>
@endpush
@endsection
