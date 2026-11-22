<?php

namespace Database\Seeders\AdministracionSistema\Roles;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReclutadorSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'Reclutador', 'guard_name' => 'api']
        );

        $permisos = [
            'Ver Opcion Menu Inicio',
            'Ver Menu Reclutamiento',

            'ver vacantes',
            'crear vacantes',
            'editar vacantes',
            'eliminar vacantes',
            'ver candidatos',
            'crear candidatos',
            'editar candidatos',
            'eliminar candidatos',
            'ver postulaciones',
            'crear postulaciones',
            'editar postulaciones',
            'ver entrevistas',
            'crear entrevistas',
            'editar entrevistas',
            'Ver Menu Catalogos',
            'ver unidades',
            'crear unidades',
            'editar unidades',
            'ver reporte postulaciones',

            'ver usuarios',

            'Configuración Ver User Preferencias',
            'MenuPerfil Ver Opcion Perfil',
        ];

        $permisosDB = Permission::whereIn('name', $permisos)->pluck('name')->toArray();
        $role->syncPermissions($permisosDB);

        $usuario = User::updateOrCreate(
            ['email' => 'reclutador@gmail.com'],
            [
                'name' => 'Reclutador Prueba',
                'username' => 'reclutador',
                'primer_nombre' => 'Reclutador',
                'primer_apellido' => 'Prueba',
                'dpi' => '1234567890101',
                'password' => bcrypt('12345'),
                'estado' => true,
            ]
        );

        $usuario->syncRoles([$role]);
    }
}
