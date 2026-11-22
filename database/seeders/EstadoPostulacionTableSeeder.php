<?php

namespace Database\Seeders;

use App\Models\EstadoPostulacion;
use Illuminate\Database\Seeder;

class EstadoPostulacionTableSeeder extends Seeder
{
    public function run()
    {
        EstadoPostulacion::updateOrCreate(['nombre' => 'Recibida'], ['orden' => 1]);
        EstadoPostulacion::updateOrCreate(['nombre' => 'En revisión'], ['orden' => 2]);
        EstadoPostulacion::updateOrCreate(['nombre' => 'Entrevista'], ['orden' => 3]);
        EstadoPostulacion::updateOrCreate(['nombre' => 'Oferta'], ['orden' => 4]);
        EstadoPostulacion::updateOrCreate(['nombre' => 'Contratado'], ['orden' => 5]);
        EstadoPostulacion::updateOrCreate(['nombre' => 'Rechazado'], ['orden' => 6]);
    }
}
