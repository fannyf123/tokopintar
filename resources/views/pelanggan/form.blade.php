@extends('layouts.app')
@section('title', $pelanggan->exists ? 'Edit Pelanggan' : 'Pelanggan Baru')
@section('breadcrumb', 'Master / Pelanggan / Form')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-xl">
    <h1 class="text-xl font-bold mb-4">{{ $pelanggan->exists ? 'Edit Pelanggan' : 'Pelanggan Baru' }}</h1>
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" action="{{ $pelanggan->exists ? route('pelanggan.update', $pelanggan) : route('pelanggan.store') }}" class="space-y-4">
        @csrf
        @if ($pelanggan->exists) @method('PUT') @endif
        <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input name="nama" value="{{ old('nama', $pelanggan->nama) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">No HP</label>
                <input name="no_hp" value="{{ old('no_hp', $pelanggan->no_hp) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tipe</label>
                <select name="tipe" class="w-full border rounded px-3 py-2">
                    @foreach (['umum' => 'Umum', 'member' => 'Member'] as $v => $l)
                        <option value="{{ $v }}" @selected(old('tipe', $pelanggan->tipe ?? 'umum') === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Alamat</label>
            <input name="alamat" value="{{ old('alamat', $pelanggan->alamat) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="flex gap-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('pelanggan.index') }}" class="px-4 py-2 border rounded">Batal</a>
        </div>
    </form>
</div>
@endsection
