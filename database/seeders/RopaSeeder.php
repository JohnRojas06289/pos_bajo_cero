<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Presentacione;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RopaSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categorías
        $categorias = ['Chaquetas', 'Buzos', 'Accesorios', 'Pantalones'];
        $categoriaIds = [];

        foreach ($categorias as $nombre) {
            $caracteristicaId = Str::uuid();
            DB::table('caracteristicas')->insert([
                'id' => $caracteristicaId,
                'nombre' => $nombre,
                'descripcion' => 'Categoría de prueba ' . $nombre,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $catId = Str::uuid();
            DB::table('categorias')->insert([
                'id' => $catId,
                'caracteristica_id' => $caracteristicaId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $categoriaIds[$nombre] = $catId;
        }

        // 2. Marcas
        $marcas = ['Bajo Cero', 'The North Face', 'Columbia', 'Patagonia'];
        $marcaIds = [];

        foreach ($marcas as $nombre) {
            $caracteristicaId = Str::uuid();
            DB::table('caracteristicas')->insert([
                'id' => $caracteristicaId,
                'nombre' => $nombre,
                'descripcion' => 'Marca reconocida ' . $nombre,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $marcaId = Str::uuid();
            DB::table('marcas')->insert([
                'id' => $marcaId,
                'caracteristica_id' => $caracteristicaId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $marcaIds[$nombre] = $marcaId;
        }

        // 3. Presentaciones (Tallas)
        $tallas = [
            'Talla S' => 'S',
            'Talla M' => 'M',
            'Talla L' => 'L',
            'Talla XL' => 'XL',
            'Unidad' => 'UND'
        ];
        $presentacionIds = [];

        foreach ($tallas as $nombre => $sigla) {
            $caracteristicaId = Str::uuid();
            DB::table('caracteristicas')->insert([
                'id' => $caracteristicaId,
                'nombre' => $nombre,
                'descripcion' => 'Talla ' . $sigla,
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $presId = Str::uuid();
            DB::table('presentaciones')->insert([
                'id' => $presId,
                'caracteristica_id' => $caracteristicaId,
                'sigla' => $sigla,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $presentacionIds[$sigla] = $presId;
        }

        // 4. Productos
        $productos = [
            [
                'codigo' => 'CHAQ-001',
                'nombre' => 'Chaqueta Puffer Impermeable',
                'descripcion' => 'Chaqueta térmica ideal para bajas temperaturas.',
                'precio' => 150000,
                'marca' => 'Bajo Cero',
                'categoria' => 'Chaquetas',
                'talla' => 'M'
            ],
            [
                'codigo' => 'CHAQ-002',
                'nombre' => 'Chaqueta Cortavientos',
                'descripcion' => 'Ligera y resistente al viento.',
                'precio' => 95000,
                'marca' => 'The North Face',
                'categoria' => 'Chaquetas',
                'talla' => 'L'
            ],
            [
                'codigo' => 'BUZO-001',
                'nombre' => 'Buzo Hoodie Clásico',
                'descripcion' => 'Algodón perchado premium.',
                'precio' => 65000,
                'marca' => 'Bajo Cero',
                'categoria' => 'Buzos',
                'talla' => 'M'
            ],
             [
                'codigo' => 'ACC-001',
                'nombre' => 'Gorro de Lana',
                'descripcion' => 'Tejido grueso para invierno.',
                'precio' => 25000,
                'marca' => 'Columbia',
                'categoria' => 'Accesorios',
                'talla' => 'UND'
            ]
        ];

        foreach ($productos as $prod) {
            DB::table('productos')->insert([
                'id' => Str::uuid(),
                'codigo' => $prod['codigo'],
                'nombre' => $prod['nombre'],
                'descripcion' => $prod['descripcion'],
                'img_path' => null, // Podemos agregar una imagen placeholder despues
                'estado' => 1,
                'precio' => $prod['precio'],
                'marca_id' => $marcaIds[$prod['marca']],
                'presentacione_id' => $presentacionIds[$prod['talla']],
                'categoria_id' => $categoriaIds[$prod['categoria']],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
