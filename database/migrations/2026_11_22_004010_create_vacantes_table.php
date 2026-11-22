<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vacantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unidad_id')->index()->constrained('unidades');
            $table->string('titulo', 150);
            $table->text('descripcion');
            $table->foreignId('modalidad_id')->index()->constrained('modalidades');
            $table->decimal('salario_minimo', 10, 2);
            $table->decimal('salario_maximo', 10, 2);
            $table->string('ubicacion', 150);
            $table->foreignId('estado_vacante_id')->index()->constrained('estados_vacante');
            $table->foreignId('user_id')->index()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacantes');
    }
};
