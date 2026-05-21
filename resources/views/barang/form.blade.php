@extends('layouts.app')
@section('title', $barang->exists ? 'Edit Barang - TOKOPINTAR' : 'Barang Baru - TOKOPINTAR')
@section('page_title', $barang->exists ? 'Edit Barang' : 'Barang Baru')
@section('content')
<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger small">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        <form method="POST" action="{{ $barang->exists ? route('barang.update', $barang) : route('barang.store') }}" class="row g-3">
            @csrf
            @if ($barang->exists) @method('PUT') @endif
            <div class="col-md-6">
                <label class="form-label fw-semibold">Kode <small class="text-muted">(kosongkan untuk auto)</small></label>
                <input name="kode" value="{{ old('kode', $barang->kode) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Barcode</label>
                <div class="input-group">
                    <input id="barcode" name="barcode" value="{{ old('barcode', $barang->barcode) }}" class="form-control">
                    <button type="button" id="scanBtn" class="btn btn-success"><i class="fas fa-camera me-1"></i> Scan</button>
                </div>
                <div id="scannerWrap" class="d-none mt-2">
                    <div id="scanner" class="rounded mx-auto" style="max-width:360px;background:#000;min-height:220px;"></div>
                    <div class="text-center mt-2"><button type="button" id="stopScan" class="btn btn-sm btn-link text-danger">Tutup Scanner</button></div>
                    <p class="text-center text-muted small mb-0">Arahkan kamera HP ke barcode produk.</p>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Nama</label>
                <input name="nama" value="{{ old('nama', $barang->nama) }}" required class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Kategori</label>
                <select name="kategori_id" required class="form-select">
                    <option value="">— pilih —</option>
                    @foreach ($kategoris as $k)<option value="{{ $k->id }}" @selected(old('kategori_id', $barang->kategori_id) == $k->id)>{{ $k->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Supplier <small class="text-muted">(default)</small></label>
                <select name="supplier_id" class="form-select">
                    <option value="">— tidak ada —</option>
                    @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id', $barang->supplier_id) == $s->id)>{{ $s->nama }}</option>@endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Satuan</label>
                <input name="satuan" value="{{ old('satuan', $barang->satuan ?? 'pcs') }}" required class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Harga Beli</label>
                <input type="number" min="0" name="harga_beli" value="{{ old('harga_beli', $barang->harga_beli) }}" required class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Harga Jual</label>
                <input type="number" min="0" name="harga_jual" value="{{ old('harga_jual', $barang->harga_jual) }}" required class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Stok Min</label>
                <input type="number" min="0" name="stok_min" value="{{ old('stok_min', $barang->stok_min) }}" required class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Stok Max</label>
                <input type="number" min="0" name="stok_max" value="{{ old('stok_max', $barang->stok_max) }}" required class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="aktif" class="form-select">
                    <option value="1" @selected(old('aktif', $barang->aktif ? 1 : 0) == 1)>Aktif</option>
                    <option value="0" @selected(old('aktif', $barang->aktif ? 1 : 0) == 0)>Tidak Aktif</option>
                </select>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('barang.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let scanner = null;
const $sb = document.getElementById('scanBtn');
const $bc = document.getElementById('barcode');
const $sw = document.getElementById('scannerWrap');
const $ss = document.getElementById('stopScan');

$sb?.addEventListener('click', async () => {
    if (!window.Html5Qrcode) { alert('Scanner belum siap'); return; }
    $sw.classList.remove('d-none');
    if (!scanner) scanner = new Html5Qrcode('scanner');
    try {
        await scanner.start({facingMode: 'environment'}, {fps: 10, qrbox: {width: 250, height: 150}},
            async (text) => {
                await scanner.stop();
                $sw.classList.add('d-none');
                $bc.value = text.trim();
                navigator.vibrate?.(80);
                $bc.dispatchEvent(new Event('change'));
            }, () => {});
    } catch (e) {
        alert('Tidak bisa buka kamera: ' + e.message);
        $sw.classList.add('d-none');
    }
});
$ss?.addEventListener('click', async () => {
    if (scanner?.isScanning) await scanner.stop();
    $sw.classList.add('d-none');
});
</script>
@endpush
