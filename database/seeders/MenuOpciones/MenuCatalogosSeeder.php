<?php

namespace Database\Seeders\MenuOpciones;

use App\Models\MenuOpcion;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class MenuCatalogosSeeder extends Seeder
{
    public function run(): void
    {
        // Título de la sección en el menú
        $this->crearMenuConPermiso(
            ['titulo_seccion' => 'Catálogos'],
            [
                'titulo' => null,
                'icono' => null,
                'ruta' => null,
                'action' => 'Ver Menu Catalogos',
                'subject' => 'MenuOpcion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Unidades'],
            [
                'titulo_seccion' => null,
                'ruta' => 'unidades',
                'icono' => 'ri-building-line',
                'action' => 'Ver Menu Catalogos',
                'subject' => 'MenuOpcion',
                'parent_id' => null,
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
