<?php

namespace Database\Seeders;

use App\Models\Unidad;
use Illuminate\Database\Seeder;

class UnidadTableSeeder extends Seeder
{
    public function run()
    {
        Unidad::updateOrCreate(
            ['nombre' => 'Desarrollo de software'],
            ['descripcion' => 'Desarrollo y mantenimiento de sistemas', 'activo' => true]
        );

        Unidad::updateOrCreate(
            ['nombre' => 'Soporte técnico IT'],
            ['descripcion' => 'Soporte técnico y mantenimiento de equipos', 'activo' => true]
        );

        Unidad::updateOrCreate(
            ['nombre' => 'Contabilidad'],
            ['descripcion' => 'Contabilidad y finanzas', 'activo' => true]
        );

        Unidad::updateOrCreate(
            ['nombre' => 'Contact center'],
            ['descripcion' => 'Atención y servicio al cliente', 'activo' => true]
        );
    }
}
