<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // En PostgreSQL, la columna enum original dejó un check constraint
        // (ventas_metodo_pago_check) que solo acepta 'EFECTIVO' y 'TARJETA'.
        // La migración anterior cambió el tipo a string pero no eliminó ese constraint.
        // Lo eliminamos aquí para que todos los valores del MetodoPagoEnum sean válidos.
        DB::statement('ALTER TABLE ventas DROP CONSTRAINT IF EXISTS ventas_metodo_pago_check');
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE ventas ADD CONSTRAINT ventas_metodo_pago_check
             CHECK (metodo_pago IN ('EFECTIVO','TARJETA'))"
        );
    }
};
