@extends('layouts.app')
@section('title', 'Export Data - TOKOPINTAR')
@section('page_title', 'Export Data')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-1"><i class="fas fa-file-export text-primary me-2"></i>Export Data Toko</h6>
        <p class="text-muted small mb-0">Unduh data dalam format yang mudah dibuka & diedit. Excel/CSV untuk diolah di spreadsheet, Word untuk dokumen, PDF untuk dicetak.</p>
    </div>
</div>

<div class="row g-3">
    @foreach ($datasets as $key => $judul)
    <div class="col-md-6 col-lg-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-table text-secondary me-2"></i>{{ $judul }}</h6>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('export.download', [$key, 'excel']) }}" class="btn btn-sm btn-outline-success"><i class="fas fa-file-excel me-1"></i> Excel</a>
                    <a href="{{ route('export.download', [$key, 'csv']) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-file-csv me-1"></i> CSV</a>
                    <a href="{{ route('export.download', [$key, 'word']) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-word me-1"></i> Word</a>
                    <a href="{{ route('export.download', [$key, 'pdf']) }}" class="btn btn-sm btn-outline-danger"><i class="fas fa-file-pdf me-1"></i> PDF</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
