<?php

namespace App\Listeners;

use App\Enums\TipoMovimientoEnum;
use App\Events\CreateVentaEvent;
use App\Models\Movimiento;
use Exception;
use Illuminate\Support\Facades\Log;

class CreateMovimientoVentaCajaListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CreateVentaEvent $event): void
    {
        try {
            if (!$event->venta->caja_id) {
                Log::error('CreateMovimientoVentaCajaListener: La venta no tiene caja asociada', [
                    'venta_id' => $event->venta->id,
                    'user_id' => $event->venta->user_id,
                ]);
                return;
            }

            Movimiento::create([
                'tipo' => TipoMovimientoEnum::Venta,
                'descripcion' => 'Venta n° ' . $event->venta->numero_comprobante,
                'monto' => $event->venta->total,
                'metodo_pago' => $event->venta->metodo_pago,
                'caja_id' => $event->venta->caja_id,
            ]);
        } catch (Exception $e) {
            Log::error(
                'Error en el Listener CreateMovimientoVentaCajaListener',
                ['error' => $e->getMessage()]
            );
        }
    }
}
