<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero', 50)->unique();
            $table->foreignUuid('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pais_origen', 100);
            $table->date('fecha_llegada');
            $table->enum('moneda_costo', ['COP', 'USD', 'EUR', 'CNY'])->default('USD');
            $table->decimal('tasa_cambio', 12, 2)->default(1)->comment('TRM al momento de la importación');
            $table->decimal('flete', 12, 2)->default(0)->comment('Costo de transporte');
            $table->decimal('seguro', 12, 2)->default(0);
            $table->decimal('arancel', 12, 2)->default(0)->comment('Impuesto de importación');
            $table->decimal('otros_gastos', 12, 2)->default(0);
            $table->text('notas')->nullable();
            $table->enum('estado', ['Pendiente', 'En Tránsito', 'Recibida', 'Cancelada'])->default('Pendiente');
            $table->timestamps();
        });

        Schema::create('importacion_producto', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('importacion_id')->constrained('importaciones')->cascadeOnDelete();
            $table->foreignUuid('producto_id')->constrained()->cascadeOnDelete();
            $table->uuid('variante_id')->nullable();
            $table->foreign('variante_id')->references('id')->on('producto_variantes')->nullOnDelete();
            $table->unsignedInteger('cantidad');
            $table->decimal('costo_unitario_moneda', 12, 2)->comment('Precio en moneda de compra');
            $table->decimal('costo_unitario_cop', 12, 2)->comment('Precio convertido a COP + gastos proporcionales');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importacion_producto');
        Schema::dropIfExists('importaciones');
    }
};
