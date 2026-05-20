<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductInsight extends Model
{
    public const KELAS_FAST = 'FAST_MOVER';
    public const KELAS_NORMAL = 'NORMAL';
    public const KELAS_SLOW = 'SLOW_MOVER';
    public const KELAS_DEAD = 'DEAD_STOCK';
    public const KELAS_NEW = 'NEW';

    public const KELAS_LIST = [
        self::KELAS_FAST, self::KELAS_NORMAL, self::KELAS_SLOW,
        self::KELAS_DEAD, self::KELAS_NEW,
    ];

    public const STRATEGY_LOSS_LEADER = 'LOSS_LEADER';
    public const STRATEGY_BALANCED = 'BALANCED';
    public const STRATEGY_PROFIT_DRIVER = 'PROFIT_DRIVER';
    public const STRATEGY_LIST = [
        self::STRATEGY_LOSS_LEADER, self::STRATEGY_BALANCED, self::STRATEGY_PROFIT_DRIVER,
    ];

    protected $fillable = [
        'barang_id', 'velocity_30', 'days_of_supply', 'kelas',
        'abc_class', 'forecast_7', 'rekomendasi_text', 'dihitung_pada',
        'margin_pct', 'strategy', 'strategy_partner_ids', 'strategy_text',
    ];

    protected $casts = [
        'velocity_30' => 'float',
        'days_of_supply' => 'float',
        'forecast_7' => 'float',
        'margin_pct' => 'float',
        'dihitung_pada' => 'datetime',
    ];

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
