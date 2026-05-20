@extends('layouts.app')
@section('title', $barang->exists ? 'Edit Barang' : 'Barang Baru')
@section('breadcrumb', 'Master / Barang / Form')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-3xl">
    <h1 class="text-xl font-bold mb-4">{{ $barang->exists ? 'Edit Barang' : 'Barang Baru' }}</h1>
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" action="{{ $barang->exists ? route('barang.update', $barang) : route('barang.store') }}" class="grid grid-cols-2 gap-4">
        @csrf
        @if ($barang->exists) @method('PUT') @endif
        <div>
            <label class="block text-sm mb-1">Kode (kosongkan untuk auto)</label>
            <input name="kode" value="{{ old('kode', $barang->kode) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Barcode</label>
            <input name="barcode" value="{{ old('barcode', $barang->barcode) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="col-span-2">
            <label class="block text-sm mb-1">Nama</label>
            <input name="nama" value="{{ old('nama', $barang->nama) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Kategori</label>
            <select name="kategori_id" required class="w-full border rounded px-3 py-2">
                <option value="">— pilih —</option>
                @foreach ($kategoris as $k)<option value="{{ $k->id }}" @selected(old('kategori_id', $barang->kategori_id) == $k->id)>{{ $k->nama }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Supplier (default)</label>
            <select name="supplier_id" class="w-full border rounded px-3 py-2">
                <option value="">— tidak ada —</option>
                @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id', $barang->supplier_id) == $s->id)>{{ $s->nama }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Satuan</label>
            <input name="satuan" value="{{ old('satuan', $barang->satuan ?? 'pcs') }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Aktif</label>
            <select name="aktif" class="w-full border rounded px-3 py-2">
                <option value="1" @selected(old('aktif', $barang->aktif ? 1 : 0) == 1)>Aktif</option>
                <option value="0" @selected(old('aktif', $barang->aktif ? 1 : 0) == 0)>Tidak</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Harga Beli</label>
            <input type="number" min="0" name="harga_beli" value="{{ old('harga_beli', $barang->harga_beli) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Harga Jual</label>
            <input type="number" min="0" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Stok Min</label>
            <input type="number" min="0" name="stok_min" value="{{ old('stok_min', $barang->stok_min) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Stok Max</label>
            <input type="number" min="0" name="stok_max" value="{{ old('stok_max', $barang->stok_max) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="col-span-2 flex gap-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('barang.index') }}" class="px-4 py-2 border rounded">Batal</a>
        </div>
    </form>
</div>
@endsection
