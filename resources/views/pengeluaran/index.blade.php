@extends('layouts.app')
@section('title', 'Pengeluaran')
@section('breadcrumb', 'Admin / Pengeluaran')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Pengeluaran</h1>
    <a href="{{ route('pengeluaran.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">+ Pengeluaran</a>
</div>
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
                <th class="text-left px-3 py-3">Tanggal</th>
                <th class="text-left px-3 py-3">Kategori</th>
                <th class="text-right px-3 py-3">Jumlah</th>
                <th class="text-left px-3 py-3">Catatan</th>
                <th class="px-3 py-3 w-32">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $p)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ format_tanggal_id($p->tanggal) }}</td>
                    <td class="px-3 py-2">{{ ucfirst($p->kategori) }}</td>
                    <td class="px-3 py-2 text-right font-semibold">{{ format_rupiah($p->jumlah) }}</td>
                    <td class="px-3 py-2 text-gray-600">{{ $p->catatan }}</td>
                    <td class="px-3 py-2 text-right">
                        <a href="{{ route('pengeluaran.edit', $p) }}" class="text-indigo-600">Edit</a>
                        <form method="POST" action="{{ route('pengeluaran.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 ml-2">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-3">{{ $items->links() }}</div>
@endsection
