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
        // El decremento de stock se realiza directamente en ventaController::store()
        // dentro de la transacción DB, para garantizar atomicidad en entornos serverless.
    }
}
