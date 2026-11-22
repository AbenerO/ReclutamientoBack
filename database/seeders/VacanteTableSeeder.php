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
                'unidad_id' => Unidad::where('nombre', 'Tecnología')->value('id'),
                'descripcion' => 'Se busca desarrollador Laravel para el área de Tecnología.',
                'modalidad_id' => Modalidad::where('nombre', 'Híbrido')->value('id'),
                'salario_minimo' => 7000,
                'salario_maximo' => 10000,
                'ubicacion' => 'Ciudad de Guatemala',
                'estado_vacante_id' => EstadoVacante::where('nombre', 'Abierta')->value('id'),
                'user_id' => $user->id,
            ]
        );

        Vacante::updateOrCreate(
            ['titulo' => 'Analista de Reclutamiento'],
            [
                'unidad_id' => Unidad::where('nombre', 'Recursos Humanos')->value('id'),
                'descripcion' => 'Se busca analista de reclutamiento para el área de Recursos Humanos.',
                'modalidad_id' => Modalidad::where('nombre', 'Presencial')->value('id'),
                'salario_minimo' => 5000,
                'salario_maximo' => 7000,
                'ubicacion' => 'Ciudad de Guatemala',
                'estado_vacante_id' => EstadoVacante::where('nombre', 'Abierta')->value('id'),
                'user_id' => $user->id,
            ]
        );

        Vacante::updateOrCreate(
            ['titulo' => 'Ejecutivo de Ventas'],
            [
                'unidad_id' => Unidad::where('nombre', 'Ventas')->value('id'),
                'descripcion' => 'Se busca ejecutivo de ventas para el área comercial.',
                'modalidad_id' => Modalidad::where('nombre', 'Presencial')->value('id'),
                'salario_minimo' => 4500,
                'salario_maximo' => 6500,
                'ubicacion' => 'Quetzaltenango',
                'estado_vacante_id' => EstadoVacante::where('nombre', 'En proceso')->value('id'),
                'user_id' => $user->id,
            ]
        );

        Vacante::updateOrCreate(
            ['titulo' => 'Contador General'],
            [
                'unidad_id' => Unidad::where('nombre', 'Finanzas')->value('id'),
                'descripcion' => 'Se busca contador general para el área de Finanzas.',
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
