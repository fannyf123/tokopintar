<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    public const KATEGORI_LIST = ['sewa', 'listrik', 'gaji', 'lainnya'];

    protected $fillable = [
        'kategori', 'tanggal', 'jumlah', 'catatan', 'bukti', 'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
