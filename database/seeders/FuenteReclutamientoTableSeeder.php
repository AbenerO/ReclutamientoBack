<?php

namespace Database\Seeders;

use App\Models\FuenteReclutamiento;
use Illuminate\Database\Seeder;

class FuenteReclutamientoTableSeeder extends Seeder
{
    public function run()
    {
        FuenteReclutamiento::updateOrCreate(['nombre' => 'LinkedIn']);
        FuenteReclutamiento::updateOrCreate(['nombre' => 'Referido']);
        FuenteReclutamiento::updateOrCreate(['nombre' => 'Sitio web']);
        FuenteReclutamiento::updateOrCreate(['nombre' => 'Computrabajo']);
        FuenteReclutamiento::updateOrCreate(['nombre' => 'Facebook']);
    }
}
