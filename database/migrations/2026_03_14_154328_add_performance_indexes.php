<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL with IF NOT EXISTS for PostgreSQL safety
        $indexes = [
            'ventas'        => [
                ['idx_ventas_created_at',  'created_at'],
                ['idx_ventas_user_id',     'user_id'],
                ['idx_ventas_cliente_id',  'cliente_id'],
                ['idx_ventas_caja_id',     'caja_id'],
                ['idx_ventas_metodo_pago', 'metodo_pago'],
            ],
            'productos'     => [
                ['idx_productos_estado',      'estado'],
                ['idx_productos_categoria_id','categoria_id'],
                ['idx_productos_nombre',      'nombre'],
            ],
            'inventario'    => [
                ['idx_inventario_producto_id','producto_id'],
            ],
            'kardex'        => [
                ['idx_kardex_producto_id','producto_id'],
                ['idx_kardex_created_at', 'created_at'],
            ],
            'personas'      => [
                ['idx_personas_estado',      'estado'],
                ['idx_personas_razon_social','razon_social'],
            ],
            'producto_venta' => [
                ['idx_pv_venta_id',   'venta_id'],
                ['idx_pv_producto_id','producto_id'],
            ],
        ];

        foreach ($indexes as $table => $cols) {
            foreach ($cols as [$name, $col]) {
                DB::statement("CREATE INDEX IF NOT EXISTS {$name} ON {$table} ({$col})");
            }
        }
    }

    public function down(): void
    {
        $names = [
            'idx_ventas_created_at','idx_ventas_user_id','idx_ventas_cliente_id',
            'idx_ventas_caja_id','idx_ventas_metodo_pago',
            'idx_productos_estado','idx_productos_categoria_id','idx_productos_nombre',
            'idx_inventario_producto_id',
            'idx_kardex_producto_id','idx_kardex_created_at',
            'idx_personas_estado','idx_personas_razon_social',
            'idx_pv_venta_id','idx_pv_producto_id',
        ];

        foreach ($names as $name) {
            DB::statement("DROP INDEX IF EXISTS {$name}");
        }
    }
};
