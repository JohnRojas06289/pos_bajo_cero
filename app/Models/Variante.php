<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Variante extends Model
{
    protected $fillable = [
        'producto_id',
        'presentacione_id',
        'color',
        'codigo',
        'stock',
        'img_path',
    ];

    protected $casts = [
        'stock' => 'integer',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function presentacione(): BelongsTo
    {
        return $this->belongsTo(Presentacione::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────

    /**
     * Etiqueta corta de talla: sigla de presentación o "T.U." si no tiene.
     */
    public function getTallaLabelAttribute(): string
    {
        return $this->presentacione?->sigla ?? 'T.U.';
    }

    /**
     * Etiqueta legible para mostrar en UI: "M / Negro", "T.U. / Rojo", etc.
     */
    public function getLabelAttribute(): string
    {
        $parts = array_filter([
            $this->presentacione?->sigla,
            $this->color,
        ]);
        return implode(' / ', $parts) ?: 'Única';
    }

    /**
     * URL de imagen: primero la propia, luego la del producto base.
     */
    public function getImageUrlAttribute(): string
    {
        $path = $this->img_path ?: $this->producto?->img_path;
        if (empty($path)) return '';
        return $this->producto?->resolveImageUrl($path) ?? '';
    }

    /**
     * Indica si la variante está disponible (tiene stock).
     */
    public function getDisponibleAttribute(): bool
    {
        return $this->stock > 0;
    }
}
