@extends('layouts.app')
@section('title', 'Cadangan Data - TOKOPINTAR')
@section('page_title', 'Cadangan Data')
@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-2"><i class="fas fa-database text-primary me-2"></i>Cadangkan Data Toko</h6>
                <p class="text-muted small mb-3">Simpan semua data toko (barang, penjualan, stok, pelanggan, dll) ke satu file. Simpan file ini di tempat aman sebagai cadangan kalau terjadi sesuatu.</p>
                <a href="{{ route('backup.download') }}" class="btn btn-primary"><i class="fas fa-download me-1"></i> Download Cadangan Sekarang</a>
                <div class="alert alert-info small mt-3 mb-0">
                    <i class="fas fa-lightbulb me-1"></i> Disarankan download cadangan rutin, misalnya seminggu sekali, lalu simpan di Google Drive atau flashdisk.
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Isi Cadangan</h6>
                <table class="table table-sm mb-2">
                    <tbody>
                        @foreach ($summary as $tabel => $jumlah)
                            <tr>
                                <td class="text-muted">{{ ucfirst(str_replace('_', ' ', $tabel)) }}</td>
                                <td class="text-end fw-semibold">{{ number_format($jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-top">
                            <td class="fw-bold">Total Baris Data</td>
                            <td class="text-end fw-bold text-primary">{{ number_format($total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
