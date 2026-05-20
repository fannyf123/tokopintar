@extends('layouts.app')
@section('title', 'Mutasi Stok Baru')
@section('breadcrumb', 'Inventory / Mutasi / Form')
@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-xl">
    <h1 class="text-xl font-bold mb-4">Mutasi Stok</h1>
    @if ($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif
    <form method="POST" action="{{ route('mutasi.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1">Barang</label>
            <select name="barang_id" required id="barang" class="w-full border rounded px-3 py-2">
                <option value="">— pilih —</option>
                @foreach ($barangs as $b)<option value="{{ $b->id }}" @selected(old('barang_id') == $b->id)>{{ $b->nama }} (stok: {{ $b->stok_current }})</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Jenis</label>
            <select name="jenis" required class="w-full border rounded px-3 py-2">
                @foreach ($jenisList as $j)<option value="{{ $j }}" @selected(old('jenis') === $j)>{{ $j }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Qty</label>
            <input type="number" min="1" name="qty" value="{{ old('qty', 1) }}" required class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm mb-1">Batch (opsional)</label>
            <select name="batch_id" id="batch" class="w-full border rounded px-3 py-2">
                <option value="">— tanpa batch —</option>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Alasan</label>
            <textarea name="alasan" required rows="2" class="w-full border rounded px-3 py-2">{{ old('alasan') }}</textarea>
        </div>
        <div class="flex gap-2">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan</button>
            <a href="{{ route('mutasi.index') }}" class="px-4 py-2 border rounded">Batal</a>
        </div>
    </form>
</div>
<script>
document.getElementById('barang').addEventListener('change', async (e) => {
    const id = e.target.value;
    const sel = document.getElementById('batch');
    sel.innerHTML = '<option value="">— tanpa batch —</option>';
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
@endsection
