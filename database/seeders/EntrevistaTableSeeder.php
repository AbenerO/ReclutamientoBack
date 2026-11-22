<?php

namespace Database\Seeders;

use App\Models\Candidato;
use App\Models\Entrevista;
use App\Models\Postulacion;
use App\Models\ResultadoEntrevista;
use App\Models\TipoEntrevista;
use App\Models\User;
use Illuminate\Database\Seeder;

class EntrevistaTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();

        $carlos = Postulacion::where('candidato_id', Candidato::where('email', 'carlos.perez@gmail.com')->value('id'))->value('id');
        $ana = Postulacion::where('candidato_id', Candidato::where('email', 'ana.hernandez@gmail.com')->value('id'))->value('id');

        Entrevista::updateOrCreate(
            ['postulacion_id' => $carlos, 'fecha_hora' => '2026-11-10 09:00:00'],
            [
                'tipo_entrevista_id' => TipoEntrevista::where('nombre', 'Técnica')->value('id'),
                'entrevistador_id' => $user->id,
                'resultado_entrevista_id' => ResultadoEntrevista::where('nombre', 'Aprobado')->value('id'),
                'observaciones' => 'Buen nivel en Laravel.',
                'user_id' => $user->id,
            ]
        );

        Entrevista::updateOrCreate(
            ['postulacion_id' => $ana, 'fecha_hora' => '2026-11-12 14:30:00'],
            [
                'tipo_entrevista_id' => TipoEntrevista::where('nombre', 'Recursos Humanos')->value('id'),
                'entrevistador_id' => $user->id,
                'resultado_entrevista_id' => ResultadoEntrevista::where('nombre', 'Pendiente')->value('id'),
                'observaciones' => null,
                'user_id' => $user->id,
            ]
        );
    }
}
