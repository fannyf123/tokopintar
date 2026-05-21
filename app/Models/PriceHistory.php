<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceHistory extends Model
{
    protected $fillable = [
        'barang_id', 'harga_jual_lama', 'harga_jual_baru',
        'delta_persen', 'diubah_oleh',
    ];

    protected $casts = [
        'harga_jual_lama' => 'integer',
        'harga_jual_baru' => 'integer',
        'delta_persen' => 'float',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }
}
