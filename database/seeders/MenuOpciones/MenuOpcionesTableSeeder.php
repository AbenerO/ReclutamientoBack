<?php

namespace Database\Seeders\MenuOpciones;

use App\Models\MenuOpcion;
use Illuminate\Database\Seeder;

class MenuOpcionesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MenuOpcion::query()->update(['orden' => 0]);
        $this->call([
            MenuInicioSeeder::class,
            MenuAdministracionSeeder::class,
        ]);
    }
}
