@extends('layouts.app')
@section('title', 'POS Kasir - TOKOPINTAR')
@section('page_title', 'POS / Kasir')

@push('styles')
<style>
.pos-results { max-height: 250px; overflow-y: auto; }
.pos-results .item { cursor:pointer; padding:8px 12px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; }
.pos-results .item:hover { background:#eef2ff; }
#scanner { background:#000; min-height:220px; }
</style>
@endpush

@section('content')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="input-group mb-3">
                    <input id="searchInput" placeholder="Scan barcode / cari nama (F2)" autofocus class="form-control">
                    <button type="button" id="scanBtn" class="btn btn-success"><i class="fas fa-camera me-1"></i> Scan HP</button>
                </div>
                <div id="scannerWrap" class="d-none mb-3">
                    <div id="scanner" class="rounded mx-auto" style="max-width:360px;"></div>
                    <div class="text-center mt-2"><button type="button" id="stopScan" class="btn btn-sm btn-link text-danger">Tutup Scanner</button></div>
                    <p class="text-center text-muted small mb-0">Arahkan kamera HP ke barcode (EAN13 / QR / Code128).</p>
                </div>
                <div id="searchResults" class="pos-results border rounded mb-3 d-none"></div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th class="text-center" style="width:100px;">Qty</th>
                                <th class="text-end" style="width:120px;">Harga</th>
                                <th class="text-end" style="width:140px;">Subtotal</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cart"></tbody>
                    </table>
                </div>
                <p id="cartEmpty" class="text-center text-muted py-4"><i class="fas fa-shopping-basket fs-1 d-block mb-2 opacity-50"></i>Keranjang kosong. Cari atau scan barang.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pelanggan</label>
                    <select id="pelanggan" class="form-select form-select-sm">
                        <option value="">Umum</option>
                        @foreach ($pelanggans as $p)<option value="{{ $p->id }}">{{ $p->nama }}</option>@endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Metode Bayar</label>
                    <select id="metode" class="form-select form-select-sm">
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                        <option value="kartu">Kartu</option>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Diskon</label>
                        <input id="diskon" type="number" min="0" value="0" class="form-control form-control-sm">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold">Pajak</label>
                        <input id="pajak" type="number" min="0" value="0" class="form-control form-control-sm">
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Subtotal</span><span id="subTotal">Rp 0</span></div>
                <div class="d-flex justify-content-between fs-5 fw-bold mb-3"><span>Grand Total</span><span id="grandTotal" class="text-primary">Rp 0</span></div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Dibayar</label>
                    <input id="dibayar" type="number" min="0" value="0" class="form-control form-control-lg">
                </div>
                <div class="d-flex justify-content-between small mb-3"><span class="text-muted">Kembalian</span><span id="kembalian" class="fw-semibold text-success">Rp 0</span></div>
                <button id="bayar" class="btn btn-success btn-lg w-100" disabled><i class="fas fa-credit-card me-2"></i>Bayar (F4)</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let cart = [];
const $g = id => document.getElementById(id);
const fmt = n => 'Rp ' + (Number(n) || 0).toLocaleString('id-ID');

async function searchBarang(q) {
    const res = await fetch('{{ route('pos.search') }}?q=' + encodeURIComponent(q));
    return res.json();
}

$g('searchInput').addEventListener('input', async (e) => {
    const q = e.target.value.trim();
    const box = $g('searchResults');
    if (!q) { box.classList.add('d-none'); return; }
    const data = await searchBarang(q);
    if (!data.length) { box.classList.add('d-none'); return; }
    box.innerHTML = data.map(b => `<div class="item" data-id="${b.id}" data-nama="${b.nama}" data-h="${b.harga_jual}" data-stok="${b.stok_current}">
        <span><strong>${b.nama}</strong> <small class="text-muted">(${b.kode})</small></span>
        <small>${fmt(b.harga_jual)} · stok ${b.stok_current}</small>
    </div>`).join('');
    box.classList.remove('d-none');
    box.querySelectorAll('[data-id]').forEach(el => el.onclick = () => {
        addToCart(+el.dataset.id, el.dataset.nama, +el.dataset.h, +el.dataset.stok);
        $g('searchInput').value = ''; box.classList.add('d-none'); $g('searchInput').focus();
    });
});

$g('searchInput').addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        const first = document.querySelector('#searchResults [data-id]');
        if (first) first.click();
    }
});

function addToCart(id, nama, harga, stok) {
    const ex = cart.find(x => x.id === id);
    if (ex) { if (ex.qty + 1 > stok) return alert('Stok tidak cukup'); ex.qty++; }
    else cart.push({id, nama, harga, qty: 1, stok, diskon: 0});
    render();
}

function render() {
    const tb = $g('cart');
    if (!cart.length) { tb.innerHTML = ''; $g('cartEmpty').style.display = 'block'; }
    else { $g('cartEmpty').style.display = 'none';
        tb.innerHTML = cart.map((x, i) => `<tr>
            <td class="fw-semibold">${x.nama}</td>
            <td class="text-center"><input type="number" min="1" max="${x.stok}" value="${x.qty}" class="form-control form-control-sm text-center" oninput="cart[${i}].qty=Math.min(+this.value,${x.stok});render()"></td>
            <td class="text-end">${fmt(x.harga)}</td>
            <td class="text-end fw-semibold">${fmt(x.qty*x.harga - x.diskon)}</td>
            <td class="text-center"><button onclick="cart.splice(${i},1);render()" class="btn btn-sm btn-link text-danger"><i class="fas fa-times"></i></button></td>
        </tr>`).join('');
    }
    recalc();
}

function recalc() {
    const sub = cart.reduce((s, x) => s + x.qty * x.harga - x.diskon, 0);
    const d = +$g('diskon').value || 0;
    const p = +$g('pajak').value || 0;
    const grand = Math.max(0, sub - d + p);
    $g('subTotal').textContent = fmt(sub);
    $g('grandTotal').textContent = fmt(grand);
    const dibayar = +$g('dibayar').value || 0;
    $g('kembalian').textContent = fmt(Math.max(0, dibayar - grand));
    $g('bayar').disabled = !cart.length || dibayar < grand;
}
['diskon','pajak','dibayar'].forEach(id => $g(id).addEventListener('input', recalc));

$g('bayar').addEventListener('click', async () => {
    const payload = {
        pelanggan_id: $g('pelanggan').value || null,
        metode_bayar: $g('metode').value,
        diskon: +$g('diskon').value || 0,
        pajak: +$g('pajak').value || 0,
        dibayar: +$g('dibayar').value || 0,
        items: cart.map(x => ({barang_id: x.id, qty: x.qty, diskon_item: x.diskon || 0})),
    };
    const res = await fetch('{{ route('pos.store') }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify(payload),
    });
    if (res.redirected) { window.location = res.url; return; }
    if (!res.ok) { alert('Gagal: ' + (await res.text()).substring(0, 200)); }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'F2') { e.preventDefault(); $g('searchInput').focus(); }
    if (e.key === 'F4') { e.preventDefault(); if (!$g('bayar').disabled) $g('bayar').click(); }
    if (e.key === 'Escape') { cart = []; render(); }
});

let scanner = null;
async function handleScan(code) {
    const data = await searchBarang(code);
    if (data.length === 1) { addToCart(data[0].id, data[0].nama, data[0].harga_jual, data[0].stok_current); navigator.vibrate?.(80); }
    else if (data.length > 1) { $g('searchInput').value = code; $g('searchInput').dispatchEvent(new Event('input')); }
    else { alert('Barcode tidak ditemukan: ' + code); }
}
$g('scanBtn').addEventListener('click', async () => {
    if (!window.Html5Qrcode) { alert('Scanner belum siap'); return; }
    $g('scannerWrap').classList.remove('d-none');
    if (!scanner) scanner = new Html5Qrcode('scanner');
    try {
        await scanner.start({facingMode: 'environment'}, {fps: 10, qrbox: {width: 250, height: 150}},
            async (text) => { await scanner.stop(); $g('scannerWrap').classList.add('d-none'); handleScan(text.trim()); }, () => {});
    } catch (e) {
        alert('Tidak bisa buka kamera: ' + e.message);
        $g('scannerWrap').classList.add('d-none');
    }
});
$g('stopScan').addEventListener('click', async () => {
    if (scanner?.isScanning) await scanner.stop();
    $g('scannerWrap').classList.add('d-none');
});
</script>
@endpush
