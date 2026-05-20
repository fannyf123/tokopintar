<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenjualanDetail extends Model
{
    protected $fillable = [
        'penjualan_id', 'barang_id', 'qty', 'harga_jual_saat_itu',
        'diskon_item', 'subtotal', 'hpp_saat_itu',
    ];

    protected $casts = [
        'qty' => 'integer',
        'harga_jual_saat_itu' => 'integer',
        'diskon_item' => 'integer',
        'subtotal' => 'integer',
        'hpp_saat_itu' => 'integer',
    ];

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function batchUsed(): HasMany
    {
        return $this->hasMany(PenjualanBatchUsed::class);
    }
}
