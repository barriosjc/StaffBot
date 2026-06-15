<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes_coberturas', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada', 'cancelado'])
                  ->default('pendiente')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes_coberturas', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])
                  ->default('pendiente')
                  ->change();
        });
    }
};
