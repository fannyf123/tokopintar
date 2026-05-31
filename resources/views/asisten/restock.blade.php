@extends('layouts.app')
@section('title', 'Daftar Restock - TOKOPINTAR')
@section('page_title', 'Daftar Belanja / Restock')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-1"><i class="fas fa-cart-plus text-primary me-2"></i>Barang yang Perlu Dibeli</h6>
        <p class="text-muted small mb-0">Daftar otomatis dari barang yang stoknya menipis, lengkap dengan saran jumlah beli berdasarkan kecepatan laku 30 hari terakhir.</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($list->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-stack">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th class="text-center">Urgensi</th>
                        <th class="text-end">Stok Sekarang</th>
                        <th class="text-end">Laku/hari</th>
                        <th class="text-end">Cukup utk</th>
                        <th class="text-end">Saran Beli</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($list as $b)
                        <tr>
                            <td data-label="Barang" class="fw-semibold">{{ $b['nama'] }}</td>
                            <td data-label="Urgensi" class="text-center"><span class="badge bg-{{ $b['warna'] }}">{{ $b['urgensi'] }}</span></td>
                            <td data-label="Stok Sekarang" class="text-end">{{ $b['stok_current'] }} {{ $b['satuan'] }}</td>
                            <td data-label="Laku/hari" class="text-end">{{ $b['per_hari'] }}</td>
                            <td data-label="Cukup utk" class="text-end">{{ $b['sisa_hari'] !== null ? $b['sisa_hari'] . ' hari' : '-' }}</td>
                            <td data-label="Saran Beli" class="text-end fw-bold text-primary">{{ $b['saran_beli'] }} {{ $b['satuan'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="alert alert-info small mt-2 mb-0">
            <i class="fas fa-lightbulb me-1"></i> <strong>HABIS/MENDESAK</strong> = stok kosong atau habis dalam 3 hari. <strong>Saran Beli</strong> dihitung agar stok cukup ~2 minggu.
        </div>
        @else
        <p class="text-center text-success py-4 mb-0"><i class="fas fa-check-circle fs-1 d-block mb-2"></i>Semua stok masih aman. Belum ada yang perlu dibeli.</p>
        @endif
    </div>
</div>
@endsection
