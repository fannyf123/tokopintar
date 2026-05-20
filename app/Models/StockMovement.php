<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const JENIS_IN = 'stock_in';
    public const JENIS_OUT = 'stock_out';
    public const JENIS_ADJ_PLUS = 'adjustment_plus';
    public const JENIS_ADJ_MINUS = 'adjustment_minus';
    public const JENIS_RETUR_JUAL = 'retur_jual';
    public const JENIS_RETUR_BELI = 'retur_beli';
    public const JENIS_RUSAK = 'rusak';
    public const JENIS_HILANG = 'hilang';
    public const JENIS_EXPIRED = 'expired_dibuang';

    public const JENIS_LIST = [
        self::JENIS_IN, self::JENIS_OUT, self::JENIS_ADJ_PLUS, self::JENIS_ADJ_MINUS,
        self::JENIS_RETUR_JUAL, self::JENIS_RETUR_BELI, self::JENIS_RUSAK,
        self::JENIS_HILANG, self::JENIS_EXPIRED,
    ];

    public const JENIS_MUTASI = [
        self::JENIS_ADJ_PLUS, self::JENIS_ADJ_MINUS, self::JENIS_RETUR_JUAL,
        self::JENIS_RETUR_BELI, self::JENIS_RUSAK, self::JENIS_HILANG,
        self::JENIS_EXPIRED,
    ];

    protected $fillable = [
        'barang_id', 'batch_id', 'jenis', 'qty_signed',
        'referensi_type', 'referensi_id', 'alasan', 'dibuat_oleh',
    ];

    protected $casts = [
        'qty_signed' => 'integer',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'batch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
