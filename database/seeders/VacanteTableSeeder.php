<?php

namespace Database\Seeders;

use App\Models\EstadoVacante;
use App\Models\Modalidad;
use App\Models\Unidad;
use App\Models\User;
use App\Models\Vacante;
use Illuminate\Database\Seeder;

class VacanteTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();

        Vacante::updateOrCreate(
            ['titulo' => 'Desarrollador Laravel'],
            [
                'unidad_id' => Unidad::where('nombre', 'Desarrollo de software')->value('id'),
                'descripcion' => 'Se busca desarrollador Laravel para el área de Desarrollo de software.',
                'modalidad_id' => Modalidad::where('nombre', 'Híbrido')->value('id'),
                'salario_minimo' => 7000,
                'salario_maximo' => 10000,
                'ubicacion' => 'Ciudad de Guatemala',
                'estado_vacante_id' => EstadoVacante::where('nombre', 'Abierta')->value('id'),
                'user_id' => $user->id,
            ]
        );

        Vacante::updateOrCreate(
            ['titulo' => 'Técnico de Soporte'],
            [
                'unidad_id' => Unidad::where('nombre', 'Soporte técnico IT')->value('id'),
                'descripcion' => 'Se busca técnico de soporte para el área de Soporte técnico IT.',
                'modalidad_id' => Modalidad::where('nombre', 'Presencial')->value('id'),
                'salario_minimo' => 5000,
                'salario_maximo' => 7000,
                'ubicacion' => 'Ciudad de Guatemala',
                'estado_vacante_id' => EstadoVacante::where('nombre', 'Abierta')->value('id'),
                'user_id' => $user->id,
            ]
        );

        Vacante::updateOrCreate(
            ['titulo' => 'Agente de Contact Center'],
            [
                'unidad_id' => Unidad::where('nombre', 'Contact center')->value('id'),
                'descripcion' => 'Se busca agente para el área de Contact center.',
                'modalidad_id' => Modalidad::where('nombre', 'Presencial')->value('id'),
                'salario_minimo' => 4500,
                'salario_maximo' => 6500,
                'ubicacion' => 'Quetzaltenango',
                'estado_vacante_id' => EstadoVacante::where('nombre', 'Abierta')->value('id'),
                'user_id' => $user->id,
            ]
        );

        Vacante::updateOrCreate(
            ['titulo' => 'Contador General'],
            [
                'unidad_id' => Unidad::where('nombre', 'Contabilidad')->value('id'),
                'descripcion' => 'Se busca contador general para el área de Contabilidad.',
                'modalidad_id' => Modalidad::where('nombre', 'Remoto')->value('id'),
                'salario_minimo' => 6000,
                'salario_maximo' => 8500,
                'ubicacion' => 'Remoto',
                'estado_vacante_id' => EstadoVacante::where('nombre', 'Cerrada')->value('id'),
                'user_id' => $user->id,
            ]
        );
    }
}
