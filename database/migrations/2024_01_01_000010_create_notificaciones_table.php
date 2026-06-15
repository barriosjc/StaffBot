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
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('solicitud_id')
                ->nullable()
                ->constrained('solicitudes_coberturas')
                ->nullOnDelete();
            $table->enum('tipo', ['recordatorio', 'alerta', 'confirmacion', 'sistema']);
            $table->text('mensaje');
            $table->enum('canal', ['whatsapp', 'sistema'])->default('whatsapp');
            $table->boolean('enviada')->default(false);
            $table->timestamp('enviada_at')->nullable();
            $table->timestamps();

            $table->index('user_id', 'idx_notif_usuario');
            $table->index('solicitud_id', 'idx_notif_solicitud');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
