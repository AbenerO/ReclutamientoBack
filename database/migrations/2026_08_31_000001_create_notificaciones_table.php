<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->enum('canal', ['email', 'whatsapp']);
            $table->string('destinatario');                 // correo o teléfono
            $table->string('asunto')->nullable();           // solo email
            $table->text('cuerpo');
            $table->enum('estado', ['pendiente', 'enviado', 'fallido', 'entregado', 'leido'])->default('pendiente');
            $table->text('error')->nullable();
            $table->string('origen', 50)->default('manual');   // prueba, manual, u otro módulo que envíe
            $table->nullableMorphs('notificable');          // registro de origen (opcional)
            $table->string('wamid')->nullable();            // id de Meta, para cruzar estados del webhook
            $table->timestamp('enviado_en')->nullable();
            $table->timestamps();

            $table->index(['canal', 'estado']);
            $table->index('origen');
            $table->index('wamid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
