<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Importacion extends Model
{
    use HasUuids;

    protected $table = 'importaciones';

    protected $fillable = [
        'numero',
        'proveedor_id',
        'user_id',
        'pais_origen',
        'fecha_llegada',
        'moneda_costo',
        'tasa_cambio',
        'flete',
        'seguro',
        'arancel',
        'otros_gastos',
        'notas',
        'estado',
    ];

    protected $casts = [
        'fecha_llegada' => 'date',
        'tasa_cambio'   => 'decimal:2',
        'flete'         => 'decimal:2',
        'seguro'        => 'decimal:2',
        'arancel'       => 'decimal:2',
        'otros_gastos'  => 'decimal:2',
    ];

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedore::class, 'proveedor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'importacion_producto')
            ->withPivot('variante_id', 'cantidad', 'costo_unitario_moneda', 'costo_unitario_cop')
            ->withTimestamps();
    }

    public function getTotalGastosAttribute(): float
    {
        return (float) ($this->flete + $this->seguro + $this->arancel + $this->otros_gastos);
    }

    protected static function booted(): void
    {
        static::creating(function (Importacion $importacion) {
            if (empty($importacion->numero)) {
                $ultimo = static::count();
                $importacion->numero = 'IMP-' . str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
