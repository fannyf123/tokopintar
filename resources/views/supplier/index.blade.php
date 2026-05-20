@extends('layouts.app')
@section('title', 'Supplier')
@section('breadcrumb', 'Master / Supplier')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Supplier</h1>
    <a href="{{ route('supplier.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">+ Supplier</a>
</div>
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
            <tr>
                <th class="text-left px-4 py-3">Nama</th>
                <th class="text-left px-4 py-3">Kontak</th>
                <th class="text-left px-4 py-3">No HP</th>
                <th class="text-left px-4 py-3">Email</th>
                <th class="px-4 py-3 w-32">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $row)
                <tr class="border-t">
                    <td class="px-4 py-2 font-medium">{{ $row->nama }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $row->kontak }}</td>
                    <td class="px-4 py-2">{{ $row->no_hp }}</td>
                    <td class="px-4 py-2">{{ $row->email }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('supplier.edit', $row) }}" class="text-indigo-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('supplier.destroy', $row) }}" class="inline" onsubmit="return confirm('Hapus supplier?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline ml-2">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
