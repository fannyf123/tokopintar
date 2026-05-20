@extends('layouts.app')
@section('title', 'Pembelian Baru')
@section('breadcrumb', 'Inventory / Pembelian / Form')
@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h1 class="text-xl font-bold mb-4">Pembelian Baru</h1>
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" action="{{ route('pembelian.store') }}" id="pembelianForm" class="space-y-4">
        @csrf
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">Supplier</label>
                <select name="supplier_id" required class="w-full border rounded px-3 py-2">
                    <option value="">— pilih —</option>
                    @foreach ($suppliers as $s)<option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->nama }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Metode Bayar</label>
                <select name="metode_bayar" class="w-full border rounded px-3 py-2">
                    <option value="cash">Cash</option><option value="transfer">Transfer</option><option value="tempo">Tempo</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm mb-1">Catatan</label>
            <input name="catatan" class="w-full border rounded px-3 py-2">
        </div>
        <div class="border rounded">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase">
                    <tr>
                        <th class="text-left px-2 py-2">Barang</th>
                        <th class="px-2 py-2 w-20">Qty</th>
                        <th class="px-2 py-2 w-32">Harga Beli</th>
                        <th class="px-2 py-2 w-32">No Batch</th>
                        <th class="px-2 py-2 w-40">Tgl Kadaluarsa</th>
                        <th class="px-2 py-2 w-32">Subtotal</th>
                        <th class="w-10"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody"></tbody>
                <tfoot>
                    <tr class="border-t bg-gray-50"><td colspan="5" class="text-right px-2 py-2 font-semibold">Total</td><td class="px-2 py-2 text-right font-bold" id="totalCell">Rp 0</td><td></td></tr>
                </tfoot>
            </table>
            <button type="button" onclick="addRow()" class="text-sm text-indigo-600 px-3 py-2">+ Tambah Baris</button>
        </div>
        <div>
            <label class="block text-sm mb-1">Dibayar</label>
            <input type="number" min="0" name="dibayar" value="{{ old('dibayar', 0) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div class="flex gap-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan (Draft)</button>
            <a href="{{ route('pembelian.index') }}" class="px-4 py-2 border rounded">Batal</a>
        </div>
    </form>
</div>
<script>
const BARANG = @json($barangs->map(fn($b) => ['id' => $b->id, 'nama' => $b->nama, 'harga_beli' => $b->harga_beli]));
let idx = 0;
function addRow() {
    const tb = document.getElementById('itemsBody');
    const i = idx++;
    const opts = BARANG.map(b => `<option value="${b.id}" data-h="${b.harga_beli}">${b.nama}</option>`).join('');
    tb.insertAdjacentHTML('beforeend', `<tr data-i="${i}" class="border-t">
        <td class="p-1"><select name="items[${i}][barang_id]" required class="w-full border rounded px-2 py-1" onchange="onBarang(${i}, this)"><option value="">—</option>${opts}</select></td>
        <td class="p-1"><input type="number" min="1" name="items[${i}][qty]" value="1" required class="w-full border rounded px-2 py-1" oninput="recalc(${i})"></td>
        <td class="p-1"><input type="number" min="0" name="items[${i}][harga_beli]" value="0" required class="w-full border rounded px-2 py-1" oninput="recalc(${i})"></td>
        <td class="p-1"><input name="items[${i}][no_batch]" class="w-full border rounded px-2 py-1"></td>
        <td class="p-1"><input type="date" name="items[${i}][tanggal_kadaluarsa]" class="w-full border rounded px-2 py-1"></td>
        <td class="p-1 text-right" id="sub-${i}">Rp 0</td>
        <td class="p-1 text-center"><button type="button" onclick="this.closest('tr').remove();recalcTotal()" class="text-red-600">×</button></td>
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
@endsection
