<?php

namespace Database\Seeders;

use Database\Seeders\AdministracionSistema\AdministracionSistemaSeeder;
use Database\Seeders\MenuOpciones\MenuOpcionesTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Menú primero: crea los permisos de navegación que luego se asignan a los roles.
        $this->call([
            MenuOpcionesTableSeeder::class,
            AdministracionSistemaSeeder::class,
            DepartamentosMunicipiosSeeder::class,
        ]);
    }
}
