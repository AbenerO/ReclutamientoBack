<?php

namespace Database\Seeders\AdministracionSistema;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermisosSeeder extends Seeder
{
    /**
     * Definición de todos los permisos del sistema organizados por módulo
     */
    public static function getPermisos(): array
    {
        return [
            // ========================================
            // MÓDULO: SISTEMA (Configuración)
            // ========================================
            'sistema' => [
                // Menú
                ['name' => 'ver menu', 'subject' => 'MenuOpcion'],
                ['name' => 'crear menu', 'subject' => 'MenuOpcion'],
                ['name' => 'editar menu', 'subject' => 'MenuOpcion'],
                ['name' => 'eliminar menu', 'subject' => 'MenuOpcion'],

                // Permisos
                ['name' => 'ver permisos', 'subject' => 'Permission'],
                ['name' => 'crear permisos', 'subject' => 'Permission'],
                ['name' => 'editar permisos', 'subject' => 'Permission'],
                ['name' => 'eliminar permisos', 'subject' => 'Permission'],

                // Roles
                ['name' => 'ver roles', 'subject' => 'Role'],
                ['name' => 'crear roles', 'subject' => 'Role'],
                ['name' => 'editar roles', 'subject' => 'Role'],
                ['name' => 'eliminar roles', 'subject' => 'Role'],

                // Usuarios
                ['name' => 'ver usuarios', 'subject' => 'User'],
                ['name' => 'crear usuarios', 'subject' => 'User'],
                ['name' => 'editar usuarios', 'subject' => 'User'],
                ['name' => 'eliminar usuarios', 'subject' => 'User'],
            ],

            // ========================================
            // MÓDULO: NOTIFICACIONES
            // ========================================
            'notificaciones' => [
                ['name' => 'ver notificaciones', 'subject' => 'Notificacion'],
                ['name' => 'enviar notificaciones', 'subject' => 'Notificacion'],
            ],

            // ========================================
            // MÓDULO: NAVEGACIÓN (Menús del sidebar)
            // ========================================
            'navegacion' => [
                ['name' => 'Ver Opcion Menu Inicio', 'subject' => 'User'],
                ['name' => 'Ver Menu Administración', 'subject' => 'MenuOpcion'],
                ['name' => 'Administracion Ver Menu Usuarios', 'subject' => 'MenuOpcion'],
                ['name' => 'Administracion Ver Menu Permisos', 'subject' => 'Permission'],
                ['name' => 'Administracion Ver Menu Roles', 'subject' => 'Role'],
                ['name' => 'Administracion Ver Menu Listado Usuarios', 'subject' => 'User'],
            ],

            // ========================================
            // MÓDULO: SECCIÓN DEV (solo SuperAdmin)
            // ========================================
            'dev' => [
                ['name' => 'Ver Menu Seccion Dev', 'subject' => 'DevHerramienta'],
                ['name' => 'ver logs sistema', 'subject' => 'DevHerramienta'],
                ['name' => 'ver colas sistema', 'subject' => 'DevHerramienta'],
                ['name' => 'ver info sistema', 'subject' => 'DevHerramienta'],
                ['name' => 'limpiar logs sistema', 'subject' => 'DevHerramienta'],
                ['name' => 'limpiar colas sistema', 'subject' => 'DevHerramienta'],
            ],

            // ========================================
            // MÓDULO: EXTRAS
            // ========================================
            'extras' => [
                ['name' => 'ver favoritos', 'subject' => 'favoritos'],
                ['name' => 'crear favoritos', 'subject' => 'favoritos'],
                ['name' => 'editar favoritos', 'subject' => 'favoritos'],
                ['name' => 'eliminar favoritos', 'subject' => 'favoritos'],
                ['name' => 'Configuración Ver User Preferencias', 'subject' => 'UserPreferencias'],
                ['name' => 'MenuPerfil Ver Opcion Perfil', 'subject' => 'MenuPerfil'],
                ['name' => 'Ver Notificaciones', 'subject' => 'Notificacion'],
            ],
        ];
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $todosLosPermisos = self::getPermisos();

        foreach ($todosLosPermisos as $modulo => $permisos) {
            foreach ($permisos as $permiso) {
                Permission::firstOrCreate(
                    ['name' => $permiso['name'], 'guard_name' => 'api'],
                    ['subject' => $permiso['subject']]
                );
            }
        }
    }
}
