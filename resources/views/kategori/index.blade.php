@extends('layouts.app')
@section('title', 'Kategori Barang - TOKOPINTAR')
@section('page_title', 'Kategori Barang')
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Daftar Kategori</h6>
            <a href="{{ route('kategori.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Tambah Kategori</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $i => $row)
                        <tr>
                            <td>{{ $items->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $row->nama }}</td>
                            <td class="text-muted">{{ $row->deskripsi }}</td>
                            <td>
                                <a href="{{ route('kategori.edit', $row) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('kategori.destroy', $row) }}" class="d-inline" onsubmit="return confirm('Hapus kategori?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
