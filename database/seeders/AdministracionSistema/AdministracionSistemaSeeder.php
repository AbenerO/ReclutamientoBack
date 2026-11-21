<?php

namespace Database\Seeders\AdministracionSistema;

use Illuminate\Database\Seeder;

class AdministracionSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RolesTableSeeder::class,
        ]);
    }
}
