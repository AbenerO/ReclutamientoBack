<?php

namespace Database\Seeders;

use App\Models\TipoEntrevista;
use Illuminate\Database\Seeder;

class TipoEntrevistaTableSeeder extends Seeder
{
    public function run()
    {
        TipoEntrevista::updateOrCreate(['nombre' => 'Recursos Humanos']);
        TipoEntrevista::updateOrCreate(['nombre' => 'Técnica']);
    }
}
