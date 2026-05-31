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
