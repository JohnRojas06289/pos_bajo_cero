<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoVariante extends Model
{
    use HasUuids;

    protected $table = 'producto_variantes';

    protected $fillable = [
        'producto_id',
        'talla',
        'color',
        'sku',
        'stock',
        'stock_minimo',
        'precio',
        'estado',
    ];

    protected $casts = [
        'stock'        => 'integer',
        'stock_minimo' => 'integer',
        'precio'       => 'decimal:2',
        'estado'       => 'integer',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function getPrecioFinalAttribute(): float
    {
        return $this->precio ?? (float) ($this->producto->precio ?? 0);
    }

    public function getNombreCompletoAttribute(): string
    {
        $partes = array_filter([$this->talla, $this->color]);
        $detalle = $partes ? ' (' . implode(' - ', $partes) . ')' : '';
        return ($this->producto->nombre ?? '') . $detalle;
    }

    public function isBajoStock(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }
}
