<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Migración de datos: convierte cada producto existente en
 * producto_base + 1 variante con su talla, color y stock actuales.
 *
 * Idempotente: no crea variantes si ya existen para ese producto.
 */
class MigrateVariantesSeeder extends Seeder
{
    public function run(): void
    {
        $productos = DB::table('productos')->get();

        foreach ($productos as $producto) {
            // Saltar si ya tiene al menos una variante
            $exists = DB::table('variantes')
                ->where('producto_id', $producto->id)
                ->exists();

            if ($exists) {
                $this->command->line("  Skipping {$producto->nombre} (ya tiene variantes)");
                continue;
            }

            // Obtener stock del inventario actual
            $inventario = DB::table('inventario')
                ->where('producto_id', $producto->id)
                ->first();

            $stock = $inventario ? (int) $inventario->cantidad : 0;

            // Crear la variante inicial
            DB::table('variantes')->insert([
                'producto_id'       => $producto->id,
                'presentacione_id'  => $producto->presentacione_id ?? null,
                'color'             => ($producto->color !== '' ? $producto->color : null),
                'codigo'            => null, // El código vive en productos; aquí se usa para SKUs nuevos
                'stock'             => $stock,
                'img_path'          => null, // La imagen principal vive en productos
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $this->command->info("  ✓ {$producto->nombre} → variante creada (stock: {$stock})");
        }

        $total = DB::table('variantes')->count();
        $this->command->info("Migración completada: {$total} variantes en total.");
    }
}
