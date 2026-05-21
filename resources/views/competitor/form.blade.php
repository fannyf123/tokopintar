@extends('layouts.app')
@section('title', 'Catat Harga Kompetitor - TOKOPINTAR')
@section('page_title', 'Catat Harga Kompetitor')
@section('content')
<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger small">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        <form method="POST" action="{{ route('competitor.store') }}" class="row g-3">
            @csrf
            <div class="col-md-6">
                <label class="form-label fw-semibold">Barang</label>
                <select name="barang_id" required class="form-select">
                    <option value="">— pilih barang —</option>
                    @foreach ($barangs as $b)<option value="{{ $b->id }}" @selected(old('barang_id') == $b->id)>{{ $b->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nama Kompetitor</label>
                <input name="competitor_name" value="{{ old('competitor_name') }}" required class="form-control" placeholder="Misal: Toko Pak Budi, Indomaret">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Harga di Kompetitor (Rp)</label>
                <input type="number" name="harga_competitor" value="{{ old('harga_competitor') }}" min="0" required class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tanggal Observasi</label>
                <input type="date" name="tanggal_observasi" value="{{ old('tanggal_observasi', now()->toDateString()) }}" required class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Catatan (opsional)</label>
                <input name="catatan" value="{{ old('catatan') }}" class="form-control" placeholder="Misal: Kemasan sama persis, atau ada promo akhir bulan">
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('competitor.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
