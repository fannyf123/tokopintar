<div class="tab-pane fade" id="tab-saran">
    <h4 class="fw-bold mb-3"><i class="fas fa-brain text-primary me-2"></i>Saran Toko (Smart Inventory)</h4>
    <p class="text-muted">Sistem otomatis analisa pola jualan Anda dan kasih saran cerdas. Bukan AI besar, tapi statistik deterministik — hasilnya bisa dipertanggungjawabkan dengan angka.</p>

    <h6 class="fw-bold mt-4 mb-2">Cara Pakai</h6>
    <div class="panduan-step">
        <span class="step-num">1</span> Buka <strong>Pengaturan → Saran Toko</strong>
    </div>
    <div class="panduan-step">
        <span class="step-num">2</span> Klik <strong>Hitung Ulang Sekarang</strong> kalau data belum muncul (perlu data jualan minimal 14 hari biar akurat).
    </div>

    <h6 class="fw-bold mt-4 mb-2">Yang Bisa Dilihat</h6>

    <div class="panduan-step">
        <strong>📊 Velocity (kecepatan jual)</strong>
        <p class="mb-0 mt-1 small text-muted">Angka rata-rata barang ini terjual per hari (misal: 2.5/hari = rata-rata 2-3 pcs per hari).</p>
    </div>
    <div class="panduan-step">
        <strong>📅 DoS (Days of Supply)</strong>
        <p class="mb-0 mt-1 small text-muted">Stok sekarang cukup untuk berapa hari ke depan? Kalau DoS = 3 berarti stok habis 3 hari lagi.</p>
    </div>
    <div class="panduan-step">
        <strong>🏷️ Kelas Barang</strong>
        <ul class="mt-1 mb-0 small">
            <li><span class="badge bg-success">FAST_MOVER</span> — Barang cepat laku, jangan kehabisan stok</li>
            <li><span class="badge bg-info">NORMAL</span> — Aman</li>
            <li><span class="badge bg-warning">SLOW_MOVER</span> — Lambat laku, kasih diskon</li>
            <li><span class="badge bg-danger">DEAD_STOCK</span> — Tidak laku berbulan-bulan, cuci gudang!</li>
            <li><span class="badge bg-secondary">NEW</span> — Belum cukup data (umur &lt;14 hari)</li>
        </ul>
    </div>
    <div class="panduan-step">
        <strong>🔤 Kelas ABC</strong>
        <p class="mb-0 mt-1 small text-muted">A = penyumbang 80% omzet (vital), B = sedang, C = kontribusi kecil. Fokus jaga stok kelas A.</p>
    </div>

    <h6 class="fw-bold mt-4 mb-2">🎯 Strategi Cross-Subsidy (Subsidi Silang)</h6>
    <p class="small text-muted">Sistem otomatis kelompokkan barang berdasar margin keuntungan:</p>
    <div class="panduan-step">
        <span class="badge bg-warning">LOSS_LEADER</span> margin ≤ 5% — barang pemikat (dijual pas modal/tipis untuk tarik pelanggan masuk toko)
    </div>
    <div class="panduan-step">
        <span class="badge bg-success">PROFIT_DRIVER</span> margin ≥ 25% — penyumbang untung utama
    </div>
    <div class="panduan-step">
        <span class="badge bg-info">BALANCED</span> margin 5-25% — wajar
    </div>

    <div class="panduan-tip">
        💡 <strong>Contoh praktek subsidi silang:</strong> Anda jual Indomie Rp 3.000 (modal Rp 2.800, untung tipis 7%). Tapi pelanggan yang beli Indomie sering ikut beli kopi sachet (margin 30%). Sistem akan kasih saran: "Tampilkan kopi dekat Indomie & buat bundling" — itulah cross-subsidy.
    </div>
</div>
