<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitud_destinatarios', function (Blueprint $table) {
            $table->enum('estado', [
                'pendiente', 'no_respondio', 'aceptada', 'rechazada', 'expirada', 'cancelado'
            ])->default('pendiente')->change();
        });
    }

    public function down(): void
    {
        Schema::table('solicitud_destinatarios', function (Blueprint $table) {
            $table->enum('estado', [
                'pendiente', 'no_respondio', 'aceptada', 'rechazada', 'expirada'
            ])->default('pendiente')->change();
        });
    }
};
