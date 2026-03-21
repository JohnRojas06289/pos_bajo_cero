<?php

namespace App\Listeners;

use App\Events\CreateVentaDetalleEvent;
use App\Models\Inventario;
use App\Models\Variante;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateInventarioVentaListener
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
            // 1. Descontar stock de la variante específica
            if ($event->variante_id) {
                $variante = Variante::find($event->variante_id);
                if ($variante) {
                    $variante->decrement('stock', $event->cantidad);
                    \Log::info('Stock variante descontado', [
                        'variante_id' => $event->variante_id,
                        'cantidad'    => $event->cantidad,
                        'stock_nuevo' => $variante->stock - $event->cantidad,
                    ]);
                }
            } else {
                // Fallback: si no hay variante_id, descontar de la primera variante del producto
                $variante = Variante::where('producto_id', $event->producto_id)->first();
                if ($variante) {
                    $variante->decrement('stock', $event->cantidad);
                }
            }

            // 2. Mantener inventario.cantidad sincronizado (para kardex/compras)
            $registro = Inventario::where('producto_id', $event->producto_id)->first();
            if ($registro) {
                $nuevaCantidad = max(0, $registro->cantidad - $event->cantidad);
                $registro->update(['cantidad' => $nuevaCantidad]);
            }
        } catch (\Exception $e) {
            \Log::error('UpdateInventarioVentaListener: Error', ['error' => $e->getMessage()]);
        }
    }
}
