@extends('layouts.app')
@section('title', 'Barang')
@section('breadcrumb', 'Master / Barang')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Barang</h1>
    <a href="{{ route('barang.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">+ Barang</a>
</div>
<form method="GET" class="bg-white rounded shadow p-3 mb-4 flex gap-2">
    <input name="q" value="{{ request('q') }}" placeholder="Cari nama / kode / barcode" class="flex-1 border rounded px-3 py-2">
    <select name="kategori_id" class="border rounded px-3 py-2">
        <option value="">Semua kategori</option>
        @foreach ($kategoris as $k)<option value="{{ $k->id }}" @selected(request('kategori_id') == $k->id)>{{ $k->nama }}</option>@endforeach
    </select>
    <button class="bg-gray-700 text-white px-4 rounded">Cari</button>
</form>
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
                <th class="text-left px-3 py-3">Kode</th>
                <th class="text-left px-3 py-3">Nama</th>
                <th class="text-left px-3 py-3">Kategori</th>
                <th class="text-right px-3 py-3">H. Beli</th>
                <th class="text-right px-3 py-3">H. Jual</th>
                <th class="text-right px-3 py-3">Stok</th>
                <th class="text-center px-3 py-3">Aktif</th>
                <th class="px-3 py-3 w-28">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $b)
                <tr class="border-t">
                    <td class="px-3 py-2 font-mono text-xs">{{ $b->kode }}</td>
                    <td class="px-3 py-2 font-medium">{{ $b->nama }}</td>
                    <td class="px-3 py-2">{{ $b->kategori?->nama }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($b->harga_beli) }}</td>
                    <td class="px-3 py-2 text-right">{{ format_rupiah($b->harga_jual) }}</td>
                    <td class="px-3 py-2 text-right {{ $b->stok_current <= $b->stok_min ? 'text-red-600 font-semibold' : '' }}">
                        {{ $b->stok_current }} {{ $b->satuan }}
                    </td>
                    <td class="px-3 py-2 text-center">{!! $b->aktif ? '<span class="text-green-600">aktif</span>' : '<span class="text-gray-400">tidak</span>' !!}</td>
                    <td class="px-3 py-2 text-right">
                        <a href="{{ route('barang.edit', $b) }}" class="text-indigo-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('barang.destroy', $b) }}" class="inline" onsubmit="return confirm('Hapus / nonaktifkan?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline ml-2">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-6 text-center text-gray-400">Belum ada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
