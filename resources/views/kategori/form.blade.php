@extends('layouts.app')
@section('title', $kategori->exists ? 'Edit Kategori' : 'Kategori Baru')
@section('breadcrumb', 'Master / Kategori / Form')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-xl">
    <h1 class="text-xl font-bold mb-4">{{ $kategori->exists ? 'Edit Kategori' : 'Kategori Baru' }}</h1>
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
            @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif
    <form method="POST" action="{{ $kategori->exists ? route('kategori.update', $kategori) : route('kategori.store') }}" class="space-y-4">
        @csrf
        @if ($kategori->exists) @method('PUT') @endif
        <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input name="nama" value="{{ old('nama', $kategori->nama) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <input name="deskripsi" value="{{ old('deskripsi', $kategori->deskripsi) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Ikon (opsional)</label>
            <input name="ikon" value="{{ old('ikon', $kategori->ikon) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="flex gap-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('kategori.index') }}" class="px-4 py-2 border rounded">Batal</a>
        </div>
    </form>
</div>
@endsection
