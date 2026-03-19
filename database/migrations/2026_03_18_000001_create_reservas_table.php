<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nombre');
            $table->string('telefono', 30);
            $table->string('email')->nullable();
            $table->json('productos'); // [{id, nombre, precio, cantidad}]
            $table->decimal('total', 12, 2)->default(0);
            $table->text('notas')->nullable();
            $table->enum('estado', ['pendiente', 'contactado', 'confirmada', 'cancelada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
