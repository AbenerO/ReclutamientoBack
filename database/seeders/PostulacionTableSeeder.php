<?php

namespace Database\Seeders;

use App\Models\Candidato;
use App\Models\EstadoPostulacion;
use App\Models\FuenteReclutamiento;
use App\Models\Postulacion;
use App\Models\User;
use App\Models\Vacante;
use Illuminate\Database\Seeder;

class PostulacionTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();

        Postulacion::updateOrCreate(
            [
                'candidato_id' => Candidato::where('email', 'carlos.perez@gmail.com')->value('id'),
                'vacante_id' => Vacante::where('titulo', 'Desarrollador Laravel')->value('id'),
            ],
            [
                'fecha_postulacion' => '2026-11-01',
                'estado_postulacion_id' => EstadoPostulacion::where('nombre', 'Entrevista')->value('id'),
                'fuente_reclutamiento_id' => FuenteReclutamiento::where('nombre', 'LinkedIn')->value('id'),
                'user_id' => $user->id,
            ]
        );

        Postulacion::updateOrCreate(
            [
                'candidato_id' => Candidato::where('email', 'maria.garcia@gmail.com')->value('id'),
                'vacante_id' => Vacante::where('titulo', 'Desarrollador Laravel')->value('id'),
            ],
            [
                'fecha_postulacion' => '2026-11-03',
                'estado_postulacion_id' => EstadoPostulacion::where('nombre', 'En revisión')->value('id'),
                'fuente_reclutamiento_id' => FuenteReclutamiento::where('nombre', 'Sitio web')->value('id'),
                'user_id' => $user->id,
            ]
        );

        Postulacion::updateOrCreate(
            [
                'candidato_id' => Candidato::where('email', 'luis.ramirez@gmail.com')->value('id'),
                'vacante_id' => Vacante::where('titulo', 'Ejecutivo de Ventas')->value('id'),
            ],
            [
                'fecha_postulacion' => '2026-11-05',
                'estado_postulacion_id' => EstadoPostulacion::where('nombre', 'Recibida')->value('id'),
                'fuente_reclutamiento_id' => FuenteReclutamiento::where('nombre', 'Referido')->value('id'),
                'user_id' => $user->id,
            ]
        );

        Postulacion::updateOrCreate(
            [
                'candidato_id' => Candidato::where('email', 'ana.hernandez@gmail.com')->value('id'),
                'vacante_id' => Vacante::where('titulo', 'Analista de Reclutamiento')->value('id'),
            ],
            [
                'fecha_postulacion' => '2026-11-07',
                'estado_postulacion_id' => EstadoPostulacion::where('nombre', 'Entrevista')->value('id'),
                'fuente_reclutamiento_id' => FuenteReclutamiento::where('nombre', 'Computrabajo')->value('id'),
                'user_id' => $user->id,
            ]
        );
    }
}
