<?php

namespace Database\Seeders;

use App\Models\Unidad;
use Illuminate\Database\Seeder;

class UnidadTableSeeder extends Seeder
{
    public function run()
    {
        Unidad::updateOrCreate(
            ['nombre' => 'Tecnología'],
            ['descripcion' => 'Desarrollo y soporte de sistemas', 'activo' => true]
        );

        Unidad::updateOrCreate(
            ['nombre' => 'Recursos Humanos'],
            ['descripcion' => 'Gestión del talento humano', 'activo' => true]
        );

        Unidad::updateOrCreate(
            ['nombre' => 'Ventas'],
            ['descripcion' => 'Área comercial', 'activo' => true]
        );

        Unidad::updateOrCreate(
            ['nombre' => 'Finanzas'],
            ['descripcion' => 'Contabilidad y finanzas', 'activo' => true]
        );
    }
}
