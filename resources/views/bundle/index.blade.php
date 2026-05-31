@extends('layouts.app')
@section('title', 'Bundle Generator - TOKOPINTAR')
@section('page_title', 'Bundle Generator')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="fas fa-magic-wand-sparkles text-primary me-2"></i>Saran Bundle Otomatis</h6>
        <p class="text-muted small">Berdasar Aturan Asosiasi (lift &ge; 1.5). Hanya pasangan paling sering dibeli bareng.</p>
        @if (count($suggestions) > 0)
            <div class="table-responsive">
                <table class="table table-striped table-stack">
                    <thead>
                        <tr>
                            <th>Pasangan Barang</th>
                            <th class="text-end">Harga Normal</th>
                            <th class="text-end">Saran Bundle (-10%)</th>
                            <th class="text-end">Hemat</th>
                            <th class="text-end">Margin</th>
                            <th class="text-end">Lift</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suggestions as $s)
                            <tr class="{{ $s['sudah_ada'] ? 'opacity-50' : '' }}">
                                <td data-label="Pasangan Barang">
                                    <strong>{{ $s['a']->nama }}</strong> + <strong>{{ $s['b']->nama }}</strong>
                                </td>
                                <td data-label="Harga Normal" class="text-end">{{ format_rupiah($s['harga_normal']) }}</td>
                                <td data-label="Saran Bundle" class="text-end fw-bold">{{ format_rupiah($s['harga_bundle_saran']) }}</td>
                                <td data-label="Hemat" class="text-end text-success">-{{ format_rupiah($s['saving']) }}</td>
                                <td data-label="Margin" class="text-end {{ $s['margin_pct'] < 10 ? 'text-warning' : 'text-success' }}">{{ $s['margin_pct'] }}%</td>
                                <td data-label="Lift" class="text-end"><span class="badge bg-info">{{ $s['lift'] }}</span></td>
                                <td data-label="Aksi">
                                    @if ($s['sudah_ada'])
                                        <span class="badge bg-secondary">Sudah dibuat</span>
                                    @else
                                        <form method="POST" action="{{ route('bundle.store') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="nama" value="Bundle {{ Str::limit($s['a']->nama, 12) }} + {{ Str::limit($s['b']->nama, 12) }}">
                                            <input type="hidden" name="barang_a_id" value="{{ $s['a']->id }}">
                                            <input type="hidden" name="barang_b_id" value="{{ $s['b']->id }}">
                                            <input type="hidden" name="harga_bundle" value="{{ $s['harga_bundle_saran'] }}">
                                            <button class="btn btn-sm btn-success" title="Bikin Bundle"><i class="fas fa-check me-1"></i>Buat</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center py-4">Belum ada saran bundle. Pastikan sudah hitung Aturan Asosiasi (butuh data &ge;20 transaksi).</p>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Bundle Aktif</h6>
        @if ($items->count() > 0)
            <div class="table-responsive">
                <table class="table table-stack">
                    <thead>
                        <tr>
                            <th>Nama Bundle</th>
                            <th>Isi</th>
                            <th class="text-end">Harga Normal</th>
                            <th class="text-end">Harga Bundle</th>
                            <th class="text-end">Hemat</th>
                            <th class="text-end">Margin</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $b)
                            <tr>
                                <td data-label="Nama Bundle"><strong>{{ $b->nama }}</strong></td>
                                <td data-label="Isi" class="small">{{ $b->barangA?->nama }} + {{ $b->barangB?->nama }}</td>
                                <td data-label="Harga Normal" class="text-end">{{ format_rupiah($b->harga_normal) }}</td>
                                <td data-label="Harga Bundle" class="text-end fw-bold">{{ format_rupiah($b->harga_bundle) }}</td>
                                <td data-label="Hemat" class="text-end text-success">-{{ format_rupiah($b->saving) }}</td>
                                <td data-label="Margin" class="text-end">{{ $b->total_margin_pct }}%</td>
                                <td data-label="Aksi">
                                    <form method="POST" action="{{ route('bundle.destroy', $b) }}" class="d-inline" onsubmit="return confirm('Hapus bundle?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center py-4">Belum ada bundle. Buat dari saran di atas.</p>
        @endif
    </div>
</div>
@endsection
