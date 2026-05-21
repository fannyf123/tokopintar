@extends('layouts.app')
@section('title', 'Pengeluaran - TOKOPINTAR')
@section('page_title', 'Pengeluaran')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Daftar Pengeluaran</h6>
            <a href="{{ route('pengeluaran.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Tambah Pengeluaran</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th class="text-end">Jumlah</th>
                        <th>Catatan</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $i => $p)
                        <tr>
                            <td>{{ $items->firstItem() + $i }}</td>
                            <td>{{ format_tanggal_id($p->tanggal) }}</td>
                            <td><span class="badge bg-light text-dark">{{ ucfirst($p->kategori) }}</span></td>
                            <td class="text-end fw-semibold">{{ format_rupiah($p->jumlah) }}</td>
                            <td class="text-muted small">{{ $p->catatan }}</td>
                            <td>
                                <a href="{{ route('pengeluaran.edit', $p) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('pengeluaran.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
