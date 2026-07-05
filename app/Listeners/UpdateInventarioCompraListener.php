<?php

namespace App\Listeners;

use App\Events\CreateCompraDetalleEvent;
use App\Models\Inventario;
use App\Models\Variante;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class UpdateInventarioCompraListener
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(CreateCompraDetalleEvent $event): void
    {
        try {
            DB::transaction(function () use ($event) {
                // 1. Actualizar inventario.cantidad (registro histórico)
                $registro = Inventario::where('producto_id', $event->producto_id)->first();
                if ($registro) {
                    $registro->update([
                        'cantidad' => $registro->cantidad + $event->cantidad,
                        'fecha_vencimiento' => $event->fecha_vencimiento
                    ]);
                }

                // 2. Actualizar variante.stock (fuente de verdad del POS)
                $variantes = Variante::where('producto_id', $event->producto_id)
                    ->orderBy('stock', 'asc')
                    ->get();

                if ($variantes->isEmpty()) {
                    \Log::warning('UpdateInventarioCompraListener: No variantes found', ['producto_id' => $event->producto_id]);
                    return;
                }

                // Agregar a la variante de menor stock (única o múltiples)
                $variantes->first()->increment('stock', $event->cantidad);
            });

            \Log::info('UpdateInventarioCompraListener: variante stock updated', [
                'producto_id' => $event->producto_id,
                'cantidad_agregada' => $event->cantidad,
            ]);
        } catch (\Exception $e) {
            \Log::error('UpdateInventarioCompraListener: Error', ['error' => $e->getMessage()]);
        }
    }
}
