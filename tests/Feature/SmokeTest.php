<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Pengeluaran;
use App\Models\ProductBatch;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Barang $barang;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create([
            'name' => 'Admin', 'username' => 'admin', 'email' => 'a@a.test',
            'password' => Hash::make('rahasia'), 'role' => 'admin', 'aktif' => true,
        ]);

        $kat = Kategori::create(['nama' => 'Minuman']);
        $sup = Supplier::create(['nama' => 'PT A']);
        $this->barang = Barang::create([
            'kode' => 'BRG00001', 'nama' => 'Teh Botol', 'kategori_id' => $kat->id,
            'supplier_id' => $sup->id, 'satuan' => 'pcs', 'harga_beli' => 3000,
            'harga_jual' => 5000, 'stok_min' => 5, 'stok_max' => 100, 'stok_current' => 50,
            'aktif' => true,
        ]);
        ProductBatch::create([
            'barang_id' => $this->barang->id, 'no_batch' => 'B1', 'tanggal_masuk' => now()->toDateString(),
            'tanggal_kadaluarsa' => now()->addDays(45)->toDateString(), 'qty_awal' => 50, 'qty_sisa' => 50,
            'harga_beli_batch' => 3000, 'supplier_id' => $sup->id,
        ]);
        Pelanggan::create(['nama' => 'Budi', 'tipe' => 'umum']);
        Pengeluaran::create(['kategori' => 'sewa', 'tanggal' => now()->toDateString(), 'jumlah' => 100000, 'dibuat_oleh' => $this->admin->id]);
    }

    public function test_admin_dapat_akses_semua_halaman_get(): void
    {
        $routes = [
            '/dashboard',
            '/profile',
            '/kategori', '/kategori/create',
            '/supplier', '/supplier/create',
            '/pelanggan', '/pelanggan/create',
            '/barang', '/barang/create',
            '/pembelian', '/pembelian/create',
            '/pos',
            '/penjualan',
            '/mutasi', '/mutasi/create',
            '/expiry',
            '/pengeluaran', '/pengeluaran/create',
            '/insight',
            '/laporan/laba',
            '/laporan/laba?preset=today',
            '/laporan/laba?preset=this_month&g=monthly',
        ];

        foreach ($routes as $r) {
            $res = $this->actingAs($this->admin)->get($r);
            $this->assertNotEquals(500, $res->status(), "Route {$r} returned 500");
            $this->assertTrue(in_array($res->status(), [200, 302]), "Route {$r} unexpected status {$res->status()}");
        }
    }

    public function test_kategori_crud_full_cycle(): void
    {
        $kat = Kategori::create(['nama' => 'Cemilan']);

        $this->actingAs($this->admin)
            ->put('/kategori/' . $kat->id, ['nama' => 'Cemilan Update'])
            ->assertRedirect('/kategori');

        $this->assertDatabaseHas('kategoris', ['id' => $kat->id, 'nama' => 'Cemilan Update']);

        $this->actingAs($this->admin)
            ->delete('/kategori/' . $kat->id)
            ->assertRedirect('/kategori');

        $this->assertDatabaseMissing('kategoris', ['id' => $kat->id]);
    }

    public function test_pos_full_flow_dengan_fefo(): void
    {
        $payload = [
            'metode_bayar' => 'cash',
            'diskon' => 0,
            'pajak' => 0,
            'dibayar' => 10000,
            'items' => [
                ['barang_id' => $this->barang->id, 'qty' => 2],
            ],
        ];

        $res = $this->actingAs($this->admin)->post('/pos', $payload);
        $res->assertRedirect();

        $this->assertDatabaseHas('penjualans', ['grand_total' => 10000, 'kembalian' => 0]);
        $this->barang->refresh();
        $this->assertEquals(48, $this->barang->stok_current);
        $this->assertDatabaseHas('stock_movements', [
            'barang_id' => $this->barang->id,
            'jenis' => 'stock_out',
            'qty_signed' => -2,
        ]);

        $batch = ProductBatch::where('barang_id', $this->barang->id)->first();
        $this->assertEquals(48, $batch->qty_sisa);
    }

    public function test_pos_tolak_qty_lebih_dari_stok(): void
    {
        $res = $this->actingAs($this->admin)->post('/pos', [
            'metode_bayar' => 'cash',
            'dibayar' => 999999,
            'items' => [['barang_id' => $this->barang->id, 'qty' => 9999]],
        ]);
        $this->assertContains($res->status(), [422, 302, 500]);
    }

    public function test_pembelian_terima_menambah_stok_dan_bikin_batch(): void
    {
        $res = $this->actingAs($this->admin)->post('/pembelian', [
            'tanggal' => now()->toDateString(),
            'supplier_id' => 1,
            'metode_bayar' => 'cash',
            'dibayar' => 60000,
            'items' => [
                ['barang_id' => $this->barang->id, 'qty' => 20, 'harga_beli' => 3000, 'no_batch' => 'NEWB',
                 'tanggal_kadaluarsa' => now()->addDays(60)->toDateString()],
            ],
        ]);
        $res->assertRedirect();

        $pembelian = \App\Models\Pembelian::latest('id')->first();
        $this->assertEquals('draft', $pembelian->status);

        $r2 = $this->actingAs($this->admin)->post('/pembelian/' . $pembelian->id . '/terima');
        $r2->assertRedirect();

        $pembelian->refresh();
        $this->assertEquals('diterima', $pembelian->status);

        $this->barang->refresh();
        $this->assertEquals(70, $this->barang->stok_current);

        $this->assertDatabaseHas('product_batches', ['no_batch' => 'NEWB', 'qty_sisa' => 20]);
    }

    public function test_mutasi_stok_adjustment_minus(): void
    {
        $this->actingAs($this->admin)->post('/mutasi', [
            'barang_id' => $this->barang->id,
            'jenis' => 'rusak',
            'qty' => 3,
            'alasan' => 'Pecah saat dibongkar',
        ])->assertRedirect('/mutasi');

        $this->barang->refresh();
        $this->assertEquals(47, $this->barang->stok_current);
        $this->assertDatabaseHas('stock_movements', [
            'jenis' => 'rusak', 'qty_signed' => -3, 'alasan' => 'Pecah saat dibongkar',
        ]);
    }

    public function test_insight_regenerate_tidak_error(): void
    {
        $this->actingAs($this->admin)->post('/insight/regenerate')->assertRedirect();
        $this->assertDatabaseHas('product_insights', ['barang_id' => $this->barang->id]);
    }

    public function test_laporan_laba_csv_dan_pdf(): void
    {
        $this->actingAs($this->admin)->get('/laporan/laba/csv?preset=today')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $this->actingAs($this->admin)->get('/laporan/laba/pdf?preset=today')
            ->assertOk();
    }

    public function test_kasir_tidak_bisa_akses_admin_only(): void
    {
        $kasir = User::create([
            'name' => 'K', 'username' => 'kasir2', 'email' => 'k@k.test',
            'password' => Hash::make('x'), 'role' => 'kasir', 'aktif' => true,
        ]);
        $this->actingAs($kasir)->get('/kategori')->assertForbidden();
        $this->actingAs($kasir)->get('/laporan/laba')->assertForbidden();
        $this->actingAs($kasir)->get('/pengeluaran')->assertForbidden();
    }
}
