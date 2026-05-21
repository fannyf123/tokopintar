@extends('layouts.app')
@section('title', $pelanggan->exists ? 'Edit Pelanggan - TOKOPINTAR' : 'Pelanggan Baru - TOKOPINTAR')
@section('page_title', $pelanggan->exists ? 'Edit Pelanggan' : 'Pelanggan Baru')
@section('content')
<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger small">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        <form method="POST" action="{{ $pelanggan->exists ? route('pelanggan.update', $pelanggan) : route('pelanggan.store') }}" class="row g-3">
            @csrf
            @if ($pelanggan->exists) @method('PUT') @endif
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Nama</label>
                <input name="nama" value="{{ old('nama', $pelanggan->nama) }}" required class="form-control">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">No HP</label>
                <input name="no_hp" value="{{ old('no_hp', $pelanggan->no_hp) }}" class="form-control">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Tipe</label>
                <select name="tipe" class="form-select">
                    @foreach (['umum' => 'Umum', 'member' => 'Member'] as $v => $l)
                        <option value="{{ $v }}" @selected(old('tipe', $pelanggan->tipe ?? 'umum') === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Alamat</label>
                <input name="alamat" value="{{ old('alamat', $pelanggan->alamat) }}" class="form-control">
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('pelanggan.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
