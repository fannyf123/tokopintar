@extends('layouts.app')
@section('title', 'Daftar Barang - TOKOPINTAR')
@section('page_title', 'Daftar Barang')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md">
                <label class="form-label small fw-semibold mb-1">Cari</label>
                <input name="q" value="{{ request('q') }}" placeholder="Nama / kode / barcode" class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold mb-1">Kategori</label>
                <select name="kategori_id" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    @foreach ($kategoris as $k)<option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-12 col-md-auto">
                <button class="btn btn-sm btn-secondary"><i class="fas fa-search me-1"></i> Cari</button>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Semua Barang di Toko</h6>
            <a href="{{ route('barang.create') }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i> Tambah Barang Baru</a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-stack">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th class="text-end">H. Beli</th>
                        <th class="text-end">H. Jual</th>
                        <th class="text-end">Stok</th>
                        <th class="text-center">Status</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $i => $b)
                        <tr>
                            <td data-label="#">{{ $items->firstItem() + $i }}</td>
                            <td data-label="Kode"><code>{{ $b->kode }}</code></td>
                            <td data-label="Nama" class="fw-semibold">{{ $b->nama }}</td>
                            <td data-label="Kategori">{{ $b->kategori?->nama }}</td>
                            <td data-label="H. Beli" class="text-end">{{ format_rupiah($b->harga_beli) }}</td>
                            <td data-label="H. Jual" class="text-end">{{ format_rupiah($b->harga_jual) }}</td>
                            <td data-label="Stok" class="text-end {{ $b->stok_current <= $b->stok_min ? 'text-danger fw-bold' : '' }}">
                                {{ $b->stok_current }} {{ $b->satuan }}
                            </td>
                            <td data-label="Status" class="text-center">
                                @if ($b->aktif)<span class="badge bg-success">aktif</span>@else<span class="badge bg-secondary">tidak</span>@endif
                            </td>
                            <td data-label="Aksi">
                                <a href="{{ route('barang.edit', $b) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('barang.destroy', $b) }}" class="d-inline" onsubmit="return confirm('Hapus / nonaktifkan?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">Belum ada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
