<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembelian extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_DITERIMA = 'diterima';
    public const STATUS_BATAL = 'batal';

    protected $fillable = [
        'nomor', 'tanggal', 'supplier_id', 'total', 'dibayar',
        'metode_bayar', 'status', 'catatan', 'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'integer',
        'dibayar' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public static function generateNomor(): string
    {
        $tgl = now()->format('Ymd');
        $count = static::whereDate('created_at', now()->toDateString())->count() + 1;
        return 'PB-' . $tgl . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
