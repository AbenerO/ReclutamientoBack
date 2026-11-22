<?php

namespace Database\Seeders;

use App\Models\Modalidad;
use Illuminate\Database\Seeder;

class ModalidadTableSeeder extends Seeder
{
    public function run()
    {
        Modalidad::updateOrCreate(['nombre' => 'Presencial']);
        Modalidad::updateOrCreate(['nombre' => 'Remoto']);
        Modalidad::updateOrCreate(['nombre' => 'Híbrido']);
    }
}
