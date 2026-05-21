<div class="tab-pane fade" id="tab-masuk">
    <h4 class="fw-bold mb-3"><i class="fas fa-truck-loading text-primary me-2"></i>Catat Barang Masuk dari Pemasok</h4>
    <p class="text-muted">Saat barang datang dari pemasok, catat di sini supaya stok bertambah otomatis.</p>

    <div class="panduan-step">
        <span class="step-num">1</span> Buka menu <strong>Stok Barang &gt; Barang Masuk</strong>
    </div>
    <div class="panduan-step">
        <span class="step-num">2</span> Klik <strong>+ Catat Barang Masuk</strong>
    </div>
    <div class="panduan-step">
        <span class="step-num">3</span> Isi header:
        <ul class="mt-2 mb-0 small">
            <li><strong>Tanggal Masuk</strong> - tanggal barang sampai</li>
            <li><strong>Pemasok</strong> - pilih dari mana barangnya</li>
            <li><strong>Cara Bayar</strong> - Tunai / Transfer Bank / Hutang (Tempo)</li>
            <li><strong>Catatan</strong> (opsional) - misal: "Kiriman bulan Mei, Faktur No.123"</li>
        </ul>
    </div>
    <div class="panduan-step">
        <span class="step-num">4</span> Klik <strong>+ Tambah Baris</strong> untuk setiap barang yang datang. Isi:
        <ul class="mt-2 mb-0 small">
            <li><strong>Barang</strong> - pilih dari daftar (harga beli auto-isi dari master)</li>
            <li><strong>Qty</strong> - jumlah yang masuk</li>
            <li><strong>Harga Beli</strong> - modal per satuan (bisa diubah kalau harga pemasok beda dari biasa)</li>
            <li><strong>No Batch</strong> (opsional) - kode batch dari pemasok, contoh "B240501"</li>
            <li><strong>Tgl Kadaluarsa</strong> - <span class="text-danger">PENTING</span> untuk produk yang punya tanggal expired (susu, makanan, obat). Sistem otomatis akan menjual barang yang lebih cepat expired duluan (FEFO).</li>
        </ul>
    </div>
    <div class="panduan-step">
        <span class="step-num">5</span> Isi <strong>Dibayar</strong>, klik <strong>Simpan Draft</strong>
    </div>
    <div class="panduan-step">
        <span class="step-num">6</span> Di halaman detail, klik tombol hijau <strong>Terima Barang</strong> &gt; stok otomatis bertambah ke daftar barang.
    </div>

    <div class="panduan-tip">
        💡 <strong>Kenapa pakai sistem Draft -&gt; Terima?</strong><br>
        Saat input awal jadi <strong>Draft</strong>, stok belum nambah. Kalau ada salah ketik, masih bisa Batal. Setelah barang fisik benar-benar masuk gudang, baru klik <strong>Terima Barang</strong> &gt; stok masuk resmi + tercatat permanen.
    </div>

    <div class="panduan-tip">
        💡 <strong>Tips kontrol kadaluarsa:</strong> Selalu isi Tgl Kadaluarsa untuk produk yang punya. Halaman <strong>Cek Kadaluarsa</strong> akan otomatis tampilkan stok berdasar urutan expired terdekat, bisa diskon agresif sebelum kadaluarsa.
    </div>
</div>
