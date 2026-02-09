<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
             // Change enum to string to support more payment methods easily
             $table->string('metodo_pago')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Reverting complex changes like this can be tricky if data doesn't match the original enum
            // Ideally we would want to revert to enum, but without doctrine/dbal support for enum changes fully reliable across drivers, 
            // and potential data loss if new values exist, we will restart strictly. 
            // For now, let's leave it as string or attempting to revert:
            
            // $table->enum('metodo_pago', ['EFECTIVO', 'TARJETA'])->change(); 
        });
    }
};
