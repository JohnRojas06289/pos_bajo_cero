<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('productos', 'origen')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->enum('origen', ['Nacional', 'Importada'])->nullable()->after('genero');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('productos', 'origen')) {
            Schema::table('productos', function (Blueprint $table) {
                $table->dropColumn('origen');
            });
        }
    }
};
