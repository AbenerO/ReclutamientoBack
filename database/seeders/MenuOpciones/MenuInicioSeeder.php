<?php

namespace Database\Seeders\MenuOpciones;

use App\Models\MenuOpcion;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class MenuInicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear título de sección "Principal" (elemento separado)
        $this->crearMenuConPermiso(
            ['titulo_seccion' => 'Principal'],
            [
                'titulo' => null,
                'icono' => null,
                'ruta' => null,
                'action' => 'Ver Opcion Menu Inicio',
                'subject' => 'User',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        // Crear link "Inicio"
        $this->crearMenuConPermiso(
            ['titulo' => 'Inicio', 'ruta' => 'index'],
            [
                'titulo_seccion' => null,
                'icono' => 'ri-home-9-line',
                'action' => 'Ver Opcion Menu Inicio',
                'subject' => 'User',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );
    }

    /**
     * Crea una opción de menú y su permiso correspondiente
     */
    private function crearMenuConPermiso(array $uniqueAttributes, array $values): MenuOpcion
    {
        $menuOpcion = MenuOpcion::updateOrCreate($uniqueAttributes, $values);

        $action = $values['action'] ?? $uniqueAttributes['action'] ?? null;
        $subject = $values['subject'] ?? $uniqueAttributes['subject'] ?? null;

        if ($action && $subject) {
            Permission::firstOrCreate(
                ['name' => $action],
                [
                    'subject' => $subject,
                    'guard_name' => 'api',
                ]
            );
        }

        return $menuOpcion;
    }
}
