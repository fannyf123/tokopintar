@extends('layouts.app')
@section('title', 'Barang Masuk - TOKOPINTAR')
@section('page_title', 'Barang Masuk dari Pemasok')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Daftar Barang Masuk</h6>
            <a href="{{ route('pembelian.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Catat Barang Masuk</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Nomor</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Status</th>
                        <th style="width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $badge = ['draft' => 'secondary', 'diterima' => 'success', 'batal' => 'danger']; @endphp
                    @forelse ($items as $i => $p)
                        <tr>
                            <td>{{ $items->firstItem() + $i }}</td>
                            <td><code>{{ $p->nomor }}</code></td>
                            <td>{{ format_tanggal_id($p->tanggal) }}</td>
                            <td>{{ $p->supplier?->nama }}</td>
                            <td class="text-end">{{ format_rupiah($p->total) }}</td>
                            <td class="text-center"><span class="badge bg-{{ $badge[$p->status] ?? 'secondary' }}">{{ $p->status }}</span></td>
                            <td><a href="{{ route('pembelian.show', $p) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
