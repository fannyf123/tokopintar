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
<div class="row g-3">
    <div class="col-lg-3">
        <div class="card sticky-top" style="top:90px;">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Topik Panduan</h6>
                <div class="nav nav-pills flex-column" id="panduanTab" role="tablist">
                    <button class="nav-link active text-start mb-1" data-bs-toggle="pill" data-bs-target="#tab-mulai">
                        <i class="fas fa-play-circle me-2"></i> Memulai
                    </button>
                    <button class="nav-link text-start mb-1" data-bs-toggle="pill" data-bs-target="#tab-barang">
                        <i class="fas fa-box me-2"></i> Atur Barang
                    </button>
                    <button class="nav-link text-start mb-1" data-bs-toggle="pill" data-bs-target="#tab-masuk">
                        <i class="fas fa-truck-loading me-2"></i> Barang Masuk
                    </button>
                    <button class="nav-link text-start mb-1" data-bs-toggle="pill" data-bs-target="#tab-kasir">
                        <i class="fas fa-cash-register me-2"></i> Cara Jualan
                    </button>
                    <button class="nav-link text-start mb-1" data-bs-toggle="pill" data-bs-target="#tab-stok">
                        <i class="fas fa-exchange-alt me-2"></i> Atur Stok
                    </button>
                    <button class="nav-link text-start mb-1" data-bs-toggle="pill" data-bs-target="#tab-laporan">
                        <i class="fas fa-chart-line me-2"></i> Lihat Untung
                    </button>
                    <button class="nav-link text-start mb-1" data-bs-toggle="pill" data-bs-target="#tab-saran">
                        <i class="fas fa-brain me-2"></i> Saran Toko
                    </button>
                    <button class="nav-link text-start" data-bs-toggle="pill" data-bs-target="#tab-faq">
                        <i class="fas fa-question-circle me-2"></i> FAQ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <div class="tab-content">
                    @include('panduan._mulai')
                    @include('panduan._barang')
                    @include('panduan._masuk')
                    @include('panduan._kasir')
                    @include('panduan._stok')
                    @include('panduan._laporan')
                    @include('panduan._saran')
                    @include('panduan._faq')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
