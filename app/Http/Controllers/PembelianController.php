<?php

namespace App\Http\Controllers;

use App\Http\Requests\PembelianRequest;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PembelianController extends Controller
{
    public function __construct(private StockService $stock) {}

    public function index(): View
    {
        $items = Pembelian::with('supplier', 'user')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(15);
        return view('pembelian.index', compact('items'));
    }

    public function create(): View
    {
        return view('pembelian.form', [
            'pembelian' => new Pembelian(['tanggal' => now()->toDateString(), 'metode_bayar' => 'cash']),
            'suppliers' => Supplier::orderBy('nama')->get(),
            'barangs' => Barang::where('aktif', true)->orderBy('nama')->get(),
        ]);
    }

    public function store(PembelianRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $pembelian = DB::transaction(function () use ($data) {
            $total = 0;
            foreach ($data['items'] as $row) {
                $total += (int) $row['qty'] * (int) $row['harga_beli'];
            }

            $pembelian = Pembelian::create([
                'nomor' => Pembelian::generateNomor(),
                'tanggal' => $data['tanggal'],
                'supplier_id' => $data['supplier_id'],
                'total' => $total,
                'dibayar' => $data['dibayar'],
                'metode_bayar' => $data['metode_bayar'],
                'status' => Pembelian::STATUS_DRAFT,
                'catatan' => $data['catatan'] ?? null,
                'dibuat_oleh' => auth()->id(),
            ]);

            foreach ($data['items'] as $row) {
                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $row['barang_id'],
                    'qty' => $row['qty'],
                    'harga_beli' => $row['harga_beli'],
                    'subtotal' => $row['qty'] * $row['harga_beli'],
                    'no_batch' => $row['no_batch'] ?? null,
                    'tanggal_kadaluarsa' => $row['tanggal_kadaluarsa'] ?? null,
                ]);
            }

            return $pembelian;
        });

        return redirect()->route('pembelian.show', $pembelian)
            ->with('success', 'Pembelian dibuat sebagai draft. Klik "Terima Barang" untuk masukkan ke stok.');
    }

    public function show(Pembelian $pembelian): View
    {
        $pembelian->load('supplier', 'user', 'details.barang');
        return view('pembelian.show', compact('pembelian'));
    }

    public function terima(Pembelian $pembelian): RedirectResponse
    {
        if ($pembelian->status !== Pembelian::STATUS_DRAFT) {
            return back()->with('error', 'Hanya pembelian draft yang bisa diterima.');
        }

        DB::transaction(function () use ($pembelian) {
            $pembelian->load('details.barang');
            foreach ($pembelian->details as $d) {
                $hargaPer = (int) $d->harga_beli;
                $this->stock->stockIn(
                    barang: $d->barang,
                    qty: (int) $d->qty,
                    hargaBeliBatch: $hargaPer,
                    noBatch: $d->no_batch,
                    tanggalKadaluarsa: $d->tanggal_kadaluarsa?->toDateString(),
                    supplierId: $pembelian->supplier_id,
                    referensiType: 'pembelian',
                    referensiId: $pembelian->id,
                    alasan: 'Pembelian #' . $pembelian->nomor,
                );
            }
            $pembelian->update(['status' => Pembelian::STATUS_DITERIMA]);
        });

        return redirect()->route('pembelian.show', $pembelian)
            ->with('success', 'Barang masuk ke stok.');
    }

    public function batal(Pembelian $pembelian): RedirectResponse
    {
        if ($pembelian->status === Pembelian::STATUS_DITERIMA) {
            return back()->with('error', 'Pembelian sudah diterima, tidak bisa dibatalkan.');
        }
        $pembelian->update(['status' => Pembelian::STATUS_BATAL]);
        return back()->with('success', 'Pembelian dibatalkan.');
    }
}
