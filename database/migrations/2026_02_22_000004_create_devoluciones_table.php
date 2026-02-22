<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero', 50)->unique();
            $table->foreignUuid('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('tipo', ['Devolucion', 'Cambio'])->default('Devolucion');
            $table->text('motivo');
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('estado', ['Pendiente', 'Aprobada', 'Rechazada'])->default('Pendiente');
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        Schema::create('devolucion_producto', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('devolucion_id')->constrained('devoluciones')->cascadeOnDelete();
            $table->foreignUuid('producto_id')->constrained()->cascadeOnDelete();
            $table->uuid('variante_id')->nullable();
            $table->foreign('variante_id')->references('id')->on('producto_variantes')->nullOnDelete();
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_venta', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devolucion_producto');
        Schema::dropIfExists('devoluciones');
    }
};
