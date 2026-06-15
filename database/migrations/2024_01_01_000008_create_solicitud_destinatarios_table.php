<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_destinatarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes_coberturas')->cascadeOnDelete();
            $table->foreignId('empleado_id')->constrained('users')->restrictOnDelete();
            $table->unsignedTinyInteger('orden')->default(1)->comment('Para modo secuencial');
            $table->enum('estado', [
                'pendiente', 'no_respondio', 'aceptada', 'rechazada', 'expirada'
            ])->default('pendiente');
            $table->timestamp('notificado_at')->nullable();
            $table->timestamp('respondido_at')->nullable();
            $table->timestamps();

            $table->index('solicitud_id', 'idx_destinatario_solicitud');
            $table->index('empleado_id', 'idx_destinatario_empleado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_destinatarios');
    }
};
