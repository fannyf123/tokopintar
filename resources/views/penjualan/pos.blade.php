@extends('layouts.app')
@section('title', 'POS Kasir')
@section('breadcrumb', 'Penjualan / POS')
@section('content')
<script src="https://unpkg.com/html5-qrcode@2.3.10/html5-qrcode.min.js"></script>
<div class="grid lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 bg-white rounded-lg shadow p-4">
        <div class="flex gap-2 mb-3">
            <input id="searchInput" placeholder="Scan barcode / cari nama (F2)" autofocus class="flex-1 border rounded px-3 py-2">
            <button type="button" id="scanBtn" class="bg-emerald-600 text-white px-4 rounded">📷 Scan</button>
        </div>
        <div id="scannerWrap" class="hidden mb-3">
            <div id="scanner" class="w-full max-w-sm mx-auto rounded overflow-hidden border bg-black"></div>
            <div class="flex justify-center mt-2"><button type="button" id="stopScan" class="text-sm text-red-600">Tutup Scanner</button></div>
            <p class="text-xs text-center text-gray-500 mt-1">Arahkan kamera HP ke barcode (EAN13 / QR / Code128).</p>
        </div>
        <div id="searchResults" class="max-h-40 overflow-auto border rounded mb-3 hidden"></div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase">
                <tr>
                    <th class="text-left px-2 py-2">Barang</th>
                    <th class="px-2 py-2 w-20">Qty</th>
                    <th class="px-2 py-2 w-32">Harga</th>
                    <th class="px-2 py-2 w-32">Subtotal</th>
                    <th class="w-10"></th>
                </tr>
            </thead>
            <tbody id="cart"></tbody>
        </table>
        <p id="cartEmpty" class="text-center text-gray-400 py-6">Keranjang kosong. Cari barang di kotak atas.</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4 space-y-3">
        <div>
            <label class="block text-sm mb-1">Pelanggan</label>
            <select id="pelanggan" class="w-full border rounded px-3 py-2">
                <option value="">Umum</option>
                @foreach ($pelanggans as $p)<option value="{{ $p->id }}">{{ $p->nama }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Metode Bayar</label>
            <select id="metode" class="w-full border rounded px-3 py-2">
                <option value="cash">Cash</option><option value="transfer">Transfer</option>
                <option value="qris">QRIS</option><option value="kartu">Kartu</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-sm mb-1">Diskon</label>
                <input id="diskon" type="number" min="0" value="0" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">Pajak</label>
                <input id="pajak" type="number" min="0" value="0" class="w-full border rounded px-3 py-2">
            </div>
        </div>
        <div class="border-t pt-3 space-y-1">
            <div class="flex justify-between text-sm"><span>Subtotal</span><span id="subTotal">Rp 0</span></div>
            <div class="flex justify-between text-lg font-bold"><span>Grand Total</span><span id="grandTotal">Rp 0</span></div>
        </div>
        <div>
            <label class="block text-sm mb-1">Dibayar</label>
            <input id="dibayar" type="number" min="0" value="0" class="w-full border rounded px-3 py-2 text-lg">
        </div>
        <div class="flex justify-between text-sm"><span>Kembalian</span><span id="kembalian" class="font-semibold">Rp 0</span></div>
        <button id="bayar" class="w-full bg-green-600 text-white py-3 rounded text-lg font-semibold disabled:bg-gray-300" disabled>Bayar (F4)</button>
    </div>
</div>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let cart = [];

const $ = id => document.getElementById(id);
const fmt = n => 'Rp ' + (Number(n) || 0).toLocaleString('id-ID');

async function searchBarang(q) {
    const res = await fetch('{{ route('pos.search') }}?q=' + encodeURIComponent(q));
    return res.json();
}

$('searchInput').addEventListener('input', async (e) => {
    const q = e.target.value.trim();
    const box = $('searchResults');
    if (!q) { box.classList.add('hidden'); return; }
    const data = await searchBarang(q);
    if (!data.length) { box.classList.add('hidden'); return; }
    box.innerHTML = data.map(b => `<div class="px-3 py-2 hover:bg-indigo-50 cursor-pointer flex justify-between" data-id="${b.id}" data-nama="${b.nama}" data-h="${b.harga_jual}" data-stok="${b.stok_current}">
        <span>${b.nama} <span class="text-xs text-gray-500">(${b.kode})</span></span>
        <span class="text-sm">${fmt(b.harga_jual)} · stok ${b.stok_current}</span>
    </div>`).join('');
    box.classList.remove('hidden');
    box.querySelectorAll('[data-id]').forEach(el => el.onclick = () => {
        addToCart(+el.dataset.id, el.dataset.nama, +el.dataset.h, +el.dataset.stok);
        $('searchInput').value = ''; box.classList.add('hidden'); $('searchInput').focus();
    });
});

$('searchInput').addEventListener('keydown', (e) => {
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
    const tb = $('cart');
    if (!cart.length) { tb.innerHTML = ''; $('cartEmpty').style.display = 'block'; }
    else { $('cartEmpty').style.display = 'none';
        tb.innerHTML = cart.map((x, i) => `<tr class="border-t">
            <td class="p-1">${x.nama}</td>
            <td class="p-1"><input type="number" min="1" max="${x.stok}" value="${x.qty}" class="w-16 border rounded px-2 py-1" oninput="cart[${i}].qty=Math.min(+this.value,${x.stok});render()"></td>
            <td class="p-1 text-right">${fmt(x.harga)}</td>
            <td class="p-1 text-right">${fmt(x.qty*x.harga - x.diskon)}</td>
            <td class="p-1"><button onclick="cart.splice(${i},1);render()" class="text-red-600">×</button></td>
        </tr>`).join('');
    }
    recalc();
}
function recalc() {
    const sub = cart.reduce((s, x) => s + x.qty * x.harga - x.diskon, 0);
    const d = +$('diskon').value || 0;
    const p = +$('pajak').value || 0;
    const grand = Math.max(0, sub - d + p);
    $('subTotal').textContent = fmt(sub);
    $('grandTotal').textContent = fmt(grand);
    const dibayar = +$('dibayar').value || 0;
    $('kembalian').textContent = fmt(Math.max(0, dibayar - grand));
    $('bayar').disabled = !cart.length || dibayar < grand;
}
['diskon','pajak','dibayar'].forEach(id => $(id).addEventListener('input', recalc));

$('bayar').addEventListener('click', async () => {
    const payload = {
        pelanggan_id: $('pelanggan').value || null,
        metode_bayar: $('metode').value,
        diskon: +$('diskon').value || 0,
        pajak: +$('pajak').value || 0,
        dibayar: +$('dibayar').value || 0,
        items: cart.map(x => ({barang_id: x.id, qty: x.qty, diskon_item: x.diskon || 0})),
    };
    const res = await fetch('{{ route('pos.store') }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify(payload),
    });
    if (res.redirected) { window.location = res.url; return; }
    if (!res.ok) {
        const t = await res.text();
        alert('Gagal: ' + t.substring(0, 200));
        return;
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'F2') { e.preventDefault(); $('searchInput').focus(); }
    if (e.key === 'F4') { e.preventDefault(); if (!$('bayar').disabled) $('bayar').click(); }
    if (e.key === 'Escape') { cart = []; render(); }
});

let scanner = null;
async function handleScan(code) {
    const data = await searchBarang(code);
    if (data.length === 1) {
        const b = data[0];
        addToCart(b.id, b.nama, b.harga_jual, b.stok_current);
        navigator.vibrate?.(80);
    } else if (data.length > 1) {
        $('searchInput').value = code;
        $('searchInput').dispatchEvent(new Event('input'));
    } else {
        alert('Barcode tidak ditemukan: ' + code);
    }
}
$('scanBtn').addEventListener('click', async () => {
    if (!window.Html5Qrcode) { alert('Scanner library belum siap'); return; }
    $('scannerWrap').classList.remove('hidden');
    if (!scanner) scanner = new Html5Qrcode('scanner');
    try {
        await scanner.start(
            {facingMode: 'environment'},
            {fps: 10, qrbox: {width: 250, height: 150}},
            async (text) => {
                await scanner.stop();
                $('scannerWrap').classList.add('hidden');
                handleScan(text.trim());
            },
            () => {}
        );
    } catch (e) {
        alert('Tidak bisa buka kamera: ' + e.message);
        $('scannerWrap').classList.add('hidden');
    }
});
$('stopScan').addEventListener('click', async () => {
    if (scanner?.isScanning) await scanner.stop();
    $('scannerWrap').classList.add('hidden');
});
</script>
@endsection
