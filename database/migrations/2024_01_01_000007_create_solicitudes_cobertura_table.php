<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_coberturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centro_id')->constrained('centros')->restrictOnDelete();
            $table->foreignId('especialidad_id')->constrained('especialidades')->restrictOnDelete();
            $table->foreignId('horario_id')->constrained('horarios')->restrictOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->restrictOnDelete();
            $table->date('fecha_inicio');
            $table->time('hora_inicio');
            $table->date('fecha_fin')->comment('Puede diferir de fecha_inicio en guardias nocturnas');
            $table->time('hora_fin');
            $table->enum('modo_envio', ['manual_uno', 'secuencial', 'broadcast'])->default('manual_uno');
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');
            $table->timestamps();

            $table->index('centro_id', 'idx_solicitud_centro');
            $table->index('supervisor_id', 'idx_solicitud_supervisor');
            $table->index('estado', 'idx_solicitud_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_coberturas');
    }
};
