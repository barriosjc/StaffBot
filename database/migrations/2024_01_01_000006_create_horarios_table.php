<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('centro_id')->constrained('centros')->cascadeOnDelete();
            $table->foreignId('especialidad_id')->constrained('especialidades')->cascadeOnDelete();
            $table->enum('dia_semana', [
                'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'
            ]);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['centro_id', 'especialidad_id'], 'idx_horarios_centro_esp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
