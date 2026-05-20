<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembelianDetail extends Model
{
    protected $fillable = [
        'pembelian_id', 'barang_id', 'qty', 'harga_beli',
        'subtotal', 'no_batch', 'tanggal_kadaluarsa',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_beli' => 'integer',
        'subtotal' => 'integer',
        'tanggal_kadaluarsa' => 'date',
    ];

    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
