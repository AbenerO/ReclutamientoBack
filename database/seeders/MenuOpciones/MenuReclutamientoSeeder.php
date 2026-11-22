<?php

namespace Database\Seeders\MenuOpciones;

use App\Models\MenuOpcion;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class MenuReclutamientoSeeder extends Seeder
{
    public function run(): void
    {
        // Título de la sección en el menú
        $this->crearMenuConPermiso(
            ['titulo_seccion' => 'Reclutamiento'],
            [
                'titulo' => null,
                'icono' => null,
                'ruta' => null,
                'action' => 'Ver Menu Reclutamiento',
                'subject' => 'MenuOpcion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        // Opción padre con sus hijos
        $moduloReclutamiento = $this->crearMenuConPermiso(
            ['titulo' => 'Módulo Reclutamiento'],
            [
                'titulo_seccion' => null,
                'ruta' => null,
                'icono' => 'ri-briefcase-line',
                'action' => 'Ver Menu Reclutamiento',
                'subject' => 'MenuOpcion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Vacantes', 'parent_id' => $moduloReclutamiento->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'vacantes',
                'icono' => 'ri-suitcase-line',
                'action' => 'ver vacantes',
                'subject' => 'Vacante',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Candidatos', 'parent_id' => $moduloReclutamiento->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'candidatos',
                'icono' => 'ri-user-search-line',
                'action' => 'ver candidatos',
                'subject' => 'Candidato',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Postulaciones', 'parent_id' => $moduloReclutamiento->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'postulaciones',
                'icono' => 'ri-file-user-line',
                'action' => 'ver postulaciones',
                'subject' => 'Postulacion',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );
    }

    private function crearMenuConPermiso(array $uniqueAttributes, array $values): MenuOpcion
    {
        $menuOpcion = MenuOpcion::updateOrCreate($uniqueAttributes, $values);

        $action = $values['action'] ?? null;
        $subject = $values['subject'] ?? null;

        if ($action && $subject) {
            Permission::firstOrCreate(
                ['name' => $action],
                ['subject' => $subject, 'guard_name' => 'api']
            );
        }

        return $menuOpcion;
    }
}
