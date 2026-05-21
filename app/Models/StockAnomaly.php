<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAnomaly extends Model
{
    public const JENIS_FRAUD_KASIR = 'fraud_kasir';
    public const JENIS_STOCK_LEAK = 'stock_leak';
    public const JENIS_SALES_SPIKE = 'sales_spike';
    public const JENIS_SALES_DROP = 'sales_drop';
    public const JENIS_DISKON_SPIKE = 'diskon_spike';
    public const JENIS_VOID_PATTERN = 'void_pattern';
    public const JENIS_OFFHOURS_TRX = 'offhours_trx';
    public const JENIS_PARETO_DECLINE = 'pareto_decline';
    public const JENIS_CANNIBALIZATION = 'cannibalization';

    public const SEV_CRITICAL = 'critical';
    public const SEV_WARNING = 'warning';
    public const SEV_INFO = 'info';

    protected $fillable = [
        'jenis', 'barang_id', 'user_id', 'severity',
        'judul', 'detail', 'score', 'resolved',
    ];

    protected $casts = [
        'score' => 'float',
        'resolved' => 'boolean',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
