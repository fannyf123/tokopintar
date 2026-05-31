<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\Kategori;

class ProductImportService
{
    /** Header kolom template (urutan wajib). */
    public const COLUMNS = ['nama', 'kategori', 'satuan', 'harga_beli', 'harga_jual', 'stok'];

    /**
     * Parse & impor file CSV produk.
     *
     * @return array{ditambah:int, dilewati:int, error:array<string>}
     */
    public function importCsv(string $path): array
    {
        $ditambah = 0;
        $dilewati = 0;
        $error = [];

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return ['ditambah' => 0, 'dilewati' => 0, 'error' => ['Gagal membuka file.']];
        }

        // Nama barang yang sudah ada (lowercase)
        $existing = Barang::query()->pluck('nama')
            ->map(fn ($n) => mb_strtolower(trim($n)))
            ->flip();

        $baris = 0;
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $baris++;

            // Lewati BOM di sel pertama
            if (isset($row[0])) {
                $row[0] = preg_replace('/^\xEF\xBB\xBF/', '', $row[0]);
            }

            // Baris 1 = header: lewati jika mengandung "nama"
            if ($baris === 1 && isset($row[0]) && mb_strtolower(trim($row[0])) === 'nama') {
                continue;
            }

            // Lewati baris kosong
            if (count(array_filter($row, fn ($c) => trim((string) $c) !== '')) === 0) {
                continue;
            }

            $nama = trim((string) ($row[0] ?? ''));
            if ($nama === '') {
                $error[] = "Baris {$baris}: nama kosong, dilewati.";
                continue;
            }

            if ($existing->has(mb_strtolower($nama))) {
                $dilewati++;
                continue;
            }

            $namaKategori = trim((string) ($row[1] ?? '')) ?: 'Lainnya';
            $satuan = trim((string) ($row[2] ?? '')) ?: 'pcs';
            $hargaBeli = $this->toInt($row[3] ?? 0);
            $hargaJual = $this->toInt($row[4] ?? 0);
            $stok = $this->toInt($row[5] ?? 0);

            $kategori = Kategori::firstOrCreate(['nama' => $namaKategori]);

            Barang::create([
                'kode' => Barang::generateKode(),
                'nama' => $nama,
                'kategori_id' => $kategori->id,
                'satuan' => $satuan,
                'harga_beli' => $hargaBeli,
                'harga_jual' => max($hargaJual, $hargaBeli),
                'stok_min' => 0,
                'stok_max' => 0,
                'stok_current' => $stok,
                'aktif' => $hargaJual > 0,
            ]);

            $ditambah++;
            $existing->put(mb_strtolower($nama), true);
        }

        fclose($handle);

        return ['ditambah' => $ditambah, 'dilewati' => $dilewati, 'error' => $error];
    }

    private function toInt($v): int
    {
        return (int) preg_replace('/[^\d]/', '', (string) $v);
    }
}
