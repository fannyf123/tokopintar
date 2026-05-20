<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\PenjualanBatchUsed;
use App\Models\PenjualanDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    public function stockIn(
        Barang $barang,
        int $qty,
        int $hargaBeliBatch,
        ?string $noBatch = null,
        ?string $tanggalKadaluarsa = null,
        ?int $supplierId = null,
        ?string $referensiType = null,
        ?int $referensiId = null,
        ?string $alasan = null,
    ): ProductBatch {
        if ($qty <= 0) {
            throw new RuntimeException('Qty stockIn harus > 0.');
        }

        return DB::transaction(function () use (
            $barang, $qty, $hargaBeliBatch, $noBatch, $tanggalKadaluarsa,
            $supplierId, $referensiType, $referensiId, $alasan
        ) {
            $batch = ProductBatch::create([
                'barang_id' => $barang->id,
                'no_batch' => $noBatch,
                'tanggal_masuk' => now()->toDateString(),
                'tanggal_kadaluarsa' => $tanggalKadaluarsa,
                'qty_awal' => $qty,
                'qty_sisa' => $qty,
                'harga_beli_batch' => $hargaBeliBatch,
                'supplier_id' => $supplierId ?? $barang->supplier_id,
            ]);

            Barang::where('id', $barang->id)->update([
                'stok_current' => DB::raw('stok_current + ' . $qty),
                'last_in_at' => now(),
            ]);

            StockMovement::create([
                'barang_id' => $barang->id,
                'batch_id' => $batch->id,
                'jenis' => StockMovement::JENIS_IN,
                'qty_signed' => $qty,
                'referensi_type' => $referensiType,
                'referensi_id' => $referensiId,
                'alasan' => $alasan,
                'dibuat_oleh' => Auth::id(),
            ]);

            return $batch->refresh();
        });
    }

    public function stockOutFefo(
        Barang $barang,
        int $qty,
        PenjualanDetail $detail,
    ): array {
        if ($qty <= 0) {
            throw new RuntimeException('Qty stockOut harus > 0.');
        }

        $current = (int) Barang::where('id', $barang->id)->lockForUpdate()->value('stok_current');
        if ($current < $qty) {
            throw new RuntimeException("Stok tidak cukup untuk {$barang->nama}. Tersedia: {$current}, diminta: {$qty}.");
        }

        $batches = ProductBatch::where('barang_id', $barang->id)
            ->where('qty_sisa', '>', 0)
            ->orderByRaw('CASE WHEN tanggal_kadaluarsa IS NULL THEN 1 ELSE 0 END')
            ->orderBy('tanggal_kadaluarsa')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        $sisa = $qty;
        $totalHpp = 0;
        $usedRows = [];

        foreach ($batches as $batch) {
            if ($sisa <= 0) break;
            $ambil = min($batch->qty_sisa, $sisa);

            ProductBatch::where('id', $batch->id)->update([
                'qty_sisa' => DB::raw('qty_sisa - ' . $ambil),
            ]);

            PenjualanBatchUsed::create([
                'penjualan_detail_id' => $detail->id,
                'batch_id' => $batch->id,
                'qty' => $ambil,
            ]);

            StockMovement::create([
                'barang_id' => $barang->id,
                'batch_id' => $batch->id,
                'jenis' => StockMovement::JENIS_OUT,
                'qty_signed' => -$ambil,
                'referensi_type' => 'penjualan_detail',
                'referensi_id' => $detail->id,
                'alasan' => 'Penjualan FEFO',
                'dibuat_oleh' => Auth::id(),
            ]);

            $totalHpp += $ambil * (int) $batch->harga_beli_batch;
            $usedRows[] = ['batch_id' => $batch->id, 'qty' => $ambil];
            $sisa -= $ambil;
        }

        if ($sisa > 0) {
            $totalHpp += $sisa * (int) $barang->harga_beli;

            StockMovement::create([
                'barang_id' => $barang->id,
                'batch_id' => null,
                'jenis' => StockMovement::JENIS_OUT,
                'qty_signed' => -$sisa,
                'referensi_type' => 'penjualan_detail',
                'referensi_id' => $detail->id,
                'alasan' => 'Penjualan tanpa batch (legacy stock)',
                'dibuat_oleh' => Auth::id(),
            ]);
        }

        Barang::where('id', $barang->id)->update([
            'stok_current' => DB::raw('stok_current - ' . $qty),
            'last_out_at' => now(),
        ]);

        $hppPerUnit = (int) round($totalHpp / max($qty, 1));

        return [
            'hpp_total' => $totalHpp,
            'hpp_per_unit' => $hppPerUnit,
            'batches_used' => $usedRows,
        ];
    }

    public function mutasi(
        Barang $barang,
        string $jenis,
        int $qty,
        string $alasan,
        ?int $batchId = null,
        ?string $referensiType = null,
        ?int $referensiId = null,
    ): StockMovement {
        if (! in_array($jenis, StockMovement::JENIS_MUTASI, true)) {
            throw new RuntimeException("Jenis mutasi tidak valid: {$jenis}");
        }
        if ($qty <= 0) {
            throw new RuntimeException('Qty mutasi harus > 0.');
        }

        $tambah = in_array($jenis, [
            StockMovement::JENIS_ADJ_PLUS,
            StockMovement::JENIS_RETUR_JUAL,
        ], true);

        return DB::transaction(function () use (
            $barang, $jenis, $qty, $alasan, $batchId, $referensiType, $referensiId, $tambah
        ) {
            if (! $tambah) {
                $current = (int) Barang::where('id', $barang->id)->lockForUpdate()->value('stok_current');
                if ($current < $qty) {
                    throw new RuntimeException("Stok tidak cukup untuk mutasi. Tersedia: {$current}, diminta: {$qty}.");
                }
            }

            if ($batchId) {
                $batch = ProductBatch::where('id', $batchId)
                    ->where('barang_id', $barang->id)
                    ->lockForUpdate()
                    ->first();
                if (! $batch) {
                    throw new RuntimeException('Batch tidak ditemukan untuk barang ini.');
                }
                if (! $tambah) {
                    if ($batch->qty_sisa < $qty) {
                        throw new RuntimeException("Sisa batch tidak cukup. Tersedia: {$batch->qty_sisa}.");
                    }
                    ProductBatch::where('id', $batchId)->update([
                        'qty_sisa' => DB::raw('qty_sisa - ' . $qty),
                    ]);
                } else {
                    ProductBatch::where('id', $batchId)->update([
                        'qty_sisa' => DB::raw('qty_sisa + ' . $qty),
                    ]);
                }
            }

            $delta = $tambah ? $qty : -$qty;
            Barang::where('id', $barang->id)->update([
                'stok_current' => DB::raw('stok_current ' . ($tambah ? '+' : '-') . ' ' . $qty),
            ]);

            return StockMovement::create([
                'barang_id' => $barang->id,
                'batch_id' => $batchId,
                'jenis' => $jenis,
                'qty_signed' => $delta,
                'referensi_type' => $referensiType,
                'referensi_id' => $referensiId,
                'alasan' => $alasan,
                'dibuat_oleh' => Auth::id(),
            ]);
        });
    }

    public function buangExpiredBatch(ProductBatch $batch, ?string $alasan = null): ?StockMovement
    {
        if ($batch->qty_sisa <= 0) {
            return null;
        }
        $barang = $batch->barang;

        return $this->mutasi(
            barang: $barang,
            jenis: StockMovement::JENIS_EXPIRED,
            qty: (int) $batch->qty_sisa,
            alasan: $alasan ?? 'Stok kadaluarsa dibuang otomatis',
            batchId: $batch->id,
            referensiType: 'product_batch',
            referensiId: $batch->id,
        );
    }
}
