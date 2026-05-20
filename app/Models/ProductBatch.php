<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductBatch extends Model
{
    protected $fillable = [
        'barang_id', 'no_batch', 'tanggal_masuk', 'tanggal_kadaluarsa',
        'qty_awal', 'qty_sisa', 'harga_beli_batch', 'supplier_id',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_kadaluarsa' => 'date',
        'qty_awal' => 'integer',
        'qty_sisa' => 'integer',
        'harga_beli_batch' => 'integer',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
