@extends('layouts.app')
@section('title', 'Panduan Pemakaian - TOKOPINTAR')
@section('page_title', 'Panduan Pemakaian')

@push('styles')
<style>
.panduan-step { background:#f8fafc; border-left:4px solid #4361ee; padding:14px 18px; margin-bottom:12px; border-radius:8px; }
.panduan-step .step-num { display:inline-block; background:#4361ee; color:#fff; width:28px; height:28px; line-height:28px; text-align:center; border-radius:50%; font-weight:700; margin-right:8px; }
.panduan-tip { background:#fef3c7; border-left:4px solid #f59e0b; padding:10px 14px; margin:10px 0; border-radius:6px; font-size:13px; }
[data-bs-theme="dark"] .panduan-step { background:#1e293b; }
[data-bs-theme="dark"] .panduan-tip { background:#78350f; color:#fde68a; }
.nav-pills .nav-link { color:#64748b; }
.nav-pills .nav-link.active { background:#4361ee; }
[data-bs-theme="dark"] .nav-pills .nav-link { color:#94a3b8; }
</style>
@endpush

@section('content')
@php
    $allow = ['mulai', 'barang', 'masuk', 'kasir', 'stok', 'laporan', 'saran', 'faq'];
    $tabs = [
        'mulai' => ['Memulai', 'fa-play-circle'],
        'barang' => ['Atur Barang', 'fa-box'],
        'masuk' => ['Barang Masuk', 'fa-truck-loading'],
        'kasir' => ['Cara Jualan', 'fa-cash-register'],
        'stok' => ['Atur Stok', 'fa-exchange-alt'],
        'laporan' => ['Lihat Untung', 'fa-chart-line'],
        'saran' => ['Saran Toko', 'fa-brain'],
        'faq' => ['FAQ', 'fa-question-circle'],
    ];
@endphp
<div class="row g-3">
    <div class="col-lg-3">
        <div class="card sticky-top" style="top:90px;">
            <div class="card-body">
                <div class="alert alert-info small mb-3 py-2">
                    <i class="fas fa-book-open me-1"></i> Panduan lengkap pemakaian TOKOPINTAR
                </div>
                <h6 class="fw-bold mb-3">Topik Panduan</h6>
                <div class="nav nav-pills flex-column" id="panduanTab" role="tablist">
                    @foreach ($allow as $i => $key)
                        <button class="nav-link {{ $i === 0 ? 'active' : '' }} text-start mb-1" data-bs-toggle="pill" data-bs-target="#tab-{{ $key }}">
                            <i class="fas {{ $tabs[$key][1] }} me-2"></i> {{ $tabs[$key][0] }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <div class="tab-content">
                    @foreach ($allow as $key)
                        @include('panduan._' . $key)
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
