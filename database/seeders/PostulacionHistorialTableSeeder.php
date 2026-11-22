<?php

namespace Database\Seeders;

use App\Models\Candidato;
use App\Models\EstadoPostulacion;
use App\Models\Postulacion;
use App\Models\PostulacionHistorial;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostulacionHistorialTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();

        $recibida = EstadoPostulacion::where('nombre', 'Recibida')->value('id');
        $enRevision = EstadoPostulacion::where('nombre', 'En revisión')->value('id');
        $entrevista = EstadoPostulacion::where('nombre', 'Entrevista')->value('id');

        $carlos = Postulacion::where('candidato_id', Candidato::where('email', 'carlos.perez@gmail.com')->value('id'))->value('id');
        $maria = Postulacion::where('candidato_id', Candidato::where('email', 'maria.garcia@gmail.com')->value('id'))->value('id');
        $luis = Postulacion::where('candidato_id', Candidato::where('email', 'luis.ramirez@gmail.com')->value('id'))->value('id');
        $ana = Postulacion::where('candidato_id', Candidato::where('email', 'ana.hernandez@gmail.com')->value('id'))->value('id');

        // Carlos: Recibida -> En revisión -> Entrevista
        PostulacionHistorial::firstOrCreate(['postulacion_id' => $carlos, 'estado_anterior_id' => null, 'estado_nuevo_id' => $recibida, 'user_id' => $user->id]);
        PostulacionHistorial::firstOrCreate(['postulacion_id' => $carlos, 'estado_anterior_id' => $recibida, 'estado_nuevo_id' => $enRevision, 'user_id' => $user->id]);
        PostulacionHistorial::firstOrCreate(['postulacion_id' => $carlos, 'estado_anterior_id' => $enRevision, 'estado_nuevo_id' => $entrevista, 'user_id' => $user->id]);

        // María: Recibida -> En revisión
        PostulacionHistorial::firstOrCreate(['postulacion_id' => $maria, 'estado_anterior_id' => null, 'estado_nuevo_id' => $recibida, 'user_id' => $user->id]);
        PostulacionHistorial::firstOrCreate(['postulacion_id' => $maria, 'estado_anterior_id' => $recibida, 'estado_nuevo_id' => $enRevision, 'user_id' => $user->id]);

        // Luis: Recibida
        PostulacionHistorial::firstOrCreate(['postulacion_id' => $luis, 'estado_anterior_id' => null, 'estado_nuevo_id' => $recibida, 'user_id' => $user->id]);

        // Ana: Recibida -> En revisión -> Entrevista
        PostulacionHistorial::firstOrCreate(['postulacion_id' => $ana, 'estado_anterior_id' => null, 'estado_nuevo_id' => $recibida, 'user_id' => $user->id]);
        PostulacionHistorial::firstOrCreate(['postulacion_id' => $ana, 'estado_anterior_id' => $recibida, 'estado_nuevo_id' => $enRevision, 'user_id' => $user->id]);
        PostulacionHistorial::firstOrCreate(['postulacion_id' => $ana, 'estado_anterior_id' => $enRevision, 'estado_nuevo_id' => $entrevista, 'user_id' => $user->id]);
    }
}
