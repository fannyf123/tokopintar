<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerInsight extends Model
{
    public const SEG_CHAMPION = 'CHAMPION';
    public const SEG_LOYAL = 'LOYAL';
    public const SEG_POTENTIAL = 'POTENTIAL';
    public const SEG_AT_RISK = 'AT_RISK';
    public const SEG_LOST = 'LOST';
    public const SEG_NEW = 'NEW';

    public const SEGMENT_LIST = [
        self::SEG_CHAMPION, self::SEG_LOYAL, self::SEG_POTENTIAL,
        self::SEG_AT_RISK, self::SEG_LOST, self::SEG_NEW,
    ];

    protected $fillable = [
        'pelanggan_id', 'recency_days', 'frequency', 'monetary',
        'r_score', 'f_score', 'm_score', 'segment', 'clv_estimate',
        'avg_interval_days', 'churn_risk', 'rekomendasi_text', 'dihitung_pada',
    ];

    protected $casts = [
        'recency_days' => 'integer',
        'frequency' => 'integer',
        'monetary' => 'integer',
        'clv_estimate' => 'integer',
        'avg_interval_days' => 'float',
        'churn_risk' => 'boolean',
        'dihitung_pada' => 'datetime',
    ];

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }
}
