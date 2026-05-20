<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    public const STATUS_LUNAS = 'lunas';
    public const STATUS_BATAL = 'batal';

    protected $fillable = [
        'nomor', 'tanggal', 'kasir_id', 'pelanggan_id',
        'total', 'diskon', 'pajak', 'grand_total',
        'dibayar', 'kembalian', 'metode_bayar', 'status',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'total' => 'integer',
        'diskon' => 'integer',
        'pajak' => 'integer',
        'grand_total' => 'integer',
        'dibayar' => 'integer',
        'kembalian' => 'integer',
    ];

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public static function generateNomor(): string
    {
        $tgl = now()->format('Ymd');
        $count = static::whereDate('created_at', now()->toDateString())->count() + 1;
        return 'TRX-' . $tgl . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
