<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_variantes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('producto_id')->constrained()->cascadeOnDelete();
            $table->string('talla', 20)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('sku', 100)->nullable()->unique();
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('stock_minimo')->default(2);
            $table->decimal('precio', 10, 2)->nullable()->comment('Si null, usa el precio del producto padre');
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();

            $table->index(['producto_id', 'talla', 'color']);
        });

        // Agregar variante_id nullable al pivot producto_venta
        Schema::table('producto_venta', function (Blueprint $table) {
            $table->uuid('variante_id')->nullable()->after('producto_id');
            $table->foreign('variante_id')->references('id')->on('producto_variantes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('producto_venta', function (Blueprint $table) {
            $table->dropForeign(['variante_id']);
            $table->dropColumn('variante_id');
        });
        Schema::dropIfExists('producto_variantes');
    }
};
