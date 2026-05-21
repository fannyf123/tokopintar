@extends('layouts.app')
@section('title', 'Cek Kadaluarsa - TOKOPINTAR')
@section('page_title', 'Cek Tanggal Kadaluarsa')
@section('content')
<div class="card">
    <div class="card-body">
        <h6 class="fw-bold mb-3">Daftar Stok Berdasarkan Tanggal Kadaluarsa</h6>
        <p class="text-muted small">Yang merah sudah kadaluarsa, kuning hampir kadaluarsa (≤30 hari), hijau masih aman.</p>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th>No Batch</th>
                        <th class="text-end">Sisa</th>
                        <th>Tgl Masuk</th>
                        <th>Tgl Kadaluarsa</th>
                        <th class="text-center">Status</th>
                        <th style="width:140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $b)
                        @php
                            $hari = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($b->tanggal_kadaluarsa)->startOfDay(), false);
                            if ($hari < 0) { $badge = 'danger'; $lbl = 'EXPIRED'; }
                            elseif ($hari <= 30) { $badge = 'warning'; $lbl = "{$hari} hari"; }
                            else { $badge = 'success'; $lbl = "{$hari} hari"; }
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $b->barang?->nama }}</td>
                            <td><code>{{ $b->no_batch ?? '-' }}</code></td>
                            <td class="text-end">{{ $b->qty_sisa }}</td>
                            <td>{{ format_tanggal_id($b->tanggal_masuk) }}</td>
                            <td>{{ format_tanggal_id($b->tanggal_kadaluarsa) }}</td>
                            <td class="text-center"><span class="badge bg-{{ $badge }}">{{ $lbl }}</span></td>
                            <td>
                                @if ($hari < 0)
                                    <form method="POST" action="{{ route('expiry.buang', $b) }}" onsubmit="return confirm('Buang stok kadaluarsa? Akan tercatat di Penyesuaian Stok.')" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash me-1"></i> Buang</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada barang yang punya tanggal kadaluarsa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
