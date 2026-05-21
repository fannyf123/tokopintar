<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama' => 'Mie Instan', 'deskripsi' => 'Mie instan kemasan'],
            ['nama' => 'Air Mineral & Minuman', 'deskripsi' => 'Air kemasan dan minuman botol'],
            ['nama' => 'Susu & UHT', 'deskripsi' => 'Susu segar, UHT, kental manis'],
            ['nama' => 'Kopi & Teh', 'deskripsi' => 'Kopi instan, teh celup'],
            ['nama' => 'Snack & Wafer', 'deskripsi' => 'Snack ringan, wafer, biskuit'],
            ['nama' => 'Coklat & Permen', 'deskripsi' => 'Coklat batang, permen, gula-gula'],
            ['nama' => 'Bumbu & Saus', 'deskripsi' => 'Kecap, saus, MSG, bumbu masak'],
            ['nama' => 'Beras & Sembako', 'deskripsi' => 'Beras, gula, minyak, tepung'],
            ['nama' => 'Sabun & Mandi', 'deskripsi' => 'Sabun, sampo, pasta gigi'],
            ['nama' => 'Deterjen & Pembersih', 'deskripsi' => 'Deterjen, pembersih lantai, pemutih'],
            ['nama' => 'Rokok', 'deskripsi' => 'Rokok kemasan'],
            ['nama' => 'Pampers & Bayi', 'deskripsi' => 'Popok, susu bayi'],
            ['nama' => 'Obat & Kesehatan', 'deskripsi' => 'Obat warung, vitamin'],
            ['nama' => 'ATK', 'deskripsi' => 'Alat tulis kantor'],
            ['nama' => 'Lainnya', 'deskripsi' => 'Kategori lain'],
        ];

        foreach ($items as $i) {
            Kategori::firstOrCreate(['nama' => $i['nama']], $i);
        }
    }
}
