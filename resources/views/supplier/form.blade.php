@extends('layouts.app')
@section('title', $supplier->exists ? 'Edit Supplier' : 'Supplier Baru')
@section('breadcrumb', 'Master / Supplier / Form')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-xl">
    <h1 class="text-xl font-bold mb-4">{{ $supplier->exists ? 'Edit Supplier' : 'Supplier Baru' }}</h1>
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" action="{{ $supplier->exists ? route('supplier.update', $supplier) : route('supplier.store') }}" class="space-y-4">
        @csrf
        @if ($supplier->exists) @method('PUT') @endif
        @foreach (['nama' => 'Nama', 'kontak' => 'Nama Kontak', 'no_hp' => 'No HP', 'email' => 'Email', 'alamat' => 'Alamat'] as $field => $label)
            <div>
                <label class="block text-sm font-medium mb-1">{{ $label }}</label>
                <input name="{{ $field }}" value="{{ old($field, $supplier->{$field}) }}" class="w-full border rounded px-3 py-2" {{ $field === 'nama' ? 'required' : '' }}>
            </div>
        @endforeach
        <div class="flex gap-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('supplier.index') }}" class="px-4 py-2 border rounded">Batal</a>
        </div>
    </form>
</div>
@endsection
