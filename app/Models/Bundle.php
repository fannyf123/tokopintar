<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bundle extends Model
{
    protected $fillable = [
        'nama', 'barang_a_id', 'barang_b_id',
        'harga_bundle', 'harga_normal', 'saving',
        'total_margin_pct', 'lift_score', 'aktif',
    ];

    protected $casts = [
        'harga_bundle' => 'integer',
        'harga_normal' => 'integer',
        'saving' => 'integer',
        'total_margin_pct' => 'float',
        'lift_score' => 'float',
        'aktif' => 'boolean',
    ];

    public function barangA(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_a_id');
    }

    public function barangB(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_b_id');
    }
}
