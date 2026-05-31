<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;

class ProductCatalogService
{
    /**
     * Katalog produk umum warung/toko kelontong Indonesia.
     * Format: 'Kategori' => [ [nama, satuan], ... ]
     * Harga sengaja dikosongkan (0) — diisi sendiri oleh pemilik toko.
     */
    public function catalog(): array
    {
        return [
            'Mie Instan' => [
                ['Indomie Goreng', 'pcs'],
                ['Indomie Kuah Ayam Bawang', 'pcs'],
                ['Indomie Soto', 'pcs'],
                ['Indomie Kari Ayam', 'pcs'],
                ['Mie Sedaap Goreng', 'pcs'],
                ['Mie Sedaap Kuah Soto', 'pcs'],
                ['Sarimi Isi 2', 'pcs'],
                ['Supermi Goreng', 'pcs'],
                ['Pop Mie Ayam', 'cup'],
                ['Pop Mie Baso', 'cup'],
            ],
            'Minuman' => [
                ['Aqua Botol 600ml', 'botol'],
                ['Aqua Gelas 240ml', 'cup'],
                ['Aqua Galon 19L', 'galon'],
                ['Le Minerale 600ml', 'botol'],
                ['Teh Botol Sosro 350ml', 'botol'],
                ['Teh Pucuk Harum 350ml', 'botol'],
                ['Teh Gelas', 'cup'],
                ['Coca-Cola 390ml', 'botol'],
                ['Sprite 390ml', 'botol'],
                ['Fanta 390ml', 'botol'],
                ['Floridina 350ml', 'botol'],
                ['Pocari Sweat 350ml', 'botol'],
                ['Mizone 500ml', 'botol'],
                ['Kratingdaeng', 'botol'],
                ['Extra Joss Sachet', 'sachet'],
                ['Kuku Bima Energi Sachet', 'sachet'],
                ['Good Day Kopi Kaleng', 'kaleng'],
                ['Nescafe Kaleng', 'kaleng'],
            ],
            'Kopi & Teh' => [
                ['Kopi Kapal Api Sachet', 'sachet'],
                ['Kopi ABC Susu Sachet', 'sachet'],
                ['Good Day Cappuccino Sachet', 'sachet'],
                ['Luwak White Koffie Sachet', 'sachet'],
                ['Nescafe Classic Sachet', 'sachet'],
                ['Torabika Cappuccino Sachet', 'sachet'],
                ['Teh Celup Sariwangi isi 25', 'box'],
                ['Teh Celup Sosro isi 25', 'box'],
                ['Teh Poci', 'bungkus'],
            ],
            'Susu' => [
                ['Susu Frisian Flag Kental Manis', 'kaleng'],
                ['Frisian Flag Kental Manis Sachet', 'sachet'],
                ['Susu Indomilk Kental Manis', 'kaleng'],
                ['Ultra Milk Coklat 250ml', 'kotak'],
                ['Ultra Milk Full Cream 250ml', 'kotak'],
                ['Frisian Flag UHT 225ml', 'kotak'],
                ['Susu Bendera Bubuk Sachet', 'sachet'],
                ['Dancow Bubuk Sachet', 'sachet'],
                ['SGM Eksplor 1+', 'box'],
                ['Milo Sachet', 'sachet'],
            ],
            'Sembako' => [
                ['Beras Premium', 'kg'],
                ['Beras Medium', 'kg'],
                ['Gula Pasir', 'kg'],
                ['Minyak Goreng Bimoli 1L', 'botol'],
                ['Minyak Goreng Bimoli 2L', 'botol'],
                ['Minyak Goreng Sania 1L', 'botol'],
                ['Minyak Goreng Curah', 'kg'],
                ['Tepung Terigu Segitiga Biru 1kg', 'bungkus'],
                ['Tepung Beras Rosebrand', 'bungkus'],
                ['Telur Ayam', 'kg'],
                ['Garam Dapur', 'bungkus'],
                ['Mentega Blue Band 200gr', 'pcs'],
            ],
            'Bumbu Dapur' => [
                ['Kecap Manis ABC 275ml', 'botol'],
                ['Kecap Manis Bango 275ml', 'botol'],
                ['Saus Sambal ABC 335ml', 'botol'],
                ['Saus Tomat ABC', 'botol'],
                ['Royco Ayam Sachet', 'sachet'],
                ['Masako Ayam Sachet', 'sachet'],
                ['Sasa MSG Sachet', 'sachet'],
                ['Ladaku Merica Sachet', 'sachet'],
                ['Bumbu Indofood Nasi Goreng', 'sachet'],
                ['Kara Santan 65ml', 'sachet'],
                ['Saus Tiram Saori', 'botol'],
            ],
            'Makanan Ringan' => [
                ['Chitato Sapi Panggang', 'pcs'],
                ['Lays Rumput Laut', 'pcs'],
                ['Qtela Singkong', 'pcs'],
                ['Taro Net', 'pcs'],
                ['Chiki Balls', 'pcs'],
                ['Cheetos', 'pcs'],
                ['Piattos', 'pcs'],
                ['Potabee', 'pcs'],
                ['Kacang Garuda', 'bungkus'],
                ['Kacang Dua Kelinci', 'bungkus'],
                ['Sukro', 'bungkus'],
                ['Momogi', 'pcs'],
                ['Chiki Twist', 'pcs'],
            ],
            'Biskuit & Wafer' => [
                ['Roma Malkist Crackers', 'bungkus'],
                ['Roma Kelapa', 'bungkus'],
                ['Biskuat Coklat', 'bungkus'],
                ['Oreo', 'bungkus'],
                ['Tango Wafer', 'bungkus'],
                ['Nissin Wafer', 'bungkus'],
                ['Gery Saluut', 'pcs'],
                ['Khong Guan Kaleng', 'kaleng'],
                ['Beng Beng', 'pcs'],
                ['SilverQueen', 'pcs'],
                ['Chocolatos', 'pcs'],
                ['Better Wafer', 'bungkus'],
            ],
            'Rokok' => [
                ['Sampoerna Mild 16', 'bungkus'],
                ['Sampoerna Mild 12', 'bungkus'],
                ['Gudang Garam Surya 12', 'bungkus'],
                ['Gudang Garam Surya 16', 'bungkus'],
                ['Djarum Super 12', 'bungkus'],
                ['Marlboro Merah', 'bungkus'],
                ['Marlboro Filter Black', 'bungkus'],
                ['LA Lights', 'bungkus'],
                ['Dunhill Mild', 'bungkus'],
                ['Magnum Filter', 'bungkus'],
            ],
            'Permen & Coklat' => [
                ['Permen Kopiko', 'bungkus'],
                ['Permen Mentos', 'rol'],
                ['Permen Relaxa', 'bungkus'],
                ['Permen Fox', 'kaleng'],
                ['Permen Kis', 'bungkus'],
                ['Coklat SilverQueen Besar', 'pcs'],
                ['Coklat Delfi', 'pcs'],
                ['Permen Yupi', 'bungkus'],
                ['Mentos Gum', 'pcs'],
            ],
            'Perawatan Tubuh' => [
                ['Sabun Lifebuoy Batang', 'pcs'],
                ['Sabun Lux Batang', 'pcs'],
                ['Shampoo Sunsilk Sachet', 'sachet'],
                ['Shampoo Clear Sachet', 'sachet'],
                ['Shampoo Pantene Sachet', 'sachet'],
                ['Pasta Gigi Pepsodent 75gr', 'pcs'],
                ['Pasta Gigi Close Up', 'pcs'],
                ['Sikat Gigi Formula', 'pcs'],
                ['Pembalut Charm', 'bungkus'],
                ['Sabun Cuci Muka Garnier Sachet', 'sachet'],
                ['Deodorant Rexona', 'pcs'],
                ['Bedak Marcks', 'pcs'],
            ],
            'Kebersihan Rumah' => [
                ['Sabun Cuci Rinso Sachet', 'sachet'],
                ['Sabun Cuci Daia Sachet', 'sachet'],
                ['Sabun Cuci So Klin Sachet', 'sachet'],
                ['Sunlight Pencuci Piring 755ml', 'botol'],
                ['Sunlight Sachet', 'sachet'],
                ['Pemutih Bayclin', 'botol'],
                ['Pewangi Molto Sachet', 'sachet'],
                ['Super Pel 770ml', 'botol'],
                ['Wipol Karbol', 'botol'],
                ['Baygon Spray', 'kaleng'],
                ['Hit Obat Nyamuk Bakar', 'bungkus'],
                ['Tisu Paseo', 'pack'],
            ],
            'Kesehatan' => [
                ['Tolak Angin Sachet', 'sachet'],
                ['Antangin JRG Sachet', 'sachet'],
                ['Bodrex Tablet', 'strip'],
                ['Paramex Tablet', 'strip'],
                ['Panadol Tablet', 'strip'],
                ['Promag Tablet', 'strip'],
                ['Komix Sachet', 'sachet'],
                ['Minyak Kayu Putih Cap Lang 60ml', 'botol'],
                ['Hansaplast', 'box'],
                ['Vitamin C IPI', 'botol'],
                ['Betadine 15ml', 'botol'],
            ],
            'Perlengkapan Bayi' => [
                ['Popok Merries M', 'bungkus'],
                ['Popok Sweety M', 'bungkus'],
                ['Popok Mamy Poko M', 'bungkus'],
                ['Tisu Basah Sweety', 'pack'],
                ['Bedak Bayi Cussons', 'pcs'],
                ['Sabun Bayi Cussons', 'botol'],
                ['Minyak Telon Konicare 60ml', 'botol'],
            ],
            'Lainnya' => [
                ['Korek Api Gas', 'pcs'],
                ['Baterai ABC AA', 'pasang'],
                ['Baterai ABC AAA', 'pasang'],
                ['Pulpen Standard', 'pcs'],
                ['Buku Tulis 38 lembar', 'pcs'],
                ['Lilin', 'bungkus'],
                ['Kantong Plastik', 'pack'],
                ['Tali Rafia', 'rol'],
                ['Sabun Colek Sachet', 'sachet'],
                ['Spons Cuci Piring', 'pcs'],
            ],
            'Frozen Food' => [
                ['Sosis So Nice', 'bungkus'],
                ['Sosis Champ', 'bungkus'],
                ['Nugget Fiesta', 'bungkus'],
                ['Nugget Champ', 'bungkus'],
                ['Bakso Bernardi', 'bungkus'],
                ['Kentang Goreng Frozen', 'bungkus'],
                ['Dimsum Frozen', 'bungkus'],
                ['Sosis Kanzler', 'bungkus'],
                ['Ebi Furai', 'bungkus'],
                ['Otak-otak Frozen', 'bungkus'],
            ],
            'Roti & Kue' => [
                ['Roti Tawar Sari Roti', 'bungkus'],
                ['Roti Sobek Sari Roti', 'bungkus'],
                ['Roti Sisir', 'bungkus'],
                ['Roti Boy', 'pcs'],
                ['Roti Kukus', 'pcs'],
                ['Pia Kacang Hijau', 'bungkus'],
                ['Bika Ambon', 'pcs'],
                ['Bolu Gulung', 'pcs'],
                ['Donat Kemasan', 'bungkus'],
            ],
            'Bahan Kue' => [
                ['Tepung Terigu Cakra Kembar 1kg', 'bungkus'],
                ['Tepung Maizena Maizenaku', 'bungkus'],
                ['Tepung Tapioka', 'bungkus'],
                ['Ragi Fermipan', 'sachet'],
                ['Baking Powder Koepoe', 'pcs'],
                ['Soda Kue', 'pcs'],
                ['Coklat Bubuk', 'bungkus'],
                ['Susu Bubuk Full Cream', 'bungkus'],
                ['Vanili Bubuk', 'sachet'],
                ['Pewarna Makanan Koepoe', 'botol'],
                ['Meses Ceres', 'bungkus'],
                ['SKM Putih Indomilk', 'kaleng'],
            ],
            'Minuman Serbuk' => [
                ['Nutrisari Jeruk Sachet', 'sachet'],
                ['Marimas Sachet', 'sachet'],
                ['Pop Ice Sachet', 'sachet'],
                ['Jas Jus Sachet', 'sachet'],
                ['Energen Coklat Sachet', 'sachet'],
                ['Energen Vanilla Sachet', 'sachet'],
                ['Milo Aktiv-Go Sachet', 'sachet'],
                ['Chocolatos Drink Sachet', 'sachet'],
                ['Teh Sisri Sachet', 'sachet'],
                ['Segar Sari Sachet', 'sachet'],
            ],
            'Alat Tulis & Sekolah' => [
                ['Pensil 2B Faber Castell', 'pcs'],
                ['Penghapus', 'pcs'],
                ['Penggaris 30cm', 'pcs'],
                ['Spidol Snowman', 'pcs'],
                ['Buku Tulis Sidu 38lbr', 'pcs'],
                ['Buku Gambar A4', 'pcs'],
                ['Pulpen Pilot', 'pcs'],
                ['Tip-Ex Kertas', 'pcs'],
                ['Lem Kertas Glukol', 'pcs'],
                ['Stabilo', 'pcs'],
                ['Amplop Putih', 'pcs'],
                ['Map Plastik', 'pcs'],
            ],
            'Rumah Tangga' => [
                ['Gas LPG 3kg (isi ulang)', 'tabung'],
                ['Tabung Gas 3kg', 'tabung'],
                ['Sapu Lantai', 'pcs'],
                ['Pel Lantai', 'pcs'],
                ['Ember Plastik', 'pcs'],
                ['Gayung', 'pcs'],
                ['Sendok Plastik (pack)', 'pack'],
                ['Gelas Plastik (pack)', 'pack'],
                ['Aluminium Foil', 'rol'],
                ['Plastik Wrap', 'rol'],
                ['Tusuk Gigi', 'bungkus'],
                ['Sedotan', 'bungkus'],
            ],
            'Kosmetik & Perawatan' => [
                ['Hand Body Citra Sachet', 'sachet'],
                ['Hand Body Vaseline 100ml', 'botol'],
                ['Lipstik Wardah', 'pcs'],
                ['Bedak Wardah', 'pcs'],
                ['Pelembab Pond\'s Sachet', 'sachet'],
                ['Sabun Muka Biore', 'botol'],
                ['Minyak Rambut Pomade', 'pcs'],
                ['Hair Tonic', 'botol'],
                ['Kapas Kecantikan', 'bungkus'],
                ['Cotton Bud', 'bungkus'],
                ['Tisu Wajah', 'pack'],
                ['Parfum Roll On', 'pcs'],
            ],
            'Makanan Kaleng' => [
                ['Sarden ABC Kaleng', 'kaleng'],
                ['Sarden Botan Kaleng', 'kaleng'],
                ['Kornet Pronas', 'kaleng'],
                ['Kornet CIP', 'kaleng'],
                ['Sosis Kaleng', 'kaleng'],
                ['Buah Kaleng', 'kaleng'],
                ['Jamur Kaleng', 'kaleng'],
            ],
            'Bumbu Instan' => [
                ['Bumbu Rendang Indofood', 'sachet'],
                ['Bumbu Opor Indofood', 'sachet'],
                ['Bumbu Soto Indofood', 'sachet'],
                ['Bumbu Nasi Goreng Sajiku', 'sachet'],
                ['Bumbu Ayam Goreng Sajiku', 'sachet'],
                ['Bumbu Sop Racik', 'sachet'],
                ['Tepung Bumbu Sajiku', 'bungkus'],
                ['Tepung Bumbu Sasa', 'bungkus'],
                ['Kecap Asin ABC', 'botol'],
                ['Sambal ABC Sachet', 'sachet'],
            ],
            'Es Krim' => [
                ['Es Krim Walls Cornetto', 'pcs'],
                ['Es Krim Walls Paddle Pop', 'pcs'],
                ['Es Krim Campina', 'pcs'],
                ['Es Krim Aice', 'pcs'],
                ['Es Krim Walls Populaire', 'pcs'],
                ['Es Lilin', 'pcs'],
            ],
            'Jajanan Anak' => [
                ['Chiki Ball', 'pcs'],
                ['Permen Karet Yosan', 'pcs'],
                ['Wafer Superstar', 'pcs'],
                ['Es Goyang', 'pcs'],
                ['Anak Mas', 'pcs'],
                ['Mie Remes Anak Mas', 'pcs'],
                ['Coklat Koin', 'pcs'],
                ['Permen Davos', 'bungkus'],
                ['Gulas Kapas', 'pcs'],
            ],
            'Perlengkapan Mandi' => [
                ['Sikat Gigi Pepsodent', 'pcs'],
                ['Sabun Giv Batang', 'pcs'],
                ['Sabun Nuvo Batang', 'pcs'],
                ['Sabun Cair Lifebuoy 250ml', 'botol'],
                ['Shampoo Head & Shoulders Sachet', 'sachet'],
                ['Shampoo Dove Sachet', 'sachet'],
                ['Handuk Kecil', 'pcs'],
                ['Sabun Cuci Muka Pria', 'botol'],
                ['Pasta Gigi Ciptadent', 'pcs'],
                ['Pisau Cukur Gillette', 'pcs'],
            ],
            'Obat Warung' => [
                ['Bodrexin Anak', 'strip'],
                ['Mixagrip', 'strip'],
                ['Procold Flu', 'strip'],
                ['Decolgen', 'strip'],
                ['Oskadon', 'strip'],
                ['Entrostop', 'strip'],
                ['Diapet', 'strip'],
                ['Konidin', 'strip'],
                ['Woods Obat Batuk', 'botol'],
                ['Vicks Inhaler', 'pcs'],
                ['Counterpain', 'pcs'],
                ['Freshcare Minyak Angin', 'botol'],
                ['Insto Tetes Mata', 'botol'],
                ['Tolak Linu Sachet', 'sachet'],
            ],
            'Listrik & Elektronik' => [
                ['Lampu LED Philips 5W', 'pcs'],
                ['Lampu LED Philips 9W', 'pcs'],
                ['Baterai ABC 9V', 'pcs'],
                ['Baterai Jam', 'pcs'],
                ['Kabel Charger Micro USB', 'pcs'],
                ['Kabel Charger Type-C', 'pcs'],
                ['Earphone', 'pcs'],
                ['Steker Listrik', 'pcs'],
                ['Isolasi Listrik', 'pcs'],
                ['Senter Kecil', 'pcs'],
            ],
            'Pulsa & Voucher' => [
                ['Voucher Pulsa 5.000', 'pcs'],
                ['Voucher Pulsa 10.000', 'pcs'],
                ['Voucher Pulsa 25.000', 'pcs'],
                ['Voucher Pulsa 50.000', 'pcs'],
                ['Voucher Pulsa 100.000', 'pcs'],
                ['Kartu Perdana Telkomsel', 'pcs'],
                ['Kartu Perdana XL', 'pcs'],
                ['Voucher Kuota Data', 'pcs'],
                ['Token Listrik 20.000', 'pcs'],
                ['Token Listrik 50.000', 'pcs'],
            ],
            'Sembako Tambahan' => [
                ['Beras Ketan Putih', 'kg'],
                ['Kacang Hijau', 'kg'],
                ['Kacang Tanah', 'kg'],
                ['Kacang Kedelai', 'kg'],
                ['Kedelai Bubuk', 'kg'],
                ['Mie Telur Cap 3 Ayam', 'bungkus'],
                ['Bihun Jagung', 'bungkus'],
                ['Soun', 'bungkus'],
                ['Kerupuk Mentah', 'bungkus'],
                ['Gula Merah', 'kg'],
                ['Gula Batu', 'kg'],
                ['Madu Botol', 'botol'],
            ],
            'Bumbu Segar' => [
                ['Bawang Merah', 'kg'],
                ['Bawang Putih', 'kg'],
                ['Cabai Merah', 'kg'],
                ['Cabai Rawit', 'kg'],
                ['Tomat', 'kg'],
                ['Kentang', 'kg'],
                ['Jahe', 'ons'],
                ['Kunyit', 'ons'],
                ['Daun Salam', 'ikat'],
                ['Serai', 'ikat'],
            ],
            'Snack Tradisional' => [
                ['Kerupuk Udang', 'bungkus'],
                ['Kerupuk Bawang', 'bungkus'],
                ['Rempeyek Kacang', 'bungkus'],
                ['Keripik Singkong', 'bungkus'],
                ['Keripik Pisang', 'bungkus'],
                ['Kacang Bawang', 'bungkus'],
                ['Emping Melinjo', 'bungkus'],
                ['Stik Keju', 'bungkus'],
                ['Marning Jagung', 'bungkus'],
                ['Opak', 'bungkus'],
            ],
            'Minuman Tambahan' => [
                ['Sprite Kaleng 330ml', 'kaleng'],
                ['Coca-Cola Kaleng 330ml', 'kaleng'],
                ['Fanta Kaleng 330ml', 'kaleng'],
                ['Larutan Cap Kaki Tiga', 'botol'],
                ['You C1000', 'botol'],
                ['Cola-cola 1.5L', 'botol'],
                ['Sprite 1.5L', 'botol'],
                ['Teh Kotak Ultra', 'kotak'],
                ['Buavita Jus', 'kotak'],
                ['Yakult', 'botol'],
                ['Cimory Yogurt', 'botol'],
                ['Air Soda', 'botol'],
            ],
            'Perlengkapan Bayi Lain' => [
                ['Sufor Bebelac', 'box'],
                ['Sufor Lactogen', 'box'],
                ['Bubur Bayi Promina', 'box'],
                ['Bubur Bayi Cerelac', 'box'],
                ['Biskuit Bayi Milna', 'box'],
                ['Sabun Cair Bayi Zwitsal', 'botol'],
                ['Minyak Telon Cap Lang', 'botol'],
                ['Cotton Bud Bayi', 'bungkus'],
                ['Botol Susu Bayi', 'pcs'],
                ['Dot Bayi', 'pcs'],
            ],
            'Camilan Sehat' => [
                ['Kurma', 'kg'],
                ['Kacang Almond', 'bungkus'],
                ['Kacang Mete', 'bungkus'],
                ['Granola Bar', 'pcs'],
                ['Roti Gandum', 'bungkus'],
                ['Oatmeal Quaker', 'box'],
                ['Madu Sachet', 'sachet'],
                ['Raisin Kismis', 'bungkus'],
            ],
        ];
    }

    /**
     * Ringkasan: jumlah produk per kategori + total.
     */
    public function summary(): array
    {
        $out = [];
        $total = 0;
        foreach ($this->catalog() as $kategori => $produk) {
            $out[$kategori] = count($produk);
            $total += count($produk);
        }
        return ['per_kategori' => $out, 'total' => $total];
    }

    /**
     * Impor katalog ke database. Hanya menambah produk yang BELUM ada
     * (berdasarkan nama, case-insensitive). Harga 0, stok 0, aktif=false
     * supaya tidak mengganggu kasir sampai pemilik mengisi harga.
     *
     * @return array{ditambah:int, dilewati:int, kategori_baru:int}
     */
    public function import(): array
    {
        $ditambah = 0;
        $dilewati = 0;
        $kategoriBaru = 0;

        // Nama barang yang sudah ada (lowercase) untuk cek duplikat
        $existing = Barang::query()->pluck('nama')
            ->map(fn ($n) => mb_strtolower(trim($n)))
            ->flip();

        foreach ($this->catalog() as $namaKategori => $produkList) {
            $kategori = Kategori::firstOrCreate(['nama' => $namaKategori]);
            if ($kategori->wasRecentlyCreated) {
                $kategoriBaru++;
            }

            foreach ($produkList as [$nama, $satuan]) {
                if ($existing->has(mb_strtolower(trim($nama)))) {
                    $dilewati++;
                    continue;
                }

                Barang::create([
                    'kode' => Barang::generateKode(),
                    'nama' => $nama,
                    'kategori_id' => $kategori->id,
                    'satuan' => $satuan,
                    'harga_beli' => 0,
                    'harga_jual' => 0,
                    'stok_min' => 0,
                    'stok_max' => 0,
                    'stok_current' => 0,
                    'aktif' => false,
                ]);
                $ditambah++;
                $existing->put(mb_strtolower(trim($nama)), true);
            }
        }

        return ['ditambah' => $ditambah, 'dilewati' => $dilewati, 'kategori_baru' => $kategoriBaru];
    }
}
