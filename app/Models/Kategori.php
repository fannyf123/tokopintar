<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $fillable = ['nama', 'deskripsi', 'ikon'];

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class);
    }
}
