<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CatalogUpdateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categorías
        $categorias = [
            'Dobleprenda', 'Abullonadas', 'Rompevientos', 'Térmicas', 
            'Termo selladas', 'Audífonos lisa', 'Buzos', 'Sudaderas', 'Doble chaqueta'
        ];

        foreach ($categorias as $nombre) {
            $exists = DB::table('caracteristicas')->where('nombre', $nombre)->exists();
            if (!$exists) {
                $caracteristicaId = Str::uuid();
                DB::table('caracteristicas')->insert([
                    'id' => $caracteristicaId,
                    'nombre' => $nombre,
                    'descripcion' => 'Categoría: ' . $nombre,
                    'estado' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                DB::table('categorias')->insert([
                    'id' => Str::uuid(),
                    'caracteristica_id' => $caracteristicaId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 2. Marcas
        $marcas = [
            'Adidas', 'Nike', 'Sin marca', 'North face', 'Polo', 
            'Lacoste', 'Montclear', 'Jordan', 'Columbia', 'Tommy', 'Supreme'
        ];

        foreach ($marcas as $nombre) {
            $exists = DB::table('caracteristicas')->where('nombre', $nombre)->exists();
            if (!$exists) {
                $caracteristicaId = Str::uuid();
                DB::table('caracteristicas')->insert([
                    'id' => $caracteristicaId,
                    'nombre' => $nombre,
                    'descripcion' => 'Marca: ' . $nombre,
                    'estado' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                DB::table('marcas')->insert([
                    'id' => Str::uuid(),
                    'caracteristica_id' => $caracteristicaId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // 3. Tallas (Presentaciones)
        $tallasNacionales = ['XS', 'S', 'M', 'L', 'XL', '2XL'];
        $tallasImportadas = ['M', 'L', 'XL', '2XL', '3XL', '4XL'];

        foreach ($tallasNacionales as $sigla) {
            $nombre = 'Nacional ' . $sigla;
            $this->createTalla($nombre, $sigla);
        }

        foreach ($tallasImportadas as $sigla) {
            $nombre = 'Importada ' . $sigla;
            $this->createTalla($nombre, $sigla);
        }
    }

    private function createTalla($nombre, $sigla)
    {
        $exists = DB::table('caracteristicas')->where('nombre', $nombre)->exists();
        if (!$exists) {
            $caracteristicaId = Str::uuid();
            DB::table('caracteristicas')->insert([
                'id' => $caracteristicaId,
                'nombre' => $nombre,
                'descripcion' => 'Talla ' . $sigla,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('presentaciones')->insert([
                'id' => Str::uuid(),
                'caracteristica_id' => $caracteristicaId,
                'sigla' => $sigla,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
