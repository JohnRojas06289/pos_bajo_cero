<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variantes', function (Blueprint $table) {
            $table->bigIncrements('id');

            // FK → productos (UUID)
            $table->uuid('producto_id');
            $table->foreign('producto_id')
                  ->references('id')->on('productos')
                  ->cascadeOnDelete();

            // FK → presentaciones (talla) — nullable
            $table->uuid('presentacione_id')->nullable();
            $table->foreign('presentacione_id')
                  ->references('id')->on('presentaciones')
                  ->nullOnDelete();

            $table->string('color', 100)->nullable();
            $table->string('codigo', 50)->nullable()->unique();
            $table->unsignedInteger('stock')->default(0);
            $table->string('img_path', 2048)->nullable();

            $table->timestamps();

            // Índice compuesto para búsqueda rápida en POS
            $table->index(['producto_id', 'presentacione_id', 'color'], 'idx_variante_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variantes');
    }
};
