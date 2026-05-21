@extends('layouts.app')
@section('title', $pengeluaran->exists ? 'Edit Biaya - TOKOPINTAR' : 'Catat Biaya Baru - TOKOPINTAR')
@section('page_title', $pengeluaran->exists ? 'Ubah Biaya' : 'Catat Biaya Baru')
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
                <label class="form-label fw-semibold">Jenis Biaya</label>
                <select name="kategori" required class="form-select">
                    @php $kat = ['sewa' => 'Sewa Tempat', 'listrik' => 'Listrik & Air', 'gaji' => 'Gaji Karyawan', 'lainnya' => 'Lainnya']; @endphp
                    @foreach ($kat as $k => $l)
                        <option value="{{ $k }}" @selected(old('kategori', $pengeluaran->kategori) === $k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tanggal</label>
                <input type="date" name="tanggal" required value="{{ old('tanggal', optional($pengeluaran->tanggal)->toDateString() ?? now()->toDateString()) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Jumlah (Rp)</label>
                <input type="number" min="0" name="jumlah" value="{{ old('jumlah', $pengeluaran->jumlah) }}" required class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Keterangan</label>
                <textarea name="catatan" rows="2" class="form-control" placeholder="Misal: bayar listrik bulan Mei">{{ old('catatan', $pengeluaran->catatan) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Foto Bukti <small class="text-muted">(opsional, boleh dikosongkan)</small></label>
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
