@extends('layouts.app')
@section('title', $pengeluaran->exists ? 'Edit Pengeluaran' : 'Pengeluaran Baru')
@section('breadcrumb', 'Admin / Pengeluaran / Form')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-xl">
    <h1 class="text-xl font-bold mb-4">{{ $pengeluaran->exists ? 'Edit Pengeluaran' : 'Pengeluaran Baru' }}</h1>
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" enctype="multipart/form-data"
          action="{{ $pengeluaran->exists ? route('pengeluaran.update', $pengeluaran) : route('pengeluaran.store') }}"
          class="space-y-4">
        @csrf
        @if ($pengeluaran->exists) @method('PUT') @endif
        <div>
            <label class="block text-sm mb-1">Kategori</label>
            <select name="kategori" required class="w-full border rounded px-3 py-2">
                @foreach (['sewa', 'listrik', 'gaji', 'lainnya'] as $k)
                    <option value="{{ $k }}" @selected(old('kategori', $pengeluaran->kategori) === $k)>{{ ucfirst($k) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Tanggal</label>
            <input type="date" name="tanggal" required value="{{ old('tanggal', optional($pengeluaran->tanggal)->toDateString() ?? now()->toDateString()) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Jumlah</label>
            <input type="number" min="0" name="jumlah" value="{{ old('jumlah', $pengeluaran->jumlah) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Catatan</label>
            <textarea name="catatan" rows="2" class="w-full border rounded px-3 py-2">{{ old('catatan', $pengeluaran->catatan) }}</textarea>
        </div>
        <div>
            <label class="block text-sm mb-1">Bukti (gambar, opsional)</label>
            <input type="file" name="bukti" accept="image/*" class="w-full">
        </div>
        <div class="flex gap-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('pengeluaran.index') }}" class="px-4 py-2 border rounded">Batal</a>
        </div>
    </form>
</div>
@endsection
