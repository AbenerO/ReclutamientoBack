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
        Schema::create('entrevistas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulacion_id')->index()->constrained('postulaciones');
            $table->dateTime('fecha_hora');
            $table->foreignId('tipo_entrevista_id')->index()->constrained('tipos_entrevista');
            $table->foreignId('entrevistador_id')->index()->constrained('users');
            $table->foreignId('resultado_entrevista_id')->index()->constrained('resultados_entrevista');
            $table->string('observaciones', 500)->nullable();
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
        Schema::dropIfExists('entrevistas');
    }
};
