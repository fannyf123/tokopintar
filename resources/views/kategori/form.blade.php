@extends('layouts.app')
@section('title', $kategori->exists ? 'Edit Kategori - TOKOPINTAR' : 'Kategori Baru - TOKOPINTAR')
@section('page_title', $kategori->exists ? 'Ubah Kategori' : 'Tambah Kategori Baru')
@section('content')
<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger small">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        <form method="POST" action="{{ $kategori->exists ? route('kategori.update', $kategori) : route('kategori.store') }}" class="row g-3">
            @csrf
            @if ($kategori->exists) @method('PUT') @endif
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Nama</label>
                <input name="nama" value="{{ old('nama', $kategori->nama) }}" required class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Deskripsi</label>
                <input name="deskripsi" value="{{ old('deskripsi', $kategori->deskripsi) }}" class="form-control">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Ikon (opsional)</label>
                <input name="ikon" value="{{ old('ikon', $kategori->ikon) }}" class="form-control">
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('kategori.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
