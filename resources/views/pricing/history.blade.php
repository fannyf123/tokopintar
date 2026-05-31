@extends('layouts.app')
@section('title', 'History Harga - TOKOPINTAR')
@section('page_title', 'History Perubahan Harga: ' . $barang->nama)
@section('content')
<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Riwayat Ubah Harga Jual</h6>
        <p class="text-muted small">Setiap kali harga jual barang ini diubah, otomatis tercatat di sini. Berguna untuk hitung price elasticity.</p>
        <div class="table-responsive">
            <table class="table table-striped table-stack">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-end">Harga Lama</th>
                        <th class="text-end">Harga Baru</th>
                        <th class="text-end">Perubahan</th>
                        <th>Diubah Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $r)
                        <tr>
                            <td data-label="Tanggal">{{ $r->created_at->format('d M Y H:i') }}</td>
                            <td data-label="Harga Lama" class="text-end">{{ format_rupiah($r->harga_jual_lama) }}</td>
                            <td data-label="Harga Baru" class="text-end fw-bold">{{ format_rupiah($r->harga_jual_baru) }}</td>
                            <td data-label="Perubahan" class="text-end {{ $r->delta_persen >= 0 ? 'text-success' : 'text-danger' }} fw-bold">
                                {{ $r->delta_persen >= 0 ? '+' : '' }}{{ $r->delta_persen }}%
                            </td>
                            <td data-label="Diubah Oleh">{{ $r->user?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat. History baru terbentuk saat harga jual diubah.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
