@extends('layouts.app')
@section('title', 'Kanibalisasi Produk - TOKOPINTAR')
@section('page_title', 'Deteksi Kanibalisasi Produk')
@section('content')
<div class="alert alert-info small">
    <strong>Apa itu kanibalisasi?</strong> Saat satu produk turun penjualannya karena pelanggan beralih ke produk lain di kategori yang sama. Sistem otomatis bandingkan periode 30 hari ini vs 30 hari sebelumnya.
</div>

<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Pasangan Produk yang Saling Kanibal</h6>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th>Produk Turun ↓</th>
                        <th>Produk Naik ↑</th>
                        <th class="text-center">Saran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $c)
                        <tr>
                            <td><span class="badge bg-light text-dark">{{ $c['kategori'] }}</span></td>
                            <td>
                                <strong>{{ $c['declining_nama'] }}</strong><br>
                                <small class="text-danger">{{ $c['declining_delta'] }}% (qty {{ $c['qty_prev'] ?? '' }} &rarr; {{ $c['qty_now'] ?? '' }})</small>
                            </td>
                            <td>
                                <strong>{{ $c['rising_nama'] }}</strong><br>
                                <small class="text-success">+{{ $c['rising_delta'] }}%</small>
                            </td>
                            <td class="small">
                                Pelanggan beralih dari "{{ $c['declining_nama'] }}" ke "{{ $c['rising_nama'] }}".
                                Pertimbangkan: turunkan stok produk turun, atau hapus duplikat di kategori sama.
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Tidak ada kanibalisasi terdeteksi. Bagus!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
