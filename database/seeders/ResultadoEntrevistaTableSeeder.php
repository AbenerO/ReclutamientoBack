<?php

namespace Database\Seeders;

use App\Models\ResultadoEntrevista;
use Illuminate\Database\Seeder;

class ResultadoEntrevistaTableSeeder extends Seeder
{
    public function run()
    {
        ResultadoEntrevista::updateOrCreate(['nombre' => 'Pendiente']);
        ResultadoEntrevista::updateOrCreate(['nombre' => 'Aprobado']);
        ResultadoEntrevista::updateOrCreate(['nombre' => 'Rechazado']);
    }
}
