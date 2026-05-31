@extends('layouts.app')
@section('title', 'Katalog Produk - TOKOPINTAR')
@section('page_title', 'Katalog Produk Indonesia')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-1"><i class="fas fa-box-open text-primary me-2"></i>Tambah Cepat dari Katalog</h6>
        <p class="text-muted small mb-3">Daftar {{ $summary['total'] }} produk umum warung Indonesia (mie instan, minuman, sembako, rokok, dll). Klik tombol di bawah untuk menambahkannya sekaligus ke Daftar Barang Anda.</p>
        <form method="POST" action="{{ route('catalog.import') }}" onsubmit="return confirm('Tambahkan produk katalog ke Daftar Barang? Produk yang sudah ada akan dilewati.')">
            @csrf
            <button class="btn btn-primary"><i class="fas fa-plus me-1"></i> Tambahkan ke Daftar Barang</button>
        </form>
        <div class="alert alert-warning small mt-3 mb-0">
            <i class="fas fa-circle-info me-1"></i> Produk ditambahkan dengan <strong>harga 0 dan status nonaktif</strong>. Setelah ditambah, buka <strong>Daftar Barang</strong>, isi harga beli & jual, lalu aktifkan barang yang Anda jual. Yang tidak dijual bisa dihapus/dibiarkan nonaktif.
        </div>
    </div>
</div>

<div class="row g-3">
    @foreach ($catalog as $kategori => $produk)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0">{{ $kategori }}</h6>
                    <span class="badge bg-light text-dark">{{ count($produk) }}</span>
                </div>
                <ul class="list-unstyled small text-muted mb-0" style="max-height:180px;overflow-y:auto;">
                    @foreach ($produk as [$nama, $satuan])
                        <li class="border-bottom py-1">{{ $nama }} <span class="text-secondary">/ {{ $satuan }}</span></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
