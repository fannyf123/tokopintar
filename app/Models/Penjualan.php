<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    public const STATUS_LUNAS = 'lunas';
    public const STATUS_BATAL = 'batal';
    public const STATUS_HUTANG = 'hutang';

    protected $fillable = [
        'nomor', 'kode_verifikasi', 'tanggal', 'kasir_id', 'pelanggan_id',
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

    public function sisaHutang(): int
    {
        return max(0, (int) $this->grand_total - (int) $this->dibayar);
    }

    public function isHutang(): bool
    {
        return $this->status === self::STATUS_HUTANG;
    }

    public static function generateNomor(): string
    {
        $tgl = now()->format('Ymd');
        $count = static::whereDate('created_at', now()->toDateString())->count() + 1;
        return 'TRX-' . $tgl . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }

    public static function generateKodeVerifikasi(): string
    {
        // Kode acak anti-pemalsuan, mis. "K7P2-9XQ4"
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            if ($i === 4) $code .= '-';
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }
}
