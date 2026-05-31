@extends('layouts.app')
@section('title', 'Pemasok - TOKOPINTAR')
@section('page_title', 'Pemasok / Supplier')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Daftar Pemasok Barang</h6>
            <a href="{{ route('supplier.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Tambah Pemasok</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-stack">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>No HP</th>
                        <th>Email</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $i => $row)
                        <tr>
                            <td data-label="#">{{ $items->firstItem() + $i }}</td>
                            <td data-label="Nama" class="fw-semibold">{{ $row->nama }}</td>
                            <td data-label="Kontak">{{ $row->kontak }}</td>
                            <td data-label="No HP">{{ $row->no_hp }}</td>
                            <td data-label="Email">{{ $row->email }}</td>
                            <td data-label="Aksi">
                                <a href="{{ route('supplier.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('supplier.destroy', $row) }}" class="d-inline" onsubmit="return confirm('Hapus supplier?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
