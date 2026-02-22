<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Devolucion extends Model
{
    use HasUuids;

    protected $table = 'devoluciones';

    protected $fillable = [
        'numero',
        'venta_id',
        'user_id',
        'tipo',
        'motivo',
        'total',
        'estado',
        'notas',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'devolucion_producto')
            ->withPivot('variante_id', 'cantidad', 'precio_venta')
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::creating(function (Devolucion $devolucion) {
            if (empty($devolucion->numero)) {
                $ultimo = static::count();
                $devolucion->numero = 'DEV-' . str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);
            }
            if (empty($devolucion->user_id)) {
                $devolucion->user_id = auth()->id();
            }
        });
    }
}
