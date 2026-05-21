@extends('layouts.app')
@section('title', 'Aturan Asosiasi - TOKOPINTAR')
@section('page_title', 'Aturan Asosiasi (Apriori)')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h6 class="fw-bold mb-0">Pelanggan beli A juga sering beli B</h6>
    <form method="POST" action="{{ route('advanced.rules.regenerate') }}" class="d-inline">@csrf
        <button class="btn btn-primary"><i class="fas fa-sync-alt me-1"></i> Hitung Ulang</button>
    </form>
</div>

<div class="alert alert-info small">
    <strong>Penjelasan singkat:</strong>
    <ul class="mb-0 mt-2">
        <li><strong>Support</strong>: berapa % transaksi mengandung kombinasi ini</li>
        <li><strong>Confidence</strong>: kalau pelanggan beli A, berapa % kemungkinan beli B juga</li>
        <li><strong>Lift</strong>: makin tinggi, makin kuat hubungan (>1 = positif, &gt;2 = sangat kuat)</li>
    </ul>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Pelanggan beli...</th>
                        <th>...biasanya juga beli</th>
                        <th class="text-end">Co-occur</th>
                        <th class="text-end">Support</th>
                        <th class="text-end">Confidence</th>
                        <th class="text-end">Lift</th>
                        <th>Saran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $r)
                        <tr>
                            <td><strong>{{ $r->antecedent?->nama }}</strong></td>
                            <td><strong>{{ $r->consequent?->nama }}</strong></td>
                            <td class="text-end">{{ $r->co_count }}x</td>
                            <td class="text-end">{{ number_format($r->support * 100, 1) }}%</td>
                            <td class="text-end">{{ number_format($r->confidence * 100, 1) }}%</td>
                            <td class="text-end">
                                <span class="badge {{ $r->lift >= 2 ? 'bg-success' : ($r->lift >= 1.5 ? 'bg-info' : 'bg-secondary') }}">
                                    {{ number_format($r->lift, 2) }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                @if ($r->lift >= 2)
                                    Bundling pasti laku, tampilkan dekat
                                @elseif ($r->lift >= 1.5)
                                    Cross-sell saat di kasir
                                @else
                                    Hubungan lemah, abaikan
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada aturan. Klik Hitung Ulang. Butuh minimal 20 transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $items->links() }}
    </div>
</div>
@endsection
