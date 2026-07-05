<?php

namespace App\Listeners;

use App\Enums\TipoTransaccionEnum;
use App\Events\CreateVentaDetalleEvent;
use App\Models\Kardex;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateRegistroVentaCardexListener
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
    public function handle(CreateVentaDetalleEvent $event): void
    {
        try {
            $kardex = new Kardex();

            $ultimoRegistro = $kardex->where('producto_id', $event->producto_id)
                ->latest('id')
                ->first();

            // Si no hay registros previos en kardex, usar 0 como costo de adquisición.
            // Usar precio_venta como costo sería incorrecto y distorsionaría el margen.
            if (!$ultimoRegistro) {
                \Log::warning('CreateRegistroVentaCardexListener: sin registro previo en kardex, costo_unitario=0', [
                    'producto_id' => $event->producto_id,
                ]);
            }
            $costoUnitario = $ultimoRegistro ? $ultimoRegistro->costo_unitario : 0;

            $kardex->crearRegistro(
                [
                    'venta_id' => $event->venta->id,
                    'producto_id' => $event->producto_id,
                    'cantidad' => $event->cantidad,
                    'costo_unitario' => $costoUnitario
                ],
                TipoTransaccionEnum::Venta
            );
            
            \Log::info('CreateRegistroVentaCardexListener: Kardex created', [
                'producto_id' => $event->producto_id,
                'cantidad' => $event->cantidad
            ]);
        } catch (\Exception $e) {
            \Log::error('CreateRegistroVentaCardexListener: Error', ['error' => $e->getMessage()]);
        }
    }
}
