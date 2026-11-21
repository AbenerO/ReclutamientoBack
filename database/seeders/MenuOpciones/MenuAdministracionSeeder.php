<?php

namespace Database\Seeders\MenuOpciones;

use App\Models\MenuOpcion;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class MenuAdministracionSeeder extends Seeder
{
    public function run(): void
    {
        // Migración: si 'Menú' existe bajo 'Módulo Usuarios', eliminarlo para recrearlo en el lugar correcto
        $oldParent = MenuOpcion::where('titulo', 'Módulo Usuarios')->first();
        if ($oldParent) {
            MenuOpcion::where('titulo', 'Menú')
                ->where('ruta', 'admin-menu')
                ->where('parent_id', $oldParent->id)
                ->delete();
        }

        // =============================================
        // SECCIÓN: Administración del Sistema
        // =============================================
        $this->crearMenuConPermiso(
            ['titulo_seccion' => 'Administración'],
            [
                'titulo' => null,
                'icono' => null,
                'ruta' => null,
                'action' => 'Ver Menu Administración',
                'subject' => 'MenuOpcion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $moduloUsuarios = $this->crearMenuConPermiso(
            ['titulo' => 'Módulo Usuarios'],
            [
                'titulo_seccion' => null,
                'ruta' => null,
                'icono' => 'ri-user-line',
                'action' => 'Administracion Ver Menu Usuarios',
                'subject' => 'MenuOpcion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Usuarios', 'parent_id' => $moduloUsuarios->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'admin-usuarios',
                'icono' => 'ri-user-add-line',
                'action' => 'Administracion Ver Menu Listado Usuarios',
                'subject' => 'User',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Roles', 'parent_id' => $moduloUsuarios->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'roles',
                'icono' => 'ri-user-3-line',
                'action' => 'Administracion Ver Menu Roles',
                'subject' => 'Role',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Permisos', 'parent_id' => $moduloUsuarios->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'permisos',
                'icono' => 'ri-lock-line',
                'action' => 'Administracion Ver Menu Permisos',
                'subject' => 'Permission',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        // =============================================
        // SECCIÓN: Configuración del Sistema
        // =============================================
        $this->crearMenuConPermiso(
            ['titulo_seccion' => 'Configuración'],
            [
                'titulo' => null,
                'icono' => null,
                'ruta' => null,
                'action' => 'ver menu',
                'subject' => 'MenuOpcion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $moduloSistema = $this->crearMenuConPermiso(
            ['titulo' => 'Sistema'],
            [
                'titulo_seccion' => null,
                'ruta' => null,
                'icono' => 'ri-settings-4-line',
                'action' => 'ver menu',
                'subject' => 'MenuOpcion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Menú', 'parent_id' => $moduloSistema->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'admin-menu',
                'icono' => 'ri-layout-left-line',
                'action' => 'ver menu',
                'subject' => 'MenuOpcion',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        // Notificaciones (bitácora + envío de prueba)
        $notificaciones = $this->crearMenuConPermiso(
            ['titulo' => 'Notificaciones'],
            [
                'titulo_seccion' => null,
                'ruta' => null,
                'icono' => 'ri-notification-3-line',
                'action' => 'ver notificaciones',
                'subject' => 'Notificacion',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Enviar Prueba', 'parent_id' => $notificaciones->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'configuracion-notificaciones-prueba',
                'icono' => 'ri-send-plane-line',
                'action' => 'enviar notificaciones',
                'subject' => 'Notificacion',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Bitácora de Mensajes', 'parent_id' => $notificaciones->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'configuracion-notificaciones',
                'icono' => 'ri-history-line',
                'action' => 'ver notificaciones',
                'subject' => 'Notificacion',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        // =============================================
        // SECCIÓN: Sección Dev (solo SUPER_ADMINISTRADOR)
        // =============================================
        $this->crearMenuConPermiso(
            ['titulo_seccion' => 'Sección Dev'],
            [
                'titulo' => null,
                'icono' => null,
                'ruta' => null,
                'action' => 'Ver Menu Seccion Dev',
                'subject' => 'DevHerramienta',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $dev = $this->crearMenuConPermiso(
            ['titulo' => 'Herramientas Dev'],
            [
                'titulo_seccion' => null,
                'ruta' => null,
                'icono' => 'ri-terminal-box-line',
                'action' => 'Ver Menu Seccion Dev',
                'subject' => 'DevHerramienta',
                'parent_id' => null,
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Logs', 'parent_id' => $dev->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'dev-logs',
                'icono' => 'ri-file-list-line',
                'action' => 'ver logs sistema',
                'subject' => 'DevHerramienta',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Colas', 'parent_id' => $dev->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'dev-colas',
                'icono' => 'ri-stack-line',
                'action' => 'ver colas sistema',
                'subject' => 'DevHerramienta',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Info Sistema', 'parent_id' => $dev->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'dev-info',
                'icono' => 'ri-information-line',
                'action' => 'ver info sistema',
                'subject' => 'DevHerramienta',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );

        $this->crearMenuConPermiso(
            ['titulo' => 'Redis', 'parent_id' => $dev->id],
            [
                'titulo_seccion' => null,
                'ruta' => 'dev-redis',
                'icono' => 'ri-database-2-line',
                'action' => 'ver info sistema',
                'subject' => 'DevHerramienta',
                'orden' => MenuOpcion::max('orden') + 1,
            ]
        );
    }

    private function crearMenuConPermiso(array $uniqueAttributes, array $values): MenuOpcion
    {
        $menuOpcion = MenuOpcion::updateOrCreate($uniqueAttributes, $values);

        $action  = $values['action']  ?? $uniqueAttributes['action']  ?? null;
        $subject = $values['subject'] ?? $uniqueAttributes['subject'] ?? null;

        if ($action && $subject) {
            Permission::firstOrCreate(
                ['name' => $action],
                ['subject' => $subject, 'guard_name' => 'api']
            );
        }

        return $menuOpcion;
    }
}
