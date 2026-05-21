<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $kat = Kategori::pluck('id', 'nama')->toArray();
        $sup = Supplier::pluck('id', 'nama')->toArray();
        $L = $sup['Distributor Lokal'] ?? null;

        // [kategori, supplier, nama, satuan, harga_beli, harga_jual, stok_min, stok_max]
        $items = [
            // Mie Instan
            ['Mie Instan', 'Indofood CBP', 'Indomie Goreng 85g', 'pcs', 2200, 3000, 24, 240],
            ['Mie Instan', 'Indofood CBP', 'Indomie Soto Mie 70g', 'pcs', 2200, 3000, 12, 120],
            ['Mie Instan', 'Indofood CBP', 'Indomie Kari Ayam 70g', 'pcs', 2200, 3000, 12, 120],
            ['Mie Instan', 'Indofood CBP', 'Indomie Ayam Bawang 70g', 'pcs', 2200, 3000, 12, 120],
            ['Mie Instan', 'Indofood CBP', 'Indomie Goreng Rendang 91g', 'pcs', 2500, 3500, 12, 120],
            ['Mie Instan', 'Indofood CBP', 'Pop Mie Ayam 75g', 'pcs', 4500, 6000, 6, 60],
            ['Mie Instan', 'Indofood CBP', 'Pop Mie Soto 75g', 'pcs', 4500, 6000, 6, 60],
            ['Mie Instan', 'Indofood CBP', 'Sarimi Soto Koya 75g', 'pcs', 1900, 2700, 12, 120],
            ['Mie Instan', 'Indofood CBP', 'Supermi Ayam Bawang 70g', 'pcs', 1900, 2700, 12, 120],
            ['Mie Instan', 'Wings Group', 'Mie Sedaap Goreng Original 90g', 'pcs', 2500, 3500, 12, 120],
            ['Mie Instan', 'Wings Group', 'Mie Sedaap Soto 75g', 'pcs', 2500, 3500, 12, 120],
            ['Mie Instan', 'Wings Group', 'Mie Sedaap Korean Spicy 75g', 'pcs', 2700, 3700, 12, 120],
            ['Mie Instan', 'Wings Group', 'Mie Sedaap Kari Spesial 75g', 'pcs', 2500, 3500, 12, 120],

            // Air Mineral & Minuman
            ['Air Mineral & Minuman', 'Aqua Danone', 'Aqua 600ml', 'pcs', 2800, 4000, 24, 240],
            ['Air Mineral & Minuman', 'Aqua Danone', 'Aqua 1500ml', 'pcs', 4500, 6000, 12, 120],
            ['Air Mineral & Minuman', 'Aqua Danone', 'Aqua Gelas 240ml', 'pcs', 600, 1000, 48, 480],
            ['Air Mineral & Minuman', 'Distributor Lokal', 'Le Minerale 600ml', 'pcs', 2700, 4000, 24, 240],
            ['Air Mineral & Minuman', 'Distributor Lokal', 'Le Minerale 1500ml', 'pcs', 4400, 6000, 12, 120],
            ['Air Mineral & Minuman', 'Distributor Lokal', 'Vit 600ml', 'pcs', 2500, 3500, 24, 240],
            ['Air Mineral & Minuman', 'Distributor Lokal', 'Teh Botol Sosro 350ml', 'pcs', 4000, 5500, 24, 240],
            ['Air Mineral & Minuman', 'Distributor Lokal', 'Teh Botol Sosro Kotak 200ml', 'pcs', 3000, 4000, 24, 240],
            ['Air Mineral & Minuman', 'Mayora Indah', 'Teh Pucuk Harum 350ml', 'pcs', 3500, 5000, 24, 240],
            ['Air Mineral & Minuman', 'Coca-Cola Indonesia', 'Frestea Lemon 350ml', 'pcs', 4000, 5500, 12, 120],
            ['Air Mineral & Minuman', 'Coca-Cola Indonesia', 'Coca Cola 390ml', 'pcs', 5500, 7500, 12, 120],
            ['Air Mineral & Minuman', 'Coca-Cola Indonesia', 'Fanta Strawberry 390ml', 'pcs', 5500, 7500, 12, 120],
            ['Air Mineral & Minuman', 'Coca-Cola Indonesia', 'Sprite 390ml', 'pcs', 5500, 7500, 12, 120],
            ['Air Mineral & Minuman', 'Distributor Lokal', 'Pocari Sweat 350ml', 'pcs', 6000, 8000, 12, 120],

            // Susu & UHT
            ['Susu & UHT', 'Ultrajaya', 'Ultra Milk Coklat 250ml', 'pcs', 5000, 7000, 12, 120],
            ['Susu & UHT', 'Ultrajaya', 'Ultra Milk Stroberi 250ml', 'pcs', 5000, 7000, 12, 120],
            ['Susu & UHT', 'Ultrajaya', 'Ultra Milk Plain 1L', 'pcs', 16000, 22000, 6, 60],
            ['Susu & UHT', 'Frisian Flag', 'Frisian Flag Kental Manis 370g', 'pcs', 11000, 14500, 12, 120],
            ['Susu & UHT', 'Frisian Flag', 'Frisian Flag UHT Coklat 200ml', 'pcs', 4500, 6000, 12, 120],
            ['Susu & UHT', 'Frisian Flag', 'Bendera SKM Sachet 42g', 'pcs', 1800, 2500, 24, 240],
            ['Susu & UHT', 'Distributor Lokal', 'Indomilk Kotak Coklat 190ml', 'pcs', 4500, 6000, 12, 120],
            ['Susu & UHT', 'Nestle Indonesia', 'Bear Brand 189ml', 'pcs', 8500, 11000, 12, 120],
            ['Susu & UHT', 'Nestle Indonesia', 'Milo UHT 200ml', 'pcs', 5500, 7500, 12, 120],
            ['Susu & UHT', 'Distributor Lokal', 'Cimory Yogurt Drink 250ml', 'pcs', 7500, 10000, 6, 60],

            // Kopi & Teh
            ['Kopi & Teh', 'Distributor Lokal', 'Kapal Api Special Mix 25g', 'pcs', 1500, 2200, 24, 240],
            ['Kopi & Teh', 'Distributor Lokal', 'Kapal Api Special 25g', 'pcs', 1500, 2200, 24, 240],
            ['Kopi & Teh', 'Nestle Indonesia', 'Nescafe Classic 100g', 'pcs', 28000, 36000, 6, 60],
            ['Kopi & Teh', 'Nestle Indonesia', 'Nescafe 3in1 Sachet 20g', 'pcs', 1500, 2200, 24, 240],
            ['Kopi & Teh', 'Mayora Indah', 'ABC Kopi Susu Sachet 31g', 'pcs', 1500, 2200, 24, 240],
            ['Kopi & Teh', 'Wings Group', 'Top Coffee Susu Sachet 31g', 'pcs', 1500, 2200, 24, 240],
            ['Kopi & Teh', 'Unilever Indonesia', 'Sariwangi Teh Celup 25 Bag', 'pcs', 6500, 9000, 12, 120],
            ['Kopi & Teh', 'Distributor Lokal', 'Tong Tji Teh Celup 25 Bag', 'pcs', 7000, 9500, 12, 120],
            ['Kopi & Teh', 'Mayora Indah', 'Energen Coklat Sachet 30g', 'pcs', 1700, 2500, 24, 240],
            ['Kopi & Teh', 'Distributor Lokal', 'Nutri Sari Jeruk Sachet 14g', 'pcs', 800, 1500, 24, 240],

            // Snack & Wafer
            ['Snack & Wafer', 'Distributor Lokal', 'Chitato Sapi Panggang 68g', 'pcs', 7000, 9500, 12, 120],
            ['Snack & Wafer', 'Distributor Lokal', 'Lays Original 68g', 'pcs', 7000, 9500, 12, 120],
            ['Snack & Wafer', 'Distributor Lokal', 'Taro Net Rumput Laut 23g', 'pcs', 1500, 2500, 24, 240],
            ['Snack & Wafer', 'Garudafood', 'Kacang Garuda 50g', 'pcs', 2500, 3500, 24, 240],
            ['Snack & Wafer', 'Mayora Indah', 'Beng Beng 20g', 'pcs', 1500, 2500, 24, 240],
            ['Snack & Wafer', 'Mayora Indah', 'Roma Kelapa 100g', 'pcs', 6000, 8500, 12, 120],
            ['Snack & Wafer', 'Mayora Indah', 'Roma Sandwich Coklat 124g', 'pcs', 8000, 11000, 12, 120],
            ['Snack & Wafer', 'Mayora Indah', 'Better 100g', 'pcs', 6500, 9000, 12, 120],
            ['Snack & Wafer', 'Mayora Indah', 'Oreo Original 137g', 'pcs', 7500, 10500, 12, 120],
            ['Snack & Wafer', 'Mayora Indah', 'Tango Wafer Vanilla 130g', 'pcs', 6500, 9000, 12, 120],
            ['Snack & Wafer', 'Distributor Lokal', 'Astor Coklat 40g', 'pcs', 4500, 6500, 12, 120],
            ['Snack & Wafer', 'Distributor Lokal', 'Khong Guan Mini 75g', 'pcs', 3500, 5000, 12, 120],
            ['Snack & Wafer', 'Distributor Lokal', 'Biskuat Susu 124g', 'pcs', 7000, 9500, 12, 120],
            ['Snack & Wafer', 'Distributor Lokal', 'Wafer Nissin Cream 56g', 'pcs', 3500, 5000, 24, 240],

            // Coklat & Permen
            ['Coklat & Permen', 'Distributor Lokal', 'Silver Queen Almond 65g', 'pcs', 9500, 13000, 12, 120],
            ['Coklat & Permen', 'Distributor Lokal', 'Silver Queen Cashew 65g', 'pcs', 9500, 13000, 12, 120],
            ['Coklat & Permen', 'Distributor Lokal', 'Cha Cha Tube 35g', 'pcs', 4500, 6500, 12, 120],
            ['Coklat & Permen', 'Distributor Lokal', 'Mentos Mint 29g', 'pcs', 4500, 6000, 24, 240],
            ['Coklat & Permen', 'Mayora Indah', 'Kopiko Coffee Candy 27g', 'pcs', 2500, 3500, 24, 240],
            ['Coklat & Permen', 'Distributor Lokal', 'Permen Sugus 6 stick', 'pcs', 1500, 2500, 24, 240],
            ['Coklat & Permen', 'Distributor Lokal', 'Permen Foxs 25g', 'pcs', 2000, 3000, 24, 240],
            ['Coklat & Permen', 'Distributor Lokal', 'Relaxa Permen 125g', 'pcs', 6000, 8500, 12, 120],

            // Bumbu & Saus
            ['Bumbu & Saus', 'Unilever Indonesia', 'Royco Ayam 8g Sachet', 'pcs', 500, 1000, 48, 480],
            ['Bumbu & Saus', 'Unilever Indonesia', 'Royco Sapi 8g Sachet', 'pcs', 500, 1000, 48, 480],
            ['Bumbu & Saus', 'Distributor Lokal', 'Masako Ayam 7g Sachet', 'pcs', 500, 1000, 48, 480],
            ['Bumbu & Saus', 'Distributor Lokal', 'Sasa MSG 5g Sachet', 'pcs', 400, 800, 48, 480],
            ['Bumbu & Saus', 'Unilever Indonesia', 'Bango Kecap Manis 220ml', 'pcs', 11000, 15000, 12, 120],
            ['Bumbu & Saus', 'Mayora Indah', 'ABC Kecap Manis 600ml', 'pcs', 22000, 28000, 6, 60],
            ['Bumbu & Saus', 'Mayora Indah', 'ABC Saus Sambal 135ml', 'pcs', 6000, 8500, 12, 120],
            ['Bumbu & Saus', 'Indofood CBP', 'Indofood Sambal Asli 135ml', 'pcs', 6000, 8500, 12, 120],
            ['Bumbu & Saus', 'Distributor Lokal', 'Heinz Saus Tomat 340g', 'pcs', 12000, 16500, 6, 60],
            ['Bumbu & Saus', 'Indofood CBP', 'Indofood Bumbu Nasi Goreng 22g', 'pcs', 1700, 2500, 24, 240],

            // Beras & Sembako
            ['Beras & Sembako', 'Distributor Lokal', 'Beras Ramos 5kg', 'kg', 70000, 78000, 6, 60],
            ['Beras & Sembako', 'Distributor Lokal', 'Beras Pandan Wangi 5kg', 'kg', 78000, 88000, 6, 60],
            ['Beras & Sembako', 'Distributor Lokal', 'Gula Pasir Gulaku 1kg', 'kg', 17000, 20000, 12, 120],
            ['Beras & Sembako', 'Indofood CBP', 'Minyak Bimoli 1L', 'pcs', 17500, 21000, 12, 120],
            ['Beras & Sembako', 'Distributor Lokal', 'Minyak Tropical 1L', 'pcs', 16500, 20000, 12, 120],
            ['Beras & Sembako', 'Indofood CBP', 'Tepung Segitiga Biru 1kg', 'kg', 11000, 14000, 12, 120],
            ['Beras & Sembako', 'Indofood CBP', 'Tepung Bumbu Sajiku 80g', 'pcs', 3000, 4500, 24, 240],
            ['Beras & Sembako', 'Distributor Lokal', 'Tepung Beras Rose Brand 500g', 'pcs', 7500, 10000, 12, 120],

            // Sabun & Mandi
            ['Sabun & Mandi', 'Unilever Indonesia', 'Lifebuoy Total 10 75g', 'pcs', 3000, 4500, 24, 240],
            ['Sabun & Mandi', 'Unilever Indonesia', 'Lifebuoy Mild Care 75g', 'pcs', 3000, 4500, 24, 240],
            ['Sabun & Mandi', 'Wings Group', 'Nuvo Family 75g', 'pcs', 2800, 4000, 24, 240],
            ['Sabun & Mandi', 'Unilever Indonesia', 'Lux Soft Touch 75g', 'pcs', 3500, 5000, 24, 240],
            ['Sabun & Mandi', 'Unilever Indonesia', 'Pantene Sachet 5ml', 'pcs', 800, 1500, 48, 480],
            ['Sabun & Mandi', 'Unilever Indonesia', 'Clear Men Sachet 5ml', 'pcs', 800, 1500, 48, 480],
            ['Sabun & Mandi', 'Unilever Indonesia', 'Sunsilk Sachet 5ml', 'pcs', 800, 1500, 48, 480],
            ['Sabun & Mandi', 'Unilever Indonesia', 'Pepsodent 75g', 'pcs', 7000, 9500, 12, 120],
            ['Sabun & Mandi', 'Unilever Indonesia', 'Closeup 75g', 'pcs', 8000, 11000, 12, 120],
            ['Sabun & Mandi', 'Distributor Lokal', 'Sensodyne 75g', 'pcs', 22000, 28000, 6, 60],
            ['Sabun & Mandi', 'Distributor Lokal', 'Sikat Gigi Formula', 'pcs', 7000, 10000, 12, 120],
            ['Sabun & Mandi', 'Unilever Indonesia', 'Citra Body Lotion Sachet 12ml', 'pcs', 1200, 2000, 48, 480],

            // Deterjen & Pembersih
            ['Deterjen & Pembersih', 'Unilever Indonesia', 'Rinso Anti Noda 800g', 'pcs', 18000, 23000, 6, 60],
            ['Deterjen & Pembersih', 'Wings Group', 'Daia Bubuk 800g', 'pcs', 14000, 18000, 6, 60],
            ['Deterjen & Pembersih', 'Wings Group', 'So Klin Detergen 1.8kg', 'pcs', 30000, 38000, 6, 60],
            ['Deterjen & Pembersih', 'Unilever Indonesia', 'Molto Pewangi 800ml', 'pcs', 16000, 20000, 6, 60],
            ['Deterjen & Pembersih', 'Wings Group', 'Wipol Pembersih Lantai 760ml', 'pcs', 11000, 15000, 6, 60],
            ['Deterjen & Pembersih', 'Distributor Lokal', 'Vixal Porselen 780ml', 'pcs', 12000, 16000, 6, 60],
            ['Deterjen & Pembersih', 'Unilever Indonesia', 'Sunlight Lemon 230ml', 'pcs', 4500, 6500, 24, 240],
            ['Deterjen & Pembersih', 'Wings Group', 'Stella Pengharum Refill 70ml', 'pcs', 8500, 12000, 12, 120],

            // Rokok
            ['Rokok', 'Sampoerna', 'Sampoerna Mild 16', 'pcs', 25000, 28500, 12, 120],
            ['Rokok', 'Sampoerna', 'Sampoerna Mild 12', 'pcs', 19000, 21500, 12, 120],
            ['Rokok', 'Sampoerna', 'Marlboro Filter 16', 'pcs', 32000, 36000, 6, 60],
            ['Rokok', 'Sampoerna', 'Marlboro Lights 16', 'pcs', 32000, 36000, 6, 60],
            ['Rokok', 'Distributor Lokal', 'Djarum Super 12', 'pcs', 18000, 21000, 12, 120],
            ['Rokok', 'Distributor Lokal', 'Gudang Garam Filter 12', 'pcs', 19000, 22000, 12, 120],
            ['Rokok', 'Distributor Lokal', 'Surya Pro Mild 16', 'pcs', 24000, 27500, 6, 60],
            ['Rokok', 'Distributor Lokal', 'Lucky Strike 12', 'pcs', 20000, 23500, 6, 60],

            // Pampers & Bayi
            ['Pampers & Bayi', 'Distributor Lokal', 'Pampers Premium S 4pcs', 'pcs', 8000, 11000, 12, 120],
            ['Pampers & Bayi', 'Distributor Lokal', 'Mamy Poko Pants L 4pcs', 'pcs', 9000, 12500, 12, 120],
            ['Pampers & Bayi', 'Distributor Lokal', 'Sweety Silver L 4pcs', 'pcs', 7500, 10500, 12, 120],
            ['Pampers & Bayi', 'Distributor Lokal', 'SGM 1+ 400g', 'pcs', 35000, 42000, 6, 60],
            ['Pampers & Bayi', 'Nestle Indonesia', 'Lactogen 1 400g', 'pcs', 60000, 70000, 6, 60],

            // Obat & Kesehatan
            ['Obat & Kesehatan', 'Distributor Lokal', 'Paramex 4 Tablet', 'pcs', 2500, 3500, 24, 240],
            ['Obat & Kesehatan', 'Distributor Lokal', 'Bodrex 4 Tablet', 'pcs', 2200, 3000, 24, 240],
            ['Obat & Kesehatan', 'Distributor Lokal', 'Panadol 10 Tablet', 'pcs', 8000, 11000, 12, 120],
            ['Obat & Kesehatan', 'Distributor Lokal', 'Komix Sachet 7ml', 'pcs', 1500, 2200, 24, 240],
            ['Obat & Kesehatan', 'Distributor Lokal', 'Antimo 4 Tablet', 'pcs', 3500, 5000, 24, 240],
            ['Obat & Kesehatan', 'Distributor Lokal', 'Tolak Angin Sachet 15ml', 'pcs', 2500, 3500, 24, 240],
            ['Obat & Kesehatan', 'Distributor Lokal', 'Promag 12 Tablet', 'pcs', 6000, 8500, 12, 120],
            ['Obat & Kesehatan', 'Distributor Lokal', 'Vitacimin 2 Tablet', 'pcs', 2000, 3000, 24, 240],

            // ATK
            ['ATK', 'Distributor Lokal', 'Pulpen Standard AE7 Hitam', 'pcs', 2500, 3500, 24, 240],
            ['ATK', 'Distributor Lokal', 'Pensil 2B Faber Castell', 'pcs', 3500, 5000, 24, 240],
            ['ATK', 'Distributor Lokal', 'Buku Tulis Sinar Dunia 38lb', 'pcs', 3500, 5000, 24, 240],
            ['ATK', 'Distributor Lokal', 'Penghapus Steadtler', 'pcs', 2000, 3000, 24, 240],
            ['ATK', 'Distributor Lokal', 'Tipe-X Kenko 4ml', 'pcs', 6000, 8500, 12, 120],

            // Lainnya
            ['Lainnya', 'Distributor Lokal', 'Kantong Plastik Klip 1kg', 'pcs', 800, 1500, 48, 480],
            ['Lainnya', 'Distributor Lokal', 'Korek Api Bjorn', 'pcs', 1500, 2500, 24, 240],
            ['Lainnya', 'Distributor Lokal', 'Lilin Putih Batang', 'pcs', 1000, 2000, 24, 240],
            ['Lainnya', 'Distributor Lokal', 'Tisu Paseo 250s', 'pcs', 12000, 16000, 6, 60],
            ['Lainnya', 'Distributor Lokal', 'Pampers Adult M 4pcs', 'pcs', 22000, 28000, 6, 60],
        ];

        $i = 1;
        foreach ($items as $row) {
            [$katNm, $supNm, $nama, $satuan, $hb, $hj, $smin, $smax] = $row;
            $kid = $kat[$katNm] ?? null;
            $sid = $sup[$supNm] ?? $L;
            if (! $kid) continue;

            Barang::firstOrCreate(['nama' => $nama], [
                'kode' => 'BRG' . str_pad((string) $i, 5, '0', STR_PAD_LEFT),
                'barcode' => null,
                'nama' => $nama,
                'kategori_id' => $kid,
                'supplier_id' => $sid,
                'satuan' => $satuan,
                'harga_beli' => $hb,
                'harga_jual' => $hj,
                'stok_min' => $smin,
                'stok_max' => $smax,
                'stok_current' => $smax,
                'aktif' => true,
            ]);
            $i++;
        }
    }
}
