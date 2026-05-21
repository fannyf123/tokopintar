<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama' => 'Indofood CBP', 'kontak' => 'Sales Indofood', 'no_hp' => '02157955660', 'email' => 'cs@indofood.co.id', 'alamat' => 'Jakarta'],
            ['nama' => 'Wings Group', 'kontak' => 'Sales Wings', 'no_hp' => '0215303322', 'email' => 'info@wings.co.id', 'alamat' => 'Jakarta'],
            ['nama' => 'Unilever Indonesia', 'kontak' => 'Sales Unilever', 'no_hp' => '0215299666', 'email' => 'cs@unilever.co.id', 'alamat' => 'Tangerang'],
            ['nama' => 'Mayora Indah', 'kontak' => 'Sales Mayora', 'no_hp' => '0215665301', 'email' => 'cs@mayora.com', 'alamat' => 'Tangerang'],
            ['nama' => 'Garudafood', 'kontak' => 'Sales Garuda', 'no_hp' => '0215797088', 'email' => 'cs@garudafood.com', 'alamat' => 'Jakarta'],
            ['nama' => 'Nestle Indonesia', 'kontak' => 'Sales Nestle', 'no_hp' => '02175885588', 'email' => 'cs@id.nestle.com', 'alamat' => 'Jakarta'],
            ['nama' => 'Frisian Flag', 'kontak' => 'Sales Frisian', 'no_hp' => '0215276266', 'email' => 'cs@frisianflag.com', 'alamat' => 'Jakarta'],
            ['nama' => 'Ultrajaya', 'kontak' => 'Sales Ultra', 'no_hp' => '0227802222', 'email' => 'cs@ultrajaya.co.id', 'alamat' => 'Bandung'],
            ['nama' => 'Aqua Danone', 'kontak' => 'Sales Aqua', 'no_hp' => '0215794321', 'email' => 'cs@aqua.com', 'alamat' => 'Jakarta'],
            ['nama' => 'Coca-Cola Indonesia', 'kontak' => 'Sales Coca', 'no_hp' => '0215575700', 'email' => 'cs@cocacola.co.id', 'alamat' => 'Bekasi'],
            ['nama' => 'Sampoerna', 'kontak' => 'Sales Sampoerna', 'no_hp' => '0315152010', 'email' => 'cs@sampoerna.com', 'alamat' => 'Surabaya'],
            ['nama' => 'Distributor Lokal', 'kontak' => 'Toko Grosir', 'no_hp' => '081234567890', 'email' => null, 'alamat' => 'Lokal'],
        ];

        foreach ($items as $i) {
            Supplier::firstOrCreate(['nama' => $i['nama']], $i);
        }
    }
}
