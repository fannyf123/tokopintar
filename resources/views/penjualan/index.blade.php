@extends('layouts.app')
@section('title', 'Riwayat Jualan - TOKOPINTAR')
@section('page_title', 'Riwayat Jualan')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Daftar Transaksi Jualan</h6>
            <a href="{{ route('pos.index') }}" class="btn btn-sm btn-primary"><i class="fas fa-cash-register me-1"></i> Buka Kasir</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-stack">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Kasir</th>
                        <th>Pelanggan</th>
                        <th class="text-end">Grand Total</th>
                        <th class="text-center">Status</th>
                        <th style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $i => $p)
                        <tr>
                            <td data-label="#">{{ $items->firstItem() + $i }}</td>
                            <td data-label="Nomor"><code>{{ $p->nomor }}</code></td>
                            <td data-label="Tanggal">{{ format_tanggal_id($p->tanggal, true) }}</td>
                            <td data-label="Kasir">{{ $p->kasir?->name }}</td>
                            <td data-label="Pelanggan">{{ $p->pelanggan?->nama ?? '-' }}</td>
                            <td data-label="Grand Total" class="text-end fw-semibold">{{ format_rupiah($p->grand_total) }}</td>
                            <td data-label="Status" class="text-center">
                                @if ($p->status === 'hutang')
                                    <span class="badge bg-danger">HUTANG</span>
                                    <div class="small text-danger">sisa {{ format_rupiah($p->grand_total - $p->dibayar) }}</div>
                                @elseif ($p->status === 'batal')
                                    <span class="badge bg-secondary">BATAL</span>
                                @else
                                    <span class="badge bg-success">LUNAS</span>
                                @endif
                            </td>
                            <td data-label="Aksi"><a href="{{ route('penjualan.show', $p) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
