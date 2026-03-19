<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Reserva extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'productos',
        'total',
        'notas',
        'estado',
    ];

    protected $casts = [
        'productos' => 'array',
        'total'     => 'decimal:2',
    ];

    public function getEstadoBadgeAttribute(): string
    {
        return match ($this->estado) {
            'pendiente'   => 'warning',
            'contactado'  => 'info',
            'confirmada'  => 'success',
            'cancelada'   => 'danger',
            default       => 'secondary',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'pendiente'   => 'Pendiente',
            'contactado'  => 'Contactado',
            'confirmada'  => 'Confirmada',
            'cancelada'   => 'Cancelada',
            default       => $this->estado,
        };
    }
}
