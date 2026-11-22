<?php

namespace Database\Seeders\AdministracionSistema;

use Database\Seeders\AdministracionSistema\Roles\AdministradorSeeder;
use Database\Seeders\AdministracionSistema\Roles\ReclutadorSeeder;
use Database\Seeders\AdministracionSistema\Roles\SuperAdministradorSeeder;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Crea los roles del sistema y asigna los permisos correspondientes.
     * Cada rol tiene su propio seeder con sus permisos específicos.
     */
    public function run(): void
    {
        // Primero crear todos los permisos
        $this->call(PermisosSeeder::class);

        $this->call([
            SuperAdministradorSeeder::class,
            AdministradorSeeder::class,
            ReclutadorSeeder::class,
        ]);
    }
}
