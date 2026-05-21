@extends('layouts.app')
@section('title', 'Pembelian Baru - TOKOPINTAR')
@section('page_title', 'Pembelian Baru')
@section('content')
<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger small">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        <form method="POST" action="{{ route('pembelian.store') }}" id="pembelianForm">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Supplier</label>
                    <select name="supplier_id" required class="form-select">
                        <option value="">— pilih —</option>
                        @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->nama }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Metode Bayar</label>
                    <select name="metode_bayar" class="form-select">
                        <option value="cash">Cash</option><option value="transfer">Transfer</option><option value="tempo">Tempo</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Catatan</label>
                    <input name="catatan" class="form-control">
                </div>
            </div>

            <div class="table-responsive border rounded">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Barang</th>
                            <th style="width:90px;">Qty</th>
                            <th style="width:130px;">Harga Beli</th>
                            <th style="width:130px;">No Batch</th>
                            <th style="width:160px;">Tgl Kadaluarsa</th>
                            <th class="text-end" style="width:130px;">Subtotal</th>
                            <th style="width:50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody"></tbody>
                    <tfoot class="table-light">
                        <tr><td colspan="5" class="text-end fw-semibold">Total</td><td class="text-end fw-bold" id="totalCell">Rp 0</td><td></td></tr>
                    </tfoot>
                </table>
            </div>
            <button type="button" onclick="addRow()" class="btn btn-sm btn-link"><i class="fas fa-plus me-1"></i> Tambah Baris</button>

            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Dibayar</label>
                    <input type="number" min="0" name="dibayar" value="{{ old('dibayar', 0) }}" required class="form-control">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Draft</button>
                    <a href="{{ route('pembelian.index') }}" class="btn btn-light">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const BARANG = @json($barangs->map(fn($b) => ['id' => $b->id, 'nama' => $b->nama, 'harga_beli' => $b->harga_beli]));
let idx = 0;
function addRow() {
    const tb = document.getElementById('itemsBody');
    const i = idx++;
    const opts = BARANG.map(b => `<option value="${b.id}" data-h="${b.harga_beli}">${b.nama}</option>`).join('');
    tb.insertAdjacentHTML('beforeend', `<tr data-i="${i}">
        <td><select name="items[${i}][barang_id]" required class="form-select form-select-sm" onchange="onBarang(${i}, this)"><option value="">—</option>${opts}</select></td>
        <td><input type="number" min="1" name="items[${i}][qty]" value="1" required class="form-control form-control-sm" oninput="recalc(${i})"></td>
        <td><input type="number" min="0" name="items[${i}][harga_beli]" value="0" required class="form-control form-control-sm" oninput="recalc(${i})"></td>
        <td><input name="items[${i}][no_batch]" class="form-control form-control-sm"></td>
        <td><input type="date" name="items[${i}][tanggal_kadaluarsa]" class="form-control form-control-sm"></td>
        <td class="text-end" id="sub-${i}">Rp 0</td>
        <td class="text-center"><button type="button" onclick="this.closest('tr').remove();recalcTotal()" class="btn btn-sm btn-link text-danger"><i class="fas fa-times"></i></button></td>
    </tr>`);
}
function onBarang(i, sel) {
    const opt = sel.selectedOptions[0]; if (!opt) return;
    const h = opt.dataset.h || 0;
    document.querySelector(`tr[data-i="${i}"] input[name="items[${i}][harga_beli]"]`).value = h;
    recalc(i);
}
function recalc(i) {
    const row = document.querySelector(`tr[data-i="${i}"]`);
    const q = +row.querySelector(`input[name="items[${i}][qty]"]`).value || 0;
    const h = +row.querySelector(`input[name="items[${i}][harga_beli]"]`).value || 0;
    document.getElementById(`sub-${i}`).textContent = 'Rp ' + (q*h).toLocaleString('id-ID');
    recalcTotal();
}
function recalcTotal() {
    let t = 0;
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const i = tr.dataset.i;
        const q = +tr.querySelector(`input[name="items[${i}][qty]"]`).value || 0;
        const h = +tr.querySelector(`input[name="items[${i}][harga_beli]"]`).value || 0;
        t += q*h;
    });
    document.getElementById('totalCell').textContent = 'Rp ' + t.toLocaleString('id-ID');
}
addRow();
</script>
@endpush
