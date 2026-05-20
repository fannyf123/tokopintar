<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = ['nama', 'kontak', 'no_hp', 'email', 'alamat'];

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class);
    }

    public function pembelians(): HasMany
    {
        return $this->hasMany(Pembelian::class);
    }
}
