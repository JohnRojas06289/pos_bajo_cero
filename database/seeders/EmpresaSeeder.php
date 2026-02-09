<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $moneda = \App\Models\Moneda::where('estandar_iso', 'COP')->first();
        Empresa::insert([
            'id' => \Illuminate\Support\Str::uuid(),
            'nombre' => 'Bajo Cero',
            'propietario' => 'Administrador',
            'ruc' => '00000000',
            'direccion' => 'Ciudad',
            'moneda_id' => $moneda->id
        ]);
    }
}
