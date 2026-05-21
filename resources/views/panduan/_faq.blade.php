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
                    Berarti barang itu belum ada di sistem. Solusinya: tambahkan dulu di <strong>Daftar Barang &gt; + Tambah Barang Baru</strong>. Saat tambah, tap tombol 📷 di field Barcode, scan kemasan asli, lalu isi nama/harga. Setelah itu, scan di Kasir akan kenali barangnya. Atau coba tap 🔍 (cari online dari nama produk via OpenFoodFacts) jika produknya internasional.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                    Lampu indikator kamera laptop/HP masih nyala setelah scanner ditutup?
                </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Sistem sudah dikonfigurasi untuk release kamera dengan benar (kill MediaStream tracks). Kalau tetap nyala, kemungkinan ada cache browser - tap kamera lagi 1x atau refresh halaman (F5). Pastikan tidak ada aplikasi lain (WhatsApp/Zoom/aplikasi Kamera) yang juga aktif memakai kamera.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                    Stok di sistem beda dengan stok fisik di gudang, bagaimana?
                </button>
            </h2>
            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Lakukan opname (hitung manual) &gt; bandingkan dengan sistem &gt; koreksi lewat <strong>Penyesuaian Stok</strong>. Pilih alasan "Tambah/Kurangi stok (penyesuaian)" dan tulis catatan "Hasil opname tanggal X". Sistem akan koreksi dan tetap simpan histori.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                    Kalau salah input transaksi penjualan, bisa dibatalkan?
                </button>
            </h2>
            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Transaksi yang sudah selesai TIDAK bisa dihapus (anti penipuan). Solusinya: catat lewat <strong>Penyesuaian Stok</strong> dengan alasan "Retur dari pelanggan" + jumlah yang dikembalikan. Stok auto-balik, dan ada audit trail.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                    Cara kasih diskon khusus per pelanggan member?
                </button>
            </h2>
            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Buka menu <strong>Pelanggan &gt; Edit pelanggan</strong>. Ubah Tipe ke "Member", lalu isi <strong>Diskon Member (%)</strong> sesuai persen yang Anda inginkan (contoh: 10 untuk 10%). Saat pelanggan dipilih di Kasir, diskon otomatis terisi. Bisa beda-beda per pelanggan: Bronze 5%, Silver 10%, Gold 15%, dll.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                    Cara cari pelanggan kalau database sudah ratusan?
                </button>
            </h2>
            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Di Kasir, klik kotak Pelanggan &gt; ketik nama atau no HP &gt; daftar otomatis filter saat ngetik. Sistem cari berdasar substring (ketik "Bud" akan match Budi, Budiman, dll). Maksimal tampil 50 hasil per query supaya tetap cepat.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7">
                    Bisa potongan/pajak pakai persen, bukan nominal Rp?
                </button>
            </h2>
            <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Bisa. Di Kasir, di label Potongan/Pajak ada tombol kecil "<strong>Rp</strong>". Klik untuk switch jadi "<strong>%</strong>" (warna kuning). Saat mode persen aktif, isi 5 = potongan 5% dari subtotal. Server tetap menerima nominal Rp final, jadi data laporan selalu konsisten.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8">
                    Kenapa Saran Toko kosong / belum keluar?
                </button>
            </h2>
            <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Sistem perlu data minimal 14 hari transaksi untuk analisa akurat. Kalau baru pakai &lt;14 hari, sebagian besar barang akan masuk kelas <code>NEW</code>. Klik tombol <strong>Hitung Ulang Sekarang</strong> di halaman Saran Toko setelah ada lebih banyak transaksi.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9">
                    Aplikasi pertama kali buka pagi hari lambat (30-60 detik), kenapa?
                </button>
            </h2>
            <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Aplikasi pakai hosting gratis yang "tidur" kalau tidak diakses lama. Saat pertama buka pagi, server perlu bangun dulu (~30-60 detik). Setelah itu cepat terus selama jam toko aktif. Sistem sudah pakai keep-alive cron-job tiap 14 menit di jam toko buka, jadi cold start cuma terjadi sekali sehari di pagi hari.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq10">
                    Bisa minimize sidebar untuk layar lebih lebar?
                </button>
            </h2>
            <div id="faq10" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Bisa. Klik tombol panah <i class="fas fa-angles-left"></i> di pojok kanan atas sidebar (sebelah brand TOKOPINTAR). Sidebar akan collapse jadi tinggal icon. Klik lagi untuk expand. Pilihan tersimpan di browser (tidak reset saat reload).
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq11">
                    Bisa cetak struk pakai printer thermal 80mm?
                </button>
            </h2>
            <div id="faq11" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Bisa. Setelah transaksi selesai, klik <strong>Cetak Struk</strong> &gt; browser akan auto-trigger print dialog. Pastikan printer thermal sudah terpasang sebagai default printer. Layout struk sudah di-format untuk lebar 76mm (cocok kertas 80mm).
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq12">
                    Bisa pakai Mode Gelap (Dark Mode)?
                </button>
            </h2>
            <div id="faq12" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Bisa. Klik tombol <strong>Mode Gelap</strong> di sidebar bagian bawah (atas Logout). Tampilan langsung berubah, dan pilihan tersimpan di browser. Klik lagi untuk balik ke Mode Terang.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq13">
                    Saya lupa password, bagaimana reset?
                </button>
            </h2>
            <div id="faq13" class="accordion-collapse collapse" data-bs-parent="#faqAcc">
                <div class="accordion-body small">
                    Untuk akun selain admin: minta admin reset lewat <strong>Pengaturan</strong> (kalau ada modul user management nantinya). Untuk admin yang lupa password: kontak developer untuk reset langsung di database.
                </div>
            </div>
        </div>

    </div>
</div>
