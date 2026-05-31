@extends('layouts.app')
@section('title', 'Harga Kompetitor - TOKOPINTAR')
@section('page_title', 'Monitor Harga Kompetitor')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="fw-bold mb-0">Bandingkan Harga dengan Toko Sebelah</h6>
    <a href="{{ route('competitor.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Catat Harga Kompetitor</a>
</div>

@if ($gaps->count() > 0)
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="fas fa-balance-scale text-warning me-2"></i>Gap Harga (Saya vs Kompetitor)</h6>
        <div class="table-responsive">
            <table class="table table-stack">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th class="text-end">Harga Saya</th>
                        <th class="text-end">Harga Kompetitor</th>
                        <th class="text-end">Selisih</th>
                        <th>Saran</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($gaps as $g)
                        <tr>
                            <td data-label="Barang"><strong>{{ $g['barang']->nama }}</strong></td>
                            <td data-label="Harga Saya" class="text-end">{{ format_rupiah($g['barang']->harga_jual) }}</td>
                            <td data-label="Harga Kompetitor" class="text-end">{{ format_rupiah($g['competitor']->harga_competitor) }}<br><small class="text-muted">{{ $g['competitor']->competitor_name }}</small></td>
                            <td data-label="Selisih" class="text-end {{ $g['delta'] > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                {{ $g['delta'] > 0 ? '+' : '' }}{{ $g['delta_pct'] }}%
                            </td>
                            <td data-label="Saran" class="small">
                                @if ($g['delta_pct'] > 10)
                                    <span class="text-danger">Saya {{ $g['delta_pct'] }}% lebih mahal. Pertimbangkan turunkan harga.</span>
                                @elseif ($g['delta_pct'] < -10)
                                    <span class="text-success">Saya {{ abs($g['delta_pct']) }}% lebih murah. Bisa naikkan harga sedikit untuk profit lebih.</span>
                                @else
                                    <span class="text-muted">Selisih kecil, harga kompetitif.</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Riwayat Catatan Harga Kompetitor</h6>
        <div class="table-responsive">
            <table class="table table-striped table-stack">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Kompetitor</th>
                        <th class="text-end">Harga</th>
                        <th>Catatan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $i)
                        <tr>
                            <td data-label="Tanggal">{{ $i->tanggal_observasi->format('d M Y') }}</td>
                            <td data-label="Barang">{{ $i->barang?->nama }}</td>
                            <td data-label="Kompetitor">{{ $i->competitor_name }}</td>
                            <td data-label="Harga" class="text-end">{{ format_rupiah($i->harga_competitor) }}</td>
                            <td data-label="Catatan" class="small text-muted">{{ $i->catatan ?? '-' }}</td>
                            <td data-label="Aksi">
                                <form method="POST" action="{{ route('competitor.destroy', $i) }}" class="d-inline" onsubmit="return confirm('Hapus catatan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data. Klik "Catat Harga Kompetitor" untuk mulai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
