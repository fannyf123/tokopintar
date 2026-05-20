<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $fillable = [
        'kode', 'barcode', 'nama', 'kategori_id', 'supplier_id',
        'satuan', 'harga_beli', 'harga_jual', 'stok_min', 'stok_max',
        'stok_current', 'foto', 'aktif', 'last_in_at', 'last_out_at',
    ];

    protected $casts = [
        'harga_beli' => 'integer',
        'harga_jual' => 'integer',
        'stok_min' => 'integer',
        'stok_max' => 'integer',
        'stok_current' => 'integer',
        'aktif' => 'boolean',
        'last_in_at' => 'datetime',
        'last_out_at' => 'datetime',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function batchesAvailable(): HasMany
    {
        return $this->hasMany(ProductBatch::class)->where('qty_sisa', '>', 0);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function insight()
    {
        return $this->hasOne(ProductInsight::class);
    }

    public static function generateKode(): string
    {
        $next = (int) (static::max('id') ?? 0) + 1;
        return 'BRG' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}
