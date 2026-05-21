@extends('layouts.app')
@section('title', 'Kasir - TOKOPINTAR')
@section('page_title', 'Kasir')

@push('styles')
<style>
.pos-results { max-height: 250px; overflow-y: auto; }
.pos-results .item { cursor:pointer; padding:8px 12px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; }
.pos-results .item:hover { background:#eef2ff; }
#scanner { background:#000; min-height:220px; }
@media (max-width:991.98px) {
    #scanner { min-height:260px; max-width:100%; }
    .pos-summary-mobile { position:sticky; bottom:0; z-index:20; }
}
</style>
@endpush

@section('content')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="input-group mb-3">
                    <input id="searchInput" placeholder="Cari nama barang atau scan barcode..." autofocus class="form-control">
                    <button type="button" id="scanBtn" class="btn btn-success"><i class="fas fa-camera me-1"></i> Scan</button>
                </div>
                <div id="scannerWrap" class="d-none mb-3">
                    <div id="scanner" class="rounded mx-auto" style="max-width:360px;"></div>
                    <div class="text-center mt-2"><button type="button" id="stopScan" class="btn btn-sm btn-link text-danger">Tutup Kamera</button></div>
                    <p class="text-center text-muted small mb-0">Arahkan kamera HP ke barcode pada kemasan.</p>
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
                <p id="cartEmpty" class="text-center text-muted py-4"><i class="fas fa-shopping-basket fs-1 d-block mb-2 opacity-50"></i>Belum ada barang. Cari atau scan barcode dulu.</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Pelanggan</label>
                    <div class="position-relative">
                        <input type="text" id="pelangganSearch" placeholder="Pembeli Umum (klik untuk cari pelanggan)" class="form-control form-control-sm" autocomplete="off">
                        <input type="hidden" id="pelanggan" value="">
                        <div id="pelangganList" class="d-none position-absolute w-100 border rounded mt-1 bg-white shadow-sm" style="z-index:50;max-height:200px;overflow-y:auto;"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-semibold">Cara Bayar</label>
                    <select id="metode" class="form-select form-select-sm">
                        <option value="cash">Tunai</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                        <option value="kartu">Kartu Debit/Kredit</option>
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
                            <span>Potongan</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" id="diskonModeBtn" style="font-size:10px;" data-mode="rp">Rp</button>
                        </label>
                        <input id="diskon" type="text" inputmode="numeric" value="0" class="form-control form-control-sm money-input">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold d-flex justify-content-between align-items-center">
                            <span>Pajak</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" id="pajakModeBtn" style="font-size:10px;" data-mode="rp">Rp</button>
                        </label>
                        <input id="pajak" type="text" inputmode="numeric" value="0" class="form-control form-control-sm money-input">
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Subtotal Belanja</span><span id="subTotal">Rp 0</span></div>
                <div class="d-flex justify-content-between fs-5 fw-bold mb-3"><span>Total Bayar</span><span id="grandTotal" class="text-primary">Rp 0</span></div>
                <div class="mb-2">
                    <label class="form-label small fw-semibold">Uang Diterima</label>
                    <input id="dibayar" type="text" inputmode="numeric" value="0" class="form-control form-control-lg money-input">
                </div>
                <div class="d-flex justify-content-between small mb-3"><span class="text-muted">Kembalian</span><span id="kembalian" class="fw-semibold text-success">Rp 0</span></div>
                <button id="bayar" class="btn btn-success btn-lg w-100" disabled><i class="fas fa-credit-card me-2"></i>Selesaikan Bayar</button>
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
    const dRaw = parseMoney($g('diskon').value);
    const pRaw = parseMoney($g('pajak').value);
    const dMode = $g('diskonModeBtn')?.dataset.mode || 'rp';
    const pMode = $g('pajakModeBtn')?.dataset.mode || 'rp';
    const d = dMode === 'pct' ? Math.round(sub * Math.min(dRaw, 100) / 100) : dRaw;
    const p = pMode === 'pct' ? Math.round(sub * Math.min(pRaw, 100) / 100) : pRaw;
    const grand = Math.max(0, sub - d + p);
    $g('subTotal').textContent = fmt(sub);
    $g('grandTotal').textContent = fmt(grand);
    const dibayar = parseMoney($g('dibayar').value);
    $g('kembalian').textContent = fmt(Math.max(0, dibayar - grand));
    $g('bayar').disabled = !cart.length || dibayar < grand;
    window._diskonRp = d;
    window._pajakRp = p;
}

function parseMoney(v) { return +String(v).replace(/[^\d]/g, '') || 0; }
function formatMoney(n) { return Number(n).toLocaleString('id-ID'); }
function attachMoneyInput(el) {
    el.addEventListener('input', () => {
        const raw = parseMoney(el.value);
        const pos = el.selectionStart;
        const prevLen = el.value.length;
        el.value = raw === 0 ? '0' : formatMoney(raw);
        const newLen = el.value.length;
        try { el.setSelectionRange(pos + (newLen - prevLen), pos + (newLen - prevLen)); } catch (e) {}
        recalc();
    });
    el.addEventListener('focus', () => { if (el.value === '0') el.value = ''; });
    el.addEventListener('blur', () => { if (el.value === '') el.value = '0'; });
}
document.querySelectorAll('.money-input').forEach(attachMoneyInput);

['diskonModeBtn', 'pajakModeBtn'].forEach(id => {
    const btn = $g(id);
    if (!btn) return;
    btn.addEventListener('click', () => {
        const cur = btn.dataset.mode;
        const next = cur === 'rp' ? 'pct' : 'rp';
        btn.dataset.mode = next;
        btn.textContent = next === 'pct' ? '%' : 'Rp';
        btn.classList.toggle('btn-outline-secondary', next === 'rp');
        btn.classList.toggle('btn-warning', next === 'pct');
        recalc();
    });
});

@php
    $plgData = $pelanggans->map(function ($p) {
        return ['id' => $p->id, 'nama' => $p->nama, 'no_hp' => $p->no_hp ?? '', 'tipe' => $p->tipe ?? 'umum', 'diskon_persen' => $p->diskon_persen ?? 0];
    })->values()->all();
@endphp
const PELANGGAN = {!! json_encode($plgData) !!};
const plgSearch = $g('pelangganSearch'), plgList = $g('pelangganList'), plgInput = $g('pelanggan');
function renderPelanggan(q) {
    const ql = (q || '').toLowerCase();
    const items = [{id: '', nama: 'Pembeli Umum', no_hp: '', tipe: ''}, ...PELANGGAN]
        .filter(p => !ql || p.nama.toLowerCase().includes(ql) || p.no_hp.toLowerCase().includes(ql));
    if (!items.length) { plgList.innerHTML = '<div class="px-3 py-2 text-muted small">Tidak ditemukan</div>'; return; }
    plgList.innerHTML = items.slice(0, 50).map(p =>
        `<div class="px-3 py-2 border-bottom" style="cursor:pointer" data-id="${p.id}" data-nm="${p.nama}">
            <strong>${p.nama}</strong> ${p.tipe === 'member' ? '<span class="badge bg-primary ms-1" style="font-size:9px">MEMBER</span>' : ''}
            ${p.no_hp ? `<small class="text-muted ms-2">${p.no_hp}</small>` : ''}
        </div>`).join('');
    plgList.querySelectorAll('[data-id]').forEach(el => el.onclick = () => {
        plgInput.value = el.dataset.id;
        plgSearch.value = el.dataset.nm;
        plgList.classList.add('d-none');
        applyMemberDiscount(el.dataset.id);
    });
}
plgSearch.addEventListener('focus', () => { renderPelanggan(plgSearch.value); plgList.classList.remove('d-none'); });
plgSearch.addEventListener('input', () => { renderPelanggan(plgSearch.value); plgList.classList.remove('d-none'); });
document.addEventListener('click', (e) => { if (!plgSearch.contains(e.target) && !plgList.contains(e.target)) plgList.classList.add('d-none'); });

function applyMemberDiscount(pid) {
    if (!pid) return;
    const p = PELANGGAN.find(x => String(x.id) === String(pid));
    if (p?.tipe === 'member') {
        const persen = (p.diskon_persen && p.diskon_persen > 0) ? p.diskon_persen : 5;
        $g('diskon').value = String(persen);
        const btn = $g('diskonModeBtn');
        btn.dataset.mode = 'pct'; btn.textContent = '%';
        btn.classList.remove('btn-outline-secondary'); btn.classList.add('btn-warning');
        recalc();
    }
}
['diskon','pajak','dibayar'].forEach(id => $g(id).addEventListener('input', recalc));

$g('bayar').addEventListener('click', async () => {
    const payload = {
        pelanggan_id: $g('pelanggan').value || null,
        metode_bayar: $g('metode').value,
        diskon: window._diskonRp ?? parseMoney($g('diskon').value),
        pajak: window._pajakRp ?? parseMoney($g('pajak').value),
        dibayar: parseMoney($g('dibayar').value),
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
    const k = e.key;
    const ctrl = e.ctrlKey || e.metaKey;
    if (k === 'F2' || (ctrl && (k === 'k' || k === 'K')) || k === '/') {
        e.preventDefault(); e.stopPropagation();
        $g('searchInput').focus(); $g('searchInput').select();
    }
    if (k === 'F4' || (ctrl && k === 'Enter')) {
        e.preventDefault(); e.stopPropagation();
        if (!$g('bayar').disabled) $g('bayar').click();
    }
    if (k === 'Escape') {
        if (document.activeElement === $g('searchInput')) {
            $g('searchInput').value = ''; $g('searchInput').blur();
        } else if (cart.length && confirm('Kosongkan keranjang?')) { cart = []; render(); }
    }
}, true);

let scanner = null;
let scanState = 'idle';
async function handleScan(code) {
    const data = await searchBarang(code);
    if (data.length === 1) { addToCart(data[0].id, data[0].nama, data[0].harga_jual, data[0].stok_current); navigator.vibrate?.(80); }
    else if (data.length > 1) { $g('searchInput').value = code; $g('searchInput').dispatchEvent(new Event('input')); }
    else { alert('Barcode tidak ditemukan: ' + code); }
}
async function forceReleaseCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({video: true, audio: false});
        stream.getTracks().forEach(t => t.stop());
    } catch (e) {}
}
async function closeScanner() {
    if (scanState === 'idle') return;
    scanState = 'stopping';
    try { if (scanner?.isScanning) await scanner.stop(); } catch (e) {}
    try { scanner?.clear?.(); } catch (e) {}
    await forceReleaseCamera();
    $g('scannerWrap').classList.add('d-none');
    scanner = null;
    scanState = 'idle';
}
$g('scanBtn').addEventListener('click', async () => {
    if (scanState === 'starting' || scanState === 'stopping') return;
    if (scanState === 'running') { await closeScanner(); return; }
    if (!window.Html5Qrcode) { alert('Scanner belum siap. Coba refresh halaman.'); return; }
    scanState = 'starting';
    $g('scannerWrap').classList.remove('d-none');
    if (!scanner) {
        try { scanner = new Html5Qrcode('scanner'); }
        catch (e) { scanState = 'idle'; alert('Init gagal: ' + (e?.message || e)); $g('scannerWrap').classList.add('d-none'); return; }
    }
    try {
        await scanner.start({facingMode: 'environment'}, {fps: 10, qrbox: {width: 250, height: 150}},
            async (text) => {
                await closeScanner();
                handleScan(text.trim());
            }, () => {});
        scanState = 'running';
    } catch (e) {
        scanState = 'idle';
        const name = e?.name || '';
        const msg = e?.message || String(e);
        let saran = '';
        if (name === 'NotAllowedError' || /permission|denied/i.test(msg)) {
            saran = '🔒 Akses kamera ditolak. Klik ikon gembok di address bar > Camera > Allow > refresh.';
        } else if (name === 'NotFoundError') {
            saran = '📷 Tidak terdeteksi kamera di perangkat ini.';
        } else if (name === 'NotReadableError' || /track start|in use|busy/i.test(msg)) {
            saran = '⚠️ Kamera dipakai aplikasi lain. Tutup app Kamera/WhatsApp/Zoom yang aktif.';
        } else if (/scan is ongoing|already.*started|clear/i.test(msg)) {
            try { scanner = new Html5Qrcode('scanner'); } catch (e) {}
            saran = 'Scanner sedang sibuk. Tunggu 2 detik lalu coba lagi.';
        } else {
            saran = 'Error kamera: ' + msg;
        }
        alert(saran);
        $g('scannerWrap').classList.add('d-none');
    }
});
$g('stopScan').addEventListener('click', async () => { await closeScanner(); });
$g('stopScan').addEventListener('click', async () => {
    try { if (scanner?.isScanning) await scanner.stop(); } catch (e) {}
    $g('scannerWrap').classList.add('d-none');
});
</script>
@endpush
