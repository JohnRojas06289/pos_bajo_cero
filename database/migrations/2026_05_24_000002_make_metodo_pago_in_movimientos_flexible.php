<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            // Permitir más métodos de pago que el enum original EFECTIVO/TARJETA.
            $table->string('metodo_pago')->change();
        });

        // En PostgreSQL, el enum original deja un check constraint.
        DB::statement('ALTER TABLE movimientos DROP CONSTRAINT IF EXISTS movimientos_metodo_pago_check');
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE movimientos ADD CONSTRAINT movimientos_metodo_pago_check
             CHECK (metodo_pago IN ('EFECTIVO','TARJETA'))"
        );
    }
};
