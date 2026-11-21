<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Identificador de acceso
            $table->string('username', 50)->unique()->nullable()->after('avatar');

            // Nombres
            $table->string('primer_nombre', 100)->nullable()->after('username');
            $table->string('segundo_nombre', 100)->nullable()->after('primer_nombre');
            $table->string('tercer_nombre', 100)->nullable()->after('segundo_nombre');

            // Apellidos
            $table->string('primer_apellido', 100)->nullable()->after('tercer_nombre');
            $table->string('segundo_apellido', 100)->nullable()->after('primer_apellido');
            $table->string('apellido_casada', 100)->nullable()->after('segundo_apellido');

            // Datos personales
            $table->string('dpi', 13)->unique()->nullable()->after('apellido_casada');
            $table->enum('genero', ['Masculino', 'Femenino', 'Otro'])->nullable()->after('dpi');
            $table->date('fecha_nacimiento')->nullable()->after('genero');
            $table->string('telefono', 15)->nullable()->after('fecha_nacimiento');

            // Estado del usuario en el sistema
            $table->boolean('estado')->default(true)->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropUnique(['dpi']);
            $table->dropColumn([
                'username',
                'primer_nombre', 'segundo_nombre', 'tercer_nombre',
                'primer_apellido', 'segundo_apellido', 'apellido_casada',
                'dpi', 'genero', 'fecha_nacimiento', 'telefono',
                'estado',
            ]);
        });
    }
};
