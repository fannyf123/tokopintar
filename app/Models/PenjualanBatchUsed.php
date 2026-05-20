<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjualanBatchUsed extends Model
{
    protected $fillable = ['penjualan_detail_id', 'batch_id', 'qty'];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(PenjualanDetail::class, 'penjualan_detail_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }
}
