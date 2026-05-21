<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssociationRule extends Model
{
    protected $fillable = [
        'antecedent_barang_id', 'consequent_barang_id',
        'support', 'confidence', 'lift', 'co_count', 'dihitung_pada',
    ];

    protected $casts = [
        'support' => 'float',
        'confidence' => 'float',
        'lift' => 'float',
        'co_count' => 'integer',
        'dihitung_pada' => 'datetime',
    ];

    public function antecedent(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'antecedent_barang_id');
    }

    public function consequent(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'consequent_barang_id');
    }
}
