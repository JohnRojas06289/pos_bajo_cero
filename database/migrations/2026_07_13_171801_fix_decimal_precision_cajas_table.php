<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->decimal('saldo_inicial', 15, 2)->change();
            $table->decimal('saldo_final', 15, 2)->nullable()->change();
        });

        Schema::table('movimientos', function (Blueprint $table) {
            $table->decimal('monto', 15, 2)->change();
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
            $table->decimal('monto_recibido', 15, 2)->change();
            $table->decimal('vuelto_entregado', 15, 2)->change();
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });

        Schema::table('kardexes', function (Blueprint $table) {
            $table->decimal('costo_unitario', 15, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->decimal('saldo_inicial', 8, 2)->change();
            $table->decimal('saldo_final', 8, 2)->nullable()->change();
        });

        Schema::table('movimientos', function (Blueprint $table) {
            $table->decimal('monto', 8, 2)->change();
        });

        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('subtotal', 8, 2)->change();
            $table->decimal('total', 8, 2)->change();
            $table->decimal('monto_recibido', 8, 2)->change();
            $table->decimal('vuelto_entregado', 8, 2)->change();
        });

        Schema::table('compras', function (Blueprint $table) {
            $table->decimal('subtotal', 8, 2)->change();
            $table->decimal('total', 8, 2)->change();
        });

        Schema::table('kardexes', function (Blueprint $table) {
            $table->decimal('costo_unitario', 8, 2)->change();
        });
    }
};
