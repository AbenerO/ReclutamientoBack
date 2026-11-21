<?php

namespace Database\Seeders\AdministracionSistema\Roles;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AdministradorSeeder extends Seeder
{
    /**
     * Permisos del Administrador: gestión administrativa del sistema.
     * - No puede eliminar roles ni permisos críticos.
     * - No tiene acceso a la Sección Dev.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'Administrador', 'guard_name' => 'api']
        );

        $permisos = [
            // ========================================
            // SISTEMA (sin eliminar roles/permisos)
            // ========================================
            'ver menu',
            'crear menu',
            'editar menu',
            'ver permisos',
            'ver roles',
            'crear roles',
            'editar roles',
            'ver usuarios',
            'crear usuarios',
            'editar usuarios',
            'eliminar usuarios',

            // ========================================
            // NAVEGACIÓN
            // ========================================
            'Ver Opcion Menu Inicio',
            'Ver Menu Administración',
            'Administracion Ver Menu Usuarios',
            'Administracion Ver Menu Permisos',
            'Administracion Ver Menu Roles',
            'Administracion Ver Menu Listado Usuarios',

            // ========================================
            // NOTIFICACIONES
            // ========================================
            'ver notificaciones',
            'enviar notificaciones',

            // ========================================
            // EXTRAS
            // ========================================
            'ver favoritos',
            'crear favoritos',
            'editar favoritos',
            'eliminar favoritos',
            'Configuración Ver User Preferencias',
            'MenuPerfil Ver Opcion Perfil',
            'Ver Notificaciones',
        ];

        $permisosDB = Permission::whereIn('name', $permisos)->pluck('name')->toArray();
        $role->syncPermissions($permisosDB);
    }
}
