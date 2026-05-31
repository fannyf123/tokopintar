<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    public const TIPE_UMUM = 'umum';
    public const TIPE_MEMBER = 'member';

    protected $fillable = ['nama', 'no_hp', 'alamat', 'tipe', 'diskon_persen', 'poin', 'total_belanja'];

    protected $casts = [
        'total_belanja' => 'integer',
        'diskon_persen' => 'integer',
        'poin' => 'integer',
    ];

    public function penjualans(): HasMany
    {
        return $this->hasMany(Penjualan::class);
    }
}
