@extends('layouts.app')
@section('title', 'Member - TOKOPINTAR')
@section('page_title', 'Member')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Daftar Member</h6>
            <a href="{{ route('pelanggan.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Tambah Member</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-stack">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Tipe</th>
                        <th class="text-end">Total Belanja</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $i => $row)
                        <tr>
                            <td data-label="#">{{ $items->firstItem() + $i }}</td>
                            <td data-label="Nama" class="fw-semibold">{{ $row->nama }}</td>
                            <td data-label="No HP">{{ $row->no_hp ?? '-' }}</td>
                            <td data-label="Tipe"><span class="badge bg-{{ $row->tipe === 'member' ? 'primary' : 'secondary' }}">{{ $row->tipe === 'member' ? 'Member' : 'Umum' }}</span></td>
                            <td data-label="Total Belanja" class="text-end">{{ format_rupiah($row->total_belanja) }}</td>
                            <td data-label="Aksi">
                                <a href="{{ route('pelanggan.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('pelanggan.destroy', $row) }}" class="d-inline" onsubmit="return confirm('Hapus pelanggan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pelanggan tercatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
