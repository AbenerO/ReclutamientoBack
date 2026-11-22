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
        Schema::create('postulaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidato_id')->constrained('candidatos');
            $table->foreignId('vacante_id')->index()->constrained('vacantes');
            $table->date('fecha_postulacion');
            $table->foreignId('estado_postulacion_id')->index()->constrained('estados_postulacion');
            $table->foreignId('fuente_reclutamiento_id')->index()->constrained('fuentes_reclutamiento');
            $table->foreignId('user_id')->index()->constrained('users');
            $table->timestamps();

            $table->unique(['candidato_id', 'vacante_id'], 'uq_postulaciones_candidato_vacante');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulaciones');
    }
};
