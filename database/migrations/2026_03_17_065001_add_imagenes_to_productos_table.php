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
        Schema::table('productos', function (Blueprint $table) {
            // JSON array of additional image paths (beyond the main img_path)
            $table->text('imagenes')->nullable()->after('img_path');
            // Extend descripcion to text so AI-generated content fits
            $table->text('descripcion')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('imagenes');
            $table->string('descripcion')->nullable()->change();
        });
    }
};
