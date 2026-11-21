<?php

namespace Database\Seeders\AdministracionSistema;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'            => 'Administrador Sistema',
                'username'        => 'admin',
                'primer_nombre'   => 'Administrador',
                'primer_apellido' => 'Sistema',
                'password'        => bcrypt('12345'),
                'estado'          => true,
            ]
        );
    }
}
