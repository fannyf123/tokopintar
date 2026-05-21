@extends('layouts.app')
@section('title', 'Pareto Monitor - TOKOPINTAR')
@section('page_title', 'Monitor Produk Pareto (Kelas A)')
@section('content')
<div class="alert alert-info small">
    <strong>Apa ini?</strong> Produk Kelas A adalah 20% produk yang menyumbang 80% omzet toko. Kalau salah satu turun signifikan, bisnis bisa terdampak besar. Sistem auto-bandingkan 30 hari ini vs 30 hari sebelumnya.
</div>

<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Produk Kelas A yang Sedang Turun &gt;30%</h6>

        @if (! empty($data['decline']))
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th class="text-end">Omzet Periode Lalu</th>
                            <th class="text-end">Omzet Sekarang</th>
                            <th class="text-end">Perubahan</th>
                            <th>Saran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['decline'] as $d)
                            <tr>
                                <td><strong>{{ $d['nama'] }}</strong></td>
                                <td class="text-end">{{ format_rupiah($d['omzet_prev']) }}</td>
                                <td class="text-end">{{ format_rupiah($d['omzet_now']) }}</td>
                                <td class="text-end fw-bold text-danger">{{ $d['delta_persen'] }}%</td>
                                <td class="small">
                                    Cek apakah: harga naik? Stok kosong? Pesaing muncul? Investigasi sebelum kelas A ini hilang.
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center py-4">{{ $data['message'] ?? 'Tidak ada produk pareto yang turun signifikan. Bisnis sehat.' }}</p>
        @endif
    </div>
</div>
@endsection
