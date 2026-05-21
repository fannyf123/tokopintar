<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorPrice extends Model
{
    protected $fillable = [
        'barang_id', 'competitor_name', 'harga_competitor',
        'tanggal_observasi', 'catatan', 'dibuat_oleh',
    ];

    protected $casts = [
        'harga_competitor' => 'integer',
        'tanggal_observasi' => 'date',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
