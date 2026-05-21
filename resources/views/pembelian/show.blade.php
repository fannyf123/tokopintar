@extends('layouts.app')
@section('title', 'Pembelian #' . $pembelian->nomor . ' - TOKOPINTAR')
@section('page_title', 'Pembelian ' . $pembelian->nomor)
@section('content')
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1">{{ $pembelian->nomor }}</h5>
                <p class="text-muted small mb-0">{{ format_tanggal_id($pembelian->tanggal) }} · {{ $pembelian->supplier?->nama }}</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                @php $badge = ['draft' => 'secondary', 'diterima' => 'success', 'batal' => 'danger']; @endphp
                <span class="badge bg-{{ $badge[$pembelian->status] ?? 'secondary' }} fs-6">{{ strtoupper($pembelian->status) }}</span>
                @if ($pembelian->status === 'draft')
                    <form method="POST" action="{{ route('pembelian.terima', $pembelian) }}" onsubmit="return confirm('Terima dan masukkan ke stok?')" class="d-inline">@csrf
                        <button class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i> Terima Barang</button>
                    </form>
                    <form method="POST" action="{{ route('pembelian.batal', $pembelian) }}" onsubmit="return confirm('Batalkan?')" class="d-inline">@csrf
                        <button class="btn btn-sm btn-danger"><i class="fas fa-times me-1"></i> Batal</button>
                    </form>
                @endif
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Barang</th>
                        <th class="text-center" style="width:80px;">Qty</th>
                        <th class="text-end">H. Beli</th>
                        <th class="text-end">Subtotal</th>
                        <th>No Batch</th>
                        <th>Kadaluarsa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembelian->details as $d)
                        <tr>
                            <td class="fw-semibold">{{ $d->barang?->nama }}</td>
                            <td class="text-center">{{ $d->qty }}</td>
                            <td class="text-end">{{ format_rupiah($d->harga_beli) }}</td>
                            <td class="text-end">{{ format_rupiah($d->subtotal) }}</td>
                            <td><code>{{ $d->no_batch ?? '-' }}</code></td>
                            <td>{{ $d->tanggal_kadaluarsa ? format_tanggal_id($d->tanggal_kadaluarsa) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr><th colspan="3" class="text-end">Total</th><th class="text-end">{{ format_rupiah($pembelian->total) }}</th><th colspan="2"></th></tr>
                    <tr><th colspan="3" class="text-end fw-normal">Dibayar</th><th class="text-end fw-normal">{{ format_rupiah($pembelian->dibayar) }}</th><th colspan="2"></th></tr>
                </tfoot>
            </table>
        </div>
        @if ($pembelian->catatan)<div class="alert alert-light small mt-3"><strong>Catatan:</strong> {{ $pembelian->catatan }}</div>@endif
        <a href="{{ route('pembelian.index') }}" class="btn btn-sm btn-light mt-3"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
    </div>
</div>
@endsection
