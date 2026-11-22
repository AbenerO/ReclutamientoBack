<?php

namespace Database\Seeders;

use App\Models\Candidato;
use Illuminate\Database\Seeder;

class CandidatoTableSeeder extends Seeder
{
    public function run()
    {
        Candidato::updateOrCreate(
            ['email' => 'carlos.perez@gmail.com'],
            [
                'primer_nombre' => 'Carlos',
                'segundo_nombre' => 'Eduardo',
                'primer_apellido' => 'Pérez',
                'segundo_apellido' => 'López',
                'telefono' => '50255551111',
                'fecha_nacimiento' => '1995-03-12',
                'expectativa_salarial' => 6000,
            ]
        );

        Candidato::updateOrCreate(
            ['email' => 'maria.garcia@gmail.com'],
            [
                'primer_nombre' => 'María',
                'segundo_nombre' => 'Fernanda',
                'primer_apellido' => 'García',
                'segundo_apellido' => 'Morales',
                'telefono' => '50255552222',
                'fecha_nacimiento' => '1998-07-25',
                'expectativa_salarial' => 5500,
            ]
        );

        Candidato::updateOrCreate(
            ['email' => 'luis.ramirez@gmail.com'],
            [
                'primer_nombre' => 'Luis',
                'primer_apellido' => 'Ramírez',
                'telefono' => '50255553333',
                'fecha_nacimiento' => '1990-11-02',
                'expectativa_salarial' => 8000,
            ]
        );

        Candidato::updateOrCreate(
            ['email' => 'ana.hernandez@gmail.com'],
            [
                'primer_nombre' => 'Ana',
                'segundo_nombre' => 'Lucía',
                'primer_apellido' => 'Hernández',
                'apellido_casada' => 'de Castillo',
                'telefono' => '50255554444',
                'fecha_nacimiento' => '1987-01-30',
                'expectativa_salarial' => 7000,
            ]
        );
    }
}
