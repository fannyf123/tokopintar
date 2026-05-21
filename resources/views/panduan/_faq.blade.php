<div class="tab-pane fade" id="tab-faq">
    <h4 class="fw-bold mb-3"><i class="fas fa-question-circle text-primary me-2"></i>Pertanyaan Sering Ditanya (FAQ)</h4>

    <div class="accordion" id="faqAcc">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                    Bagaimana kalau barcode produk tidak ketemu saat scan?
                </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Berarti barang itu belum ada di sistem. Solusinya: tambahkan dulu di <strong>Daftar Barang → + Tambah Barang Baru</strong>. Saat tambah, tap tombol 📷 di field Barcode, scan kemasan asli, lalu isi nama/harga. Setelah itu, scan di Kasir akan kenali barangnya.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                    Stok di sistem beda dengan stok fisik di gudang, bagaimana?
                </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Lakukan opname (hitung manual) → bandingkan dengan sistem → koreksi lewat <strong>Penyesuaian Stok</strong>. Pilih alasan "Tambah/Kurangi stok (penyesuaian)" dan tulis catatan "Hasil opname tanggal X". Sistem akan koreksi dan tetap simpan histori.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                    Kalau salah input transaksi penjualan, bisa dibatalkan?
                </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Transaksi yang sudah selesai TIDAK bisa dihapus (anti penipuan). Solusinya: catat lewat <strong>Penyesuaian Stok</strong> dengan alasan "Retur dari pelanggan" + jumlah yang dikembalikan. Stok auto-balik, dan ada audit trail.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                    Kenapa Saran Toko kosong / belum keluar?
                </button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Sistem perlu data minimal 14 hari transaksi untuk analisa akurat. Kalau baru pakai &lt;14 hari, sebagian besar barang akan masuk kelas <code>NEW</code>. Klik tombol <strong>Hitung Ulang Sekarang</strong> di halaman Saran Toko setelah ada lebih banyak transaksi.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                    Aplikasi pertama kali buka pagi hari lambat (30-60 detik), kenapa?
                </button>
            </h2>
            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Aplikasi pakai hosting gratis yang "tidur" kalau tidak diakses lama. Saat pertama buka pagi, server perlu bangun dulu (~30-60 detik). Setelah itu cepat terus selama jam toko aktif. Kalau mau hilangkan keterlambatan ini, bisa di-keep-alive otomatis (sudah dipasang) atau upgrade hosting berbayar.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                    Bisa cetak struk pakai printer thermal 80mm?
                </button>
            </h2>
            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Bisa. Setelah transaksi selesai, klik <strong>Cetak Struk</strong> → browser akan auto-trigger print dialog. Pastikan printer thermal sudah terpasang sebagai default printer. Layout struk sudah di-format untuk lebar 76mm (cocok kertas 80mm).
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                    Saya lupa password, bagaimana reset?
                </button>
            </h2>
            <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Untuk akun selain admin: minta admin reset lewat <strong>Pengaturan</strong> (kalau ada modul user management nantinya). Untuk admin yang lupa password: kontak developer untuk reset langsung di database.
                </div>
            </div>
        </div>

    </div>
</div>
