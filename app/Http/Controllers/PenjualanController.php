<?php

namespace App\Http\Controllers;

use App\Http\Requests\PenjualanRequest;
use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    public function __construct(private StockService $stock) {}

    public function index(): View
    {
        $items = Penjualan::with('kasir', 'pelanggan')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate(15);
        return view('penjualan.index', compact('items'));
    }

    public function pos(): View
    {
        return view('penjualan.pos', [
            'pelanggans' => Pelanggan::orderBy('nama')->get(),
            'bundles' => \App\Models\Bundle::with('barangA', 'barangB')->where('aktif', true)->get(),
        ]);
    }

    public function crossSell(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $barangId = (int) $request->query('barang_id');
        if (! $barangId) return response()->json(['suggestions' => []]);

        $rules = \App\Models\AssociationRule::with('consequent')
            ->where('antecedent_barang_id', $barangId)
            ->where('lift', '>=', 1.2)
            ->orderByDesc('lift')
            ->limit(3)
            ->get()
            ->filter(fn ($r) => $r->consequent && $r->consequent->aktif && $r->consequent->stok_current > 0)
            ->map(fn ($r) => [
                'id' => $r->consequent->id,
                'nama' => $r->consequent->nama,
                'harga_jual' => (int) $r->consequent->harga_jual,
                'stok_current' => (int) $r->consequent->stok_current,
                'lift' => round($r->lift, 2),
                'confidence' => round($r->confidence * 100, 1),
            ])->values();

        return response()->json(['suggestions' => $rules]);
    }

    public function searchBarang(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $barangs = Barang::where('aktif', true)
            ->where('stok_current', '>', 0)
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($x) use ($q) {
                    $x->whereRaw('LOWER(nama) LIKE ?', ['%' . mb_strtolower($q) . '%'])
                      ->orWhere('kode', $q)
                      ->orWhere('barcode', $q);
                });
            })
            ->orderBy('nama')
            ->limit(20)
            ->get(['id', 'kode', 'barcode', 'nama', 'satuan', 'harga_jual', 'harga_beli', 'stok_current']);
        return response()->json($barangs);
    }

    public function store(PenjualanRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $penjualan = DB::transaction(function () use ($data) {
            $total = 0;
            $detailRows = [];
            foreach ($data['items'] as $row) {
                $barang = Barang::lockForUpdate()->findOrFail($row['barang_id']);
                if (! $barang->aktif) {
                    abort(422, "Barang {$barang->nama} tidak aktif.");
                }
                $qty = (int) $row['qty'];
                $diskonItem = (int) ($row['diskon_item'] ?? 0);
                $hargaJual = (int) $barang->harga_jual;
                $subtotal = max(0, $qty * $hargaJual - $diskonItem);
                $total += $subtotal;
                $detailRows[] = compact('barang', 'qty', 'hargaJual', 'diskonItem', 'subtotal');
            }

            $diskon = (int) ($data['diskon'] ?? 0);
            $pajak = (int) ($data['pajak'] ?? 0);
            $baseGrand = max(0, $total - $diskon + $pajak);

            // Penukaran poin member: 100 poin = Rp 1.000
            $tukarPoin = intdiv((int) ($data['tukar_poin'] ?? 0), 100) * 100;
            $poinValue = 0;
            if ($tukarPoin > 0 && ($data['pelanggan_id'] ?? null)) {
                $plg = Pelanggan::lockForUpdate()->find($data['pelanggan_id']);
                if (! $plg || $plg->poin < $tukarPoin) {
                    abort(422, 'Poin tidak cukup untuk ditukar.');
                }
                // batasi agar nilai poin tidak melebihi tagihan
                $maxPoin = intdiv($baseGrand, 10);
                $tukarPoin = min($tukarPoin, intdiv($maxPoin, 100) * 100);
                $poinValue = $tukarPoin * 10;
            }

            $grand = max(0, $baseGrand - $poinValue);
            $dibayar = (int) $data['dibayar'];
            if ($dibayar < $grand) {
                abort(422, 'Pembayaran kurang dari grand total.');
            }
            $kembalian = $dibayar - $grand;

            $penjualan = Penjualan::create([
                'nomor' => Penjualan::generateNomor(),
                'tanggal' => now(),
                'kasir_id' => auth()->id(),
                'pelanggan_id' => $data['pelanggan_id'] ?? null,
                'total' => $total,
                'diskon' => $diskon,
                'pajak' => $pajak,
                'grand_total' => $grand,
                'dibayar' => $dibayar,
                'kembalian' => $kembalian,
                'metode_bayar' => $data['metode_bayar'],
                'status' => Penjualan::STATUS_LUNAS,
            ]);

            foreach ($detailRows as $r) {
                $detail = PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $r['barang']->id,
                    'qty' => $r['qty'],
                    'harga_jual_saat_itu' => $r['hargaJual'],
                    'diskon_item' => $r['diskonItem'],
                    'subtotal' => $r['subtotal'],
                    'hpp_saat_itu' => 0,
                ]);

                $result = $this->stock->stockOutFefo($r['barang'], $r['qty'], $detail);

                $detail->update(['hpp_saat_itu' => $result['hpp_per_unit']]);
            }

            if ($penjualan->pelanggan_id) {
                // Poin didapat: 1 poin per Rp 1.000 dari yang benar-benar dibayar (grand)
                $poinDidapat = intdiv($grand, 1000);
                Pelanggan::where('id', $penjualan->pelanggan_id)->update([
                    'total_belanja' => DB::raw('total_belanja + ' . $grand),
                    'poin' => DB::raw('GREATEST(0, poin - ' . (int) $tukarPoin . ') + ' . $poinDidapat),
                ]);
            }

            return $penjualan;
        });

        return redirect()->route('penjualan.show', $penjualan)
            ->with('success', 'Transaksi tersimpan. Nomor: ' . $penjualan->nomor);
    }

    public function show(Penjualan $penjualan): View
    {
        $penjualan->load('kasir', 'pelanggan', 'details.barang');
        return view('penjualan.show', compact('penjualan'));
    }

    public function struk(Penjualan $penjualan): View
    {
        $penjualan->load('kasir', 'pelanggan', 'details.barang');
        return view('penjualan.struk', compact('penjualan'));
    }
}
