<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ExportService
{
    /**
     * Definisi tabel yang bisa diekspor: query + kolom ramah-baca.
     * key => [judul, closure pengambil baris (array of assoc)]
     */
    public function datasets(): array
    {
        return [
            'barang' => 'Daftar Barang',
            'penjualan' => 'Riwayat Penjualan',
            'pelanggan' => 'Member / Pelanggan',
            'supplier' => 'Pemasok',
            'pengeluaran' => 'Biaya Operasional',
            'stok_menipis' => 'Stok Menipis',
        ];
    }

    public function title(string $key): string
    {
        return $this->datasets()[$key] ?? 'Data';
    }

    /**
     * @return array{0: array<string>, 1: array<array<scalar>>} [header, rows]
     */
    public function rows(string $key): array
    {
        return match ($key) {
            'barang' => $this->barang(),
            'penjualan' => $this->penjualan(),
            'pelanggan' => $this->pelanggan(),
            'supplier' => $this->supplier(),
            'pengeluaran' => $this->pengeluaran(),
            'stok_menipis' => $this->stokMenipis(),
            default => [[], []],
        };
    }

    private function barang(): array
    {
        $header = ['Kode', 'Nama', 'Kategori', 'Harga Beli', 'Harga Jual', 'Stok', 'Satuan', 'Status'];
        $rows = DB::table('barangs as b')
            ->leftJoin('kategoris as k', 'k.id', '=', 'b.kategori_id')
            ->orderBy('b.nama')
            ->get(['b.kode', 'b.nama', 'k.nama as kategori', 'b.harga_beli', 'b.harga_jual', 'b.stok_current', 'b.satuan', 'b.aktif'])
            ->map(fn ($r) => [
                $r->kode, $r->nama, $r->kategori, (int) $r->harga_beli, (int) $r->harga_jual,
                (int) $r->stok_current, $r->satuan, $r->aktif ? 'Dijual' : 'Disembunyikan',
            ])->all();
        return [$header, $rows];
    }

    private function penjualan(): array
    {
        $header = ['Nomor', 'Tanggal', 'Pelanggan', 'Grand Total', 'Dibayar', 'Status', 'Metode'];
        $rows = DB::table('penjualans as p')
            ->leftJoin('pelanggans as pl', 'pl.id', '=', 'p.pelanggan_id')
            ->orderByDesc('p.tanggal')
            ->get(['p.nomor', 'p.tanggal', 'pl.nama as pelanggan', 'p.grand_total', 'p.dibayar', 'p.status', 'p.metode_bayar'])
            ->map(fn ($r) => [
                $r->nomor, $r->tanggal, $r->pelanggan ?? 'Umum', (int) $r->grand_total,
                (int) $r->dibayar, strtoupper($r->status), strtoupper($r->metode_bayar),
            ])->all();
        return [$header, $rows];
    }

    private function pelanggan(): array
    {
        $header = ['Nama', 'No HP', 'Tipe', 'Diskon %', 'Poin', 'Total Belanja'];
        $rows = DB::table('pelanggans')->orderBy('nama')
            ->get(['nama', 'no_hp', 'tipe', 'diskon_persen', 'poin', 'total_belanja'])
            ->map(fn ($r) => [
                $r->nama, $r->no_hp ?? '-', ucfirst($r->tipe), (int) ($r->diskon_persen ?? 0),
                (int) ($r->poin ?? 0), (int) $r->total_belanja,
            ])->all();
        return [$header, $rows];
    }

    private function supplier(): array
    {
        $header = ['Nama', 'Kontak', 'No HP', 'Email'];
        $rows = DB::table('suppliers')->orderBy('nama')
            ->get(['nama', 'kontak', 'no_hp', 'email'])
            ->map(fn ($r) => [$r->nama, $r->kontak ?? '-', $r->no_hp ?? '-', $r->email ?? '-'])->all();
        return [$header, $rows];
    }

    private function pengeluaran(): array
    {
        $header = ['Tanggal', 'Kategori', 'Keterangan', 'Jumlah'];
        $rows = DB::table('pengeluarans')->orderByDesc('tanggal')
            ->get(['tanggal', 'kategori', 'keterangan', 'jumlah'])
            ->map(fn ($r) => [$r->tanggal, $r->kategori, $r->keterangan ?? '-', (int) $r->jumlah])->all();
        return [$header, $rows];
    }

    private function stokMenipis(): array
    {
        $header = ['Kode', 'Nama', 'Stok', 'Stok Min', 'Satuan'];
        $rows = DB::table('barangs')
            ->where('aktif', true)
            ->whereColumn('stok_current', '<=', 'stok_min')
            ->orderBy('stok_current')
            ->get(['kode', 'nama', 'stok_current', 'stok_min', 'satuan'])
            ->map(fn ($r) => [$r->kode, $r->nama, (int) $r->stok_current, (int) $r->stok_min, $r->satuan])->all();
        return [$header, $rows];
    }
}
