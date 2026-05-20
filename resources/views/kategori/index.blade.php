@extends('layouts.app')
@section('title', 'Kategori')
@section('breadcrumb', 'Master / Kategori')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Kategori</h1>
    <a href="{{ route('kategori.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Kategori</a>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="text-left px-4 py-3">Nama</th>
                <th class="text-left px-4 py-3">Deskripsi</th>
                <th class="px-4 py-3 w-32">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $row)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium">{{ $row->nama }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $row->deskripsi }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('kategori.edit', $row) }}" class="text-indigo-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('kategori.destroy', $row) }}" class="inline" onsubmit="return confirm('Hapus kategori?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline ml-2">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
