@extends('layouts.app')
@section('title', 'Penyesuaian Stok - TOKOPINTAR')
@section('page_title', 'Penyesuaian Stok')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold mb-1">Jenis</label>
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach ($jenisList as $j)<option value="{{ $j }}" @selected(request('jenis') === $j)>{{ $j }}</option>@endforeach
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
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Riwayat Mutasi</h6>
            <a href="{{ route('mutasi.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Mutasi Baru</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-stack">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Jenis</th>
                        <th class="text-end">Qty</th>
                        <th>Alasan</th>
                        <th>Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $m)
                        <tr>
                            <td data-label="Tanggal" class="small">{{ $m->created_at->format('Y-m-d H:i') }}</td>
                            <td data-label="Barang" class="fw-semibold">{{ $m->barang?->nama }}</td>
                            <td data-label="Jenis"><span class="badge bg-light text-dark">{{ $m->jenis }}</span></td>
                            <td data-label="Qty" class="text-end fw-bold {{ $m->qty_signed < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $m->qty_signed > 0 ? '+' : '' }}{{ $m->qty_signed }}
                            </td>
                            <td data-label="Alasan" class="text-muted small">{{ $m->alasan }}</td>
                            <td data-label="Oleh" class="small">{{ $m->user?->name }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada mutasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
