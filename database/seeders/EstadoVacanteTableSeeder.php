<?php

namespace Database\Seeders;

use App\Models\EstadoVacante;
use Illuminate\Database\Seeder;

class EstadoVacanteTableSeeder extends Seeder
{
    public function run()
    {
        EstadoVacante::updateOrCreate(['nombre' => 'Abierta']);
        EstadoVacante::updateOrCreate(['nombre' => 'En proceso']);
        EstadoVacante::updateOrCreate(['nombre' => 'Cerrada']);
    }
}
