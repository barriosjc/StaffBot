<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones_whatsapp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('chat_id', 50)->unique()->comment('ID de chat de wwebjs ej: 5491155555555@c.us');
            $table->enum('estado', ['activa', 'expirada'])->default('activa');
            $table->string('ultimo_menu', 100)->nullable()->comment('Último estado del menú conversacional');
            $table->json('contexto')->nullable()->comment('Datos temporales del flujo activo');
            $table->timestamp('actualizada_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_whatsapp');
    }
};
