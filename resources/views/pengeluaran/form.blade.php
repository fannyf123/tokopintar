@extends('layouts.app')
@section('title', $pengeluaran->exists ? 'Edit Pengeluaran - TOKOPINTAR' : 'Pengeluaran Baru - TOKOPINTAR')
@section('page_title', $pengeluaran->exists ? 'Edit Pengeluaran' : 'Pengeluaran Baru')
@section('content')
<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger small">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        <form method="POST" enctype="multipart/form-data"
              action="{{ $pengeluaran->exists ? route('pengeluaran.update', $pengeluaran) : route('pengeluaran.store') }}"
              class="row g-3">
            @csrf
            @if ($pengeluaran->exists) @method('PUT') @endif
            <div class="col-md-4">
                <label class="form-label fw-semibold">Kategori</label>
                <select name="kategori" required class="form-select">
                    @foreach (['sewa', 'listrik', 'gaji', 'lainnya'] as $k)
                        <option value="{{ $k }}" @selected(old('kategori', $pengeluaran->kategori) === $k)>{{ ucfirst($k) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tanggal</label>
                <input type="date" name="tanggal" required value="{{ old('tanggal', optional($pengeluaran->tanggal)->toDateString() ?? now()->toDateString()) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Jumlah</label>
                <input type="number" min="0" name="jumlah" value="{{ old('jumlah', $pengeluaran->jumlah) }}" required class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Catatan</label>
                <textarea name="catatan" rows="2" class="form-control">{{ old('catatan', $pengeluaran->catatan) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bukti <small class="text-muted">(gambar, opsional)</small></label>
                <input type="file" name="bukti" accept="image/*" class="form-control">
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('pengeluaran.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
