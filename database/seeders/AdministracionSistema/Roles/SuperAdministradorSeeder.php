<?php

namespace Database\Seeders\AdministracionSistema\Roles;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdministradorSeeder extends Seeder
{
    /**
     * Permisos del SuperAdministrador: TODOS los permisos del sistema
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'SuperAdministrador', 'guard_name' => 'api']
        );

        // SuperAdmin tiene TODOS los permisos
        $todosLosPermisos = Permission::all();
        $role->syncPermissions($todosLosPermisos);

        // Asignar rol al primer usuario (admin principal)
        $adminUser = User::first();
        if ($adminUser) {
            $adminUser->syncRoles([$role]);
        }
    }
}