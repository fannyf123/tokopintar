@extends('layouts.app')
@section('title', 'Penyesuaian Stok Baru - TOKOPINTAR')
@section('page_title', 'Penyesuaian Stok Baru')
@section('content')
<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger small">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        <form method="POST" action="{{ route('mutasi.store') }}" class="row g-3">
            @csrf
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Pilih Barang</label>
                <select name="barang_id" required id="barang" class="form-select">
                    <option value="">- pilih barang -</option>
                    @foreach ($barangs as $b)<option value="{{ $b->id }}" @selected(old('barang_id') == $b->id)>{{ $b->nama }} (stok sekarang: {{ $b->stok_current }})</option>@endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Alasan</label>
                <select name="jenis" required class="form-select">
                    @php $jenisLabel = [
                        'adjustment_plus' => 'Tambah stok (penyesuaian)',
                        'adjustment_minus' => 'Kurangi stok (penyesuaian)',
                        'retur_jual' => 'Retur dari pelanggan',
                        'retur_beli' => 'Retur ke pemasok',
                        'rusak' => 'Barang rusak',
                        'hilang' => 'Barang hilang',
                        'expired_dibuang' => 'Sudah kadaluarsa',
                    ]; @endphp
                    @foreach ($jenisList as $j)<option value="{{ $j }}" @selected(old('jenis') === $j)>{{ $jenisLabel[$j] ?? $j }}</option>@endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label fw-semibold">Jumlah</label>
                <input type="number" min="1" name="qty" value="{{ old('qty', 1) }}" required class="form-control">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Batch Tertentu <small class="text-muted">(opsional)</small></label>
                <select name="batch_id" id="batch" class="form-select">
                    <option value="">- semua batch / tanpa batch -</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Catatan / Keterangan</label>
                <textarea name="alasan" required rows="2" class="form-control" placeholder="Misal: 2 pcs pecah waktu bongkar muat">{{ old('alasan') }}</textarea>
            </div>
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('mutasi.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
document.getElementById('barang').addEventListener('change', async (e) => {
    const id = e.target.value;
    const sel = document.getElementById('batch');
    sel.innerHTML = '<option value="">- tanpa batch -</option>';
    if (!id) return;
    const res = await fetch(`/mutasi/batches/${id}`);
    const data = await res.json();
    data.forEach(b => {
        const opt = document.createElement('option');
        opt.value = b.id;
        opt.textContent = `${b.no_batch ?? '(tanpa nomor)'} sisa ${b.qty_sisa}` + (b.tanggal_kadaluarsa ? ` (exp ${b.tanggal_kadaluarsa})` : '');
        sel.appendChild(opt);
    });
});
</script>
@endpush
