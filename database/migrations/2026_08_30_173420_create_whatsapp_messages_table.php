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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('wamid')->unique();          // ID de Meta, evita duplicados
            $table->enum('direction', ['in', 'out']);
            $table->string('phone', 20);                 // wa_id del paciente
            $table->string('contact_name')->nullable();
            $table->string('type', 30)->default('text'); // text, image, button, etc.
            $table->text('body')->nullable();
            $table->string('status', 20)->nullable();    // sent, delivered, read, failed
            $table->json('payload')->nullable();         // el evento crudo, para depurar
            $table->timestamp('event_at')->nullable();
            $table->timestamps();

            $table->index(['phone', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
