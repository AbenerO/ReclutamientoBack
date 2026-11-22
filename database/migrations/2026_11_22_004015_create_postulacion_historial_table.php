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
        Schema::create('postulacion_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulacion_id')->index()->constrained('postulaciones');
            $table->foreignId('estado_anterior_id')->nullable()->index()->constrained('estados_postulacion');
            $table->foreignId('estado_nuevo_id')->index()->constrained('estados_postulacion');
            $table->foreignId('user_id')->index()->constrained('users');
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postulacion_historial');
    }
};
